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
    {
//        if ($conf['plugin']['issuetracker']['userinfo_email']==1)
        {   global $ID;
            global $conf;
            $subject='Issue '.$issue['id'].' on '.$project.' was modified';            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            
            $body = 'Dear user,'.chr(10).chr(10).'Your reported issue was modified.'.chr(10).chr(13).
            'ID:'.chr(9).chr(9).chr(9).chr(9).$issue['id'].chr(10).
            $column.' (old): '.$old_value.chr(10).
            $column.' (new): '.$new_value.chr(10).
            'see details:'.chr(9).chr(9).DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
            'best regards'.chr(10).$project.' Issue Tracker'.chr(10).chr(10);


            $from= $conf['plugin']['issuetracker']['email_address'];
            $to=$issue['user_mail'];
            $cc=$issue['add_user_mail'];
            $from='taggic@t-online.de';
            $to=$from;

            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);

        }
    }
/******************************************************************************/
    global $ID;    
    $exploded = explode(' ',htmlspecialchars(stripslashes($_POST['id'])));
    $project = $exploded[0];
    $id_issue = intval($exploded[1]);
    
    // get issues file contents
    $pfile = metaFN($project, '.issues');
    if (@file_exists($pfile))
        {$issues  = unserialize(@file_get_contents($pfile));}
    else 
        {$issues = array();}
    
    
    $field = strtolower(htmlspecialchars(stripslashes($_POST['field'])));
    $value = htmlspecialchars(stripslashes($_POST['value']));
    
        _emailForIssueMod($project, $issues[$id_issue], $issues[$id_issue][$field], $field, $value);
        $issues[$id_issue][$field] = $value;
        $issues[$id_issue]['modified'] = date ('Y-m-d G:i:s');
    
    // Save issues file contents
    $fh = fopen($pfile, 'w');
    fwrite($fh, serialize($issues));
    fclose($fh);
    echo $_POST['value'];    
?>
