<?php

  require_once(realpath(dirname(__FILE__)).'/../../../inc/init.php');
  if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
  require_once(DOKU_PLUGIN.'syntax.php');  
  
// POST Sent by the edited array
//    * &row=n: The row index of the edited cell
//    * &cell=n: The cell index of the edited cell
//    * &id=id: The id attribute of the row, it may be useful to set this to the record ID you are editing
//    * &field=field: The id attribute of the header cell of the column of the edited cell, it may be useful to set this to the field name you are editing
//    * &value=xxxxxx: The rest of the POST body is the serialised form. The default name of the field is 'value'.

 
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                                          
    function _emailForIssueMod($project, $issue, $old_value, $column, $new_value)
    {     global $conf;
//        if ($conf['plugin']['issuetracker']['userinfo_email']==1)
        {   global $ID;
        
            // Include the language file  
            if ($conf['lang']=='') $conf['lang']=='en'; 
            include 'lang/'.$conf['lang'].'/lang.php';
            
            $subject= utf8_encode (sprintf($lang['issuemod_subject'], $issue['id'], $project));
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));

            $body = $lang['issuemod_head'].chr(10).chr(10).
                    $lang['issuemod_intro'].chr(10).
                    $lang['issuemod_issueid'].$issue['id'].chr(10).
                    $lang['issuemod_product'].$issue['product'].chr(10).
                    $lang['issuemod_version'].$issue['version'].chr(10).
                    $lang['issuemod_severity'].$issue['severity'].chr(10).
                    $lang['issuemod_status'].$issue['status'].chr(10).
                    $lang['issuemod_creator'].$issue['user_name'].chr(10).
                    $lang['issuemod_title'].$issue['title'].chr(10).
                    $lang['issuenew_descr'].$issue['description'].chr(10).
                    $lang['issuemod_see'].DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $lang['issuemod_br'].chr(10).$lang['issuemod_end'];
            
            $from = $conf['plugin']['issuetracker']['email_address'];
            $to   = $issue['user_mail'];
            $cc   = $issue['add_user_mail'];
            
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n"; 
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers, $params=null);
        }
    }
/******************************************************************************/
/* send an e-mail to assignee about routed service request
*/                                          
    function _emailToAssigneeMod($project,$issue,$value)
    {       
            global $ID;
            global $conf;
        
            // Include the language file
            if ($conf['lang']=='') $conf['lang']=='en'; 
            include 'lang/'.$conf['lang'].'/lang.php';
            
            
            $subject= utf8_encode ($project.sprintf($lang['issueassigned_subject'],$issue['id']));
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));

            $body = $lang['issueassigned_head'].chr(10).chr(10).
                    $lang['issueassigned_intro'].chr(10).
                    $lang['issuemod_issueid'].$issue['id'].chr(10).
                    $lang['issuemod_product'].$issue['product'].chr(10).
                    $lang['issuemod_version'].$issue['version'].chr(10).
                    $lang['issuemod_severity'].$issue['severity'].chr(10).
                    $lang['issuemod_creator'].$issue['user_name'].chr(10).
                    $lang['issuemod_title'].$issue['title'].chr(10).
                    $lang['issuenew_descr'].$issue['description'].chr(10).
                    $lang['issuemod_see'].DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $lang['issuemod_br'].chr(10).$lang['issuemod_end'];
                        
            $from = $conf['plugin']['issuetracker']['email_address'];
            $to   = $value;

            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers, $params=null);


    }
/******************************************************************************/
/* log issue modificaions
 * who changed what and when per issue
*/                                          
    function _log_mods($project, $issue, $usr, $column, $new_value)
    {     global $conf;
          // get mod-log file contents
          $modfile = metaFN($project.'_'.$issue['id'], '.mod-log');
          if (@file_exists($modfile))
              {$mods  = unserialize(@file_get_contents($modfile));}
          else 
              {$mods = array();}
          
          
          $mod_id = count($mods);
          if($new_value=='') $new_value = '[deleted]';
          $mods[$mod_id]['timestamp']   = $issue['modified'];
          $mods[$mod_id]['user']        = $usr;
          $mods[$mod_id]['field']       = $column;
          $mods[$mod_id]['new_value']   = $new_value;
          
          // Save issues file contents
          $fh = fopen($modfile, 'w');
          fwrite($fh, serialize($mods));
          fclose($fh);
    }
/******************************************************************************/
/* improved implode needed
*/
    function array_implode($arrays, &$target = array()) 
    {         
         foreach ($arrays as $item) {
             if (is_array($item)) {
                 $this->array_implode($item, $target);
             } else {
                 $target[] = $item;
             }
         }
         return $target;
    }
/******************************************************************************/
    global $ID;
    global $conf;
        
    $exploded = explode(' ',htmlspecialchars(stripslashes($_POST['id'])));
    $project = $exploded[0];
    $id_issue = intval($exploded[1]);
    $usr = $_POST['usr']; 
    // get issues file contents
    $pfile = metaFN($project, '.issues');
    if (@file_exists($pfile))
        {$issues  = unserialize(@file_get_contents($pfile));}
    else 
        {$issues = array();}
    
    
    $field = strtolower(htmlspecialchars(stripslashes($_POST['field'])));
    $value = htmlspecialchars(stripslashes($_POST['value']));
   
   // do not send notification mail if status set to deleted
    if($conf['status_special']=='') $conf['status_special']='Deleted';
    if (($field == 'status') && (stripos($conf['status_special'],$value) !== false)) {
      // for now do nothing, maybe later implementation
      // will define further action on issues marked as 'deleted'
    }
    else
    _emailForIssueMod($project, $issues[$id_issue], $issues[$id_issue][$field], $field, $value);
    
    $issues[$id_issue][$field] = $value;
    $issues[$id_issue]['modified'] = date ('Y-m-d G:i:s');
    _log_mods($project, $issues[$id_issue], $usr, $field, $value);
    
        // inform assigned workforce
    if ($field == 'assigned') {
        $issues[$id_issue]['assigned'] = $_POST['value'];
        $status = explode(',', $conf['plugin']['issuetracker']['status']);
        // No custom configuration in the event $conf does not contain issuetracker status field
        // not the best idea to fall back to hard coded defaults but fast implemented
        if($status[0]=='') $status[0]='New';
        if($status[1]=='') $status[1]='Assigned';
        
        if($issues[$id_issue]['assigned']=='')
        { // assignment deleted => set status to first config value
          
          $issues[$id_issue]['status'] = $status[0];
        }
        else $issues[$id_issue]['status'] = $status[1];
        
        _emailToAssigneeMod($project, $issues[$id_issue], $value);
    }
    // Save issues file contents
    $fh = fopen($pfile, 'w');
    fwrite($fh, serialize($issues));
    fclose($fh);
    echo $_POST['value'];    

?>
