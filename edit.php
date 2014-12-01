<?php

  require_once(realpath(dirname(__FILE__)).'/../../../inc/init.php');
  if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
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
    function _emailForIssueMod($currentID, $project, $issue, $old_value, $column, $new_value, $usr)
    {     
//        if ($conf['plugin']['issuetracker']['userinfo_email']==1)
        {   global $ID;
            global $lang;
            global $conf;
            if($new_value == '') $new_value = $lang['it__none'];
            if($old_value == '') $old_value = $lang['it__none'];
                        
            if ($conf['plugin']['issuetracker']['mail_templates']==1) {
                // load user html mail template
                $sFilename = DOKU_PLUGIN.'issuetracker/mailtemplate/edit_issuemod_mail.html';
                $bodyhtml = file_get_contents($sFilename);
                $comment = array();
                $comment["field"] = $column;
                $comment["old_value"] = $old_value;
                $comment["new_value"] = $new_value;
                $comment["timestamp"] = date('Y-m-d G:i:s');
                $comment["author"] = $user_mail['userinfo']['mail'];
            }
            //issuemod_subject = 'Issue #%s on %s: %s';
            $subject = sprintf($lang['issuemod_subject'], $issue['id'], $project, $lang['th_'.$column]);
            $subject = mb_encode_mimeheader($subject, "UTF-8", "Q" );
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            //issuemod_changes = The issue changed on %s from %s to %s.
            $changes = sprintf($lang['issuemod_changes'],$lang['th_'.$column], $old_value, $new_value);

            $body = chr(10).$lang['issuemod_head'].chr(10).chr(10).
                    $lang['issuemod_intro'].chr(10).
                    $changes.chr(10).chr(10).
                    $lang['issuemod_title'].$issue['title'].chr(10).
                    $lang['issuemod_issueid'].$issue['id'].chr(10).
                    $lang['issuemod_product'].$issue['product'].chr(10).
                    $lang['issuemod_version'].$issue['version'].chr(10).
                    $lang['issuemod_severity'].$issue['severity'].chr(10).
                    $lang['issuemod_status'].$issue['status'].chr(10).
                    $lang['issuemod_creator'].$issue['user_name'].chr(10).
                    $lang['th_assigned'].': '.$issue['assigned'].chr(10).                    
                    $lang['issuenew_descr'].$issue['description'].chr(10).
                    $lang['issuemod_see'].DOKU_URL.'doku.php?id='.$currentID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $lang['issuemod_br'].chr(10).$lang['issuemod_end'];
            
            
                $body = html_entity_decode($body);
                $from = $conf['plugin']['issuetracker']['email_address'];
                $to   = $issue['user_mail'];
                $cc   = $issue['add_user_mail'];
                
            if ($conf['plugin']['issuetracker']['mail_templates']==1) { 
                $bodyhtml = replace_bodyhtml($currentID, $bodyhtml, $pstring, $project, $issue, $comment, $usr);              
              $headers .= "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
              mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
            }
        }
    }
/******************************************************************************/
/* send an e-mail to assignee about routed service request
*/                                          
    function _emailToAssigneeMod($currentID, $project, $issue, $value, $usr)
    {       global $lang;
            global $conf;
            if ($conf['plugin']['issuetracker']['mail_templates']==1) {
                // load user html mail template
                $sFilename = DOKU_PLUGIN.'issuetracker/mailtemplate/assignee_mail.html';
                $bodyhtml = file_get_contents($sFilename);
                $comment = array();
                $comment["timestamp"] = date('Y-m-d G:i:s');
                $comment["author"] = $user_mail['userinfo']['mail'];
            }
            $subject = sprintf($lang['issueassigned_subject'], $issue['id'], $project);
            $subject = mb_encode_mimeheader($subject, "UTF-8", "Q" );
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));

            $body = chr(10).$lang['issueassigned_head'].chr(10).chr(10).
                    $lang['issueassigned_intro'].chr(10).
                    $lang['issuemod_title'].$issue['title'].chr(10).
                    $lang['issuemod_issueid'].$issue['id'].chr(10).
                    $lang['issuemod_product'].$issue['product'].chr(10).
                    $lang['issuemod_version'].$issue['version'].chr(10).
                    $lang['issuemod_severity'].$issue['severity'].chr(10).
                    $lang['issuemod_status'].$issue['status'].chr(10).
                    $lang['issuemod_creator'].$issue['user_name'].chr(10).
                    $lang['th_assigned'].$issue['assigned'].chr(10).                    
                    $lang['issuenew_descr'].$issue['description'].chr(10).
                    $lang['issuemod_see'].DOKU_URL.'doku.php?id='.$currentID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $lang['issuemod_br'].chr(10).$lang['issuemod_end'];
            
            $body = html_entity_decode($body);
            if ($conf['plugin']['issuetracker']['mail_templates']==1) $bodyhtml = replace_bodyhtml($currentID, $bodyhtml, $pstring, $project, $issue, $comment, $usr);
            
            $from = $conf['plugin']['issuetracker']['email_address'];
            $to   = $value;

            if ($conf['plugin']['issuetracker']['mail_templates']==1) { 
              $bodyhtml = replace_bodyhtml($currentID, $bodyhtml, $pstring, $project, $issue, $comment, $usr);              
              $headers .= "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
              mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc='', $bcc='', $headers=null, $params=null);
            }
    }
/******************************************************************************
     * HTML Mail functions
     *
     * Sends HTML-formatted mail
     * By Lin Junjie (mail [dot] junjie [at] gmail [dot] com)
     *
******************************************************************************/
    function mail_send_html($to, $subject, $body, $bodyhtml, $from='', $cc='', $bcc='', $header='', $params=null){
      if(defined('MAILHEADER_ASCIIONLY')){
        $subject = utf8_deaccent($subject);
        $subject = utf8_strip($subject);
      }
      if(!defined('MAILHEADER_EOL')) define('MAILHEADER_EOL',"\n");
      if(!utf8_isASCII($subject)) {
        $subject = '=?UTF-8?Q?'.mail_quotedprintable_encode($subject,0).'?=';
        // Spaces must be encoded according to rfc2047. Use the "_" shorthand
        $subject = preg_replace('/ /', '_', $subject);
      }
     
      $header  = '';
     
      $usenames = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? false : true;
     
      $random_hash = md5(date('r', time())); // added
     
      $to = mail_encode_address($to,'',$usenames);
      $header .= mail_encode_address($from,'From');
      $header .= mail_encode_address($cc,'Cc');
      $header .= mail_encode_address($bcc,'Bcc');
      $header .= 'MIME-Version: 1.0'.MAILHEADER_EOL;
      $header .= "Content-Type: multipart/alternative; boundary=PHP-alt-".$random_hash.MAILHEADER_EOL;
      $header  = trim($header);
     

      $body = mail_quotedprintable_encode($body,0);
      $bodyhtml = mail_quotedprintable_encode($bodyhtml,0);

      $message =	"--PHP-alt-".$random_hash."\r\n".
    				"Content-Type: text/plain; charset=UTF-8"."\n".
    				"Content-Transfer-Encoding: quoted-printable"."\n\n".
    				$body."\n\n".
    				"--PHP-alt-".$random_hash."\r\n".
    				"Content-Type: text/html; charset=UTF-8"."\n".
    				"Content-Transfer-Encoding: quoted-printable"."\n\n".
    				$bodyhtml."\n".
    				"--PHP-alt-".$random_hash."--";
     
      if($params == null){
        return @mail($to,$subject,$message,$header);
      }else{
        return @mail($to,$subject,$message,$header,$params);
      }
    }

/******************************************************************************/
    function replace_bodyhtml($currentID, $bodyhtml, $pstring, $project, $issue, $comment, $usr) {
        global $ID;
        global $lang;
        
        $bodyhtml = str_ireplace("%%_see%%",DOKU_URL.'doku.php?id='.$currentID.'&do=showcaselink&'.$pstring,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_head%%",$lang['issuemod_head'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_intro%%",$lang['issuemod_intro'],$bodyhtml);

        $bodyhtml = str_ireplace("%%issuemod_issueid%%",$lang['issuemod_issueid'],$bodyhtml);
        $bodyhtml = str_ireplace("%%ID%%",$issue['id'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_title%%",$lang['issuemod_title'],$bodyhtml);
        $bodyhtml = str_ireplace("%%TITEL%%",$issue['title'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_status%%",$lang['issuemod_status'],$bodyhtml);
        $bodyhtml = str_ireplace("%%status%%",$issue['status'],$bodyhtml);
        $bodyhtml = str_ireplace("%%th_project%%",$lang['th_project'],$bodyhtml);
        $bodyhtml = str_ireplace("%%project%%",$project,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_product%%",$lang['issuemod_product'],$bodyhtml);
        $bodyhtml = str_ireplace("%%product%%",$issue['product'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_version%%",$lang['issuemod_version'],$bodyhtml);
        $bodyhtml = str_ireplace("%%version%%",$issue['version'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_severity%%",$lang['issuemod_severity'],$bodyhtml);
        $bodyhtml = str_ireplace("%%severity%%",$issue['severity'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_creator%%",$lang['issuemod_creator'],$bodyhtml);
        $bodyhtml = str_ireplace("%%creator%%",$issue['user_name'],$bodyhtml);
        $bodyhtml = str_ireplace("%%CREATOR_MAIL%%",$issue['user_mail'],$bodyhtml);
        $bodyhtml = str_ireplace("%%th_assigned%%",$lang['th_assigned'],$bodyhtml);
        $bodyhtml = str_ireplace("%%assigned%%",$issue['assigned'],$bodyhtml);
        $bodyhtml = str_ireplace("%%th_created%%",$lang['th_created'],$bodyhtml);
        $bodyhtml = str_ireplace("%%created%%",$issue['created'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issueassigned_head%%",$lang['issueassigned_head'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issueassigned_intro%%",$lang['issueassigned_intro'],$bodyhtml);

        $bodyhtml = str_ireplace("%%issue_resolved_intro%%",$lang['issue_resolved_intro'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issue_resolved_text%%",$lang['issue_resolved_text'],$bodyhtml);
        $frmt_res = str_ireplace(chr(10),"<br />",$issue['resolution']);
        $bodyhtml = str_ireplace("%%resolution%%",xs_format($frmt_res),$bodyhtml);
        $bodyhtml = str_ireplace("%%timestamp%%",date($conf['plugin']['issuetracker']['d_format']),$bodyhtml);
        
        $bodyhtml = str_ireplace("%%resolver%%",$usr,$bodyhtml);
        $bodyhtml = str_ireplace("%%mod_by%%",$usr,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuedescrmod_subject%%",sprintf($lang['issuedescrmod_subject'],$issue['id'], $project),$bodyhtml);
        $bodyhtml = str_ireplace("%%th_description%%",$lang['th_description'],$bodyhtml);
        $frmt_descr = str_ireplace(chr(10),"<br />",$issue['description']);
        $bodyhtml = str_ireplace("%%description%%",xs_format($frmt_descr),$bodyhtml);
        
                        
//        if($comment) {
            $bodyhtml = str_ireplace("%%lbl_cmts_wlog%%",$lang['lbl_cmts_wlog'],$bodyhtml);
            $bodyhtml = str_ireplace("%%cmnt_id%%",$comment['id'],$bodyhtml);
            $bodyhtml = str_ireplace("%%edit_author%%",$comment['author'],$bodyhtml);
            $bodyhtml = str_ireplace("%%cmnt_timestamp%%",date($conf['plugin']['issuetracker']['d_format'],strtotime($comment['timestamp'])),$bodyhtml);
            $frmt_cmnt = str_ireplace(chr(10),"<br />",$comment['comment']);
            $bodyhtml = str_ireplace("%%comment%%",xs_format($frmt_cmnt),$bodyhtml);
            $bodyhtml = str_ireplace("%%field%%",str_ireplace(chr(10),"<br />",$comment["field"]),$bodyhtml);
            $bodyhtml = str_ireplace("%%old_value%%",xs_format(str_ireplace(chr(10),"<br />",$comment["old_value"])),$bodyhtml);
            $bodyhtml = str_ireplace("%%new_value%%",xs_format(str_ireplace(chr(10),"<br />",$comment["new_value"])),$bodyhtml);
//        }
        $bodyhtml = str_ireplace("%%issuemod_br%%",$lang['issuemod_br'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_end%%",$lang['issuemod_end'],$bodyhtml);
        
        return $bodyhtml;
    }
/******************************************************************************/
/* replace simple formats used by editor buttons
*/
    function xs_format($x_comment)
    { // bold , italic, underline, etc.
        $x_comment = preg_replace('/\[([bius])\]/i', '<\\1>', $x_comment);
        $x_comment = preg_replace('/\[\/([bius])\]/i', '</\\1>', $x_comment);

        $x_comment = preg_replace('/\[ol\]/i', '<ol>', $x_comment);
        $x_comment = preg_replace('/\[\/ol\]/i', '</ol>', $x_comment);    

        $x_comment = preg_replace('/\[ul\]/i', '<ul>', $x_comment);
        $x_comment = preg_replace('/\[\/ul\]/i', '</ul>', $x_comment);    

        $x_comment = preg_replace('/\[li\]/i', '<li>', $x_comment);
        $x_comment = preg_replace('/\[\/li\]/i', '</li>', $x_comment);    

        $x_comment = preg_replace('/\[sup\]/i', '<sup>', $x_comment);
        $x_comment = preg_replace('/\[\/sup\]/i', '</sup>', $x_comment);    

        $x_comment = preg_replace('/\[sub\]/i', '<sub>', $x_comment);
        $x_comment = preg_replace('/\[\/sub\]/i', '</sub>', $x_comment);    

        $x_comment = preg_replace('/\[hr\]/i', '<hr>', $x_comment);

        $x_comment = preg_replace('/\[blockquote\]/i', '<blockquote>', $x_comment);
        $x_comment = preg_replace('/\[\/blockquote\]/i', '</blockquote>', $x_comment);    

        $x_comment = preg_replace('/\[code\]/i', '<code>', $x_comment);
        $x_comment = preg_replace('/\[\/code\]/i', '</code>', $x_comment);    

        $x_comment = preg_replace('/\[red\]/i', '<span style="color:red;">', $x_comment);
        $x_comment = preg_replace('/\[\/red\]/i', '</span>', $x_comment);    

        $x_comment = preg_replace('/\[grn\]/i', '<span style="color:green;">', $x_comment);
        $x_comment = preg_replace('/\[\/grn\]/i', '</span>', $x_comment);    

        $x_comment = preg_replace('/\[bgy\]/i', '<span style="background:yellow;">', $x_comment);
        $x_comment = preg_replace('/\[\/bgy\]/i', '</span>', $x_comment);    

        $x_comment = preg_replace('/\[blu\]/i', '<span style="color:blue;">', $x_comment);
        $x_comment = preg_replace('/\[\/blu\]/i', '</span>', $x_comment);    

        $urlsuch[]="/([^]_a-z0-9-=\"'\/])((https?|ftp):\/\/|www\.)([^ \r\n\(\)\^\$!`\"'\|\[\]\{\}<>]*)/si";
        $urlsuch[]="/^((https?|ftp):\/\/|www\.)([^ \r\n\(\)\^\$!`\"'\|\[\]\{\}<>]*)/si";
        $urlreplace[]="\\1[link]\\2\\4[/link]";
        $urlreplace[]="[link]\\1\\3[/link]";
        $x_comment = preg_replace($urlsuch, $urlreplace, $x_comment);   
        $x_comment = preg_replace("/\[link\]www.(.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"http://www.\\1\">www.\\1</a>", $x_comment); 
        $x_comment = preg_replace("/\[link=www.(.*?)\](.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"http://www.\\1\">\\2</a>", $x_comment); 
        $x_comment = preg_replace("/\[link\](\:.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"doku.php?id=\\1\">\\1</a>", $x_comment); 
        $x_comment = preg_replace("/\[link=(\:.*?)\]\[\/link\]/si", "<a target=\"_blank\" href=\"doku.php?id=\\1\">\\1</a>", $x_comment); 
        $x_comment = preg_replace("/\[link=(\:.*?)\](.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"doku.php?id=\\1\">\\2</a>", $x_comment); 
        $x_comment = preg_replace("/\[link\](.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"\\1\">\\1</a>", $x_comment); 
        $x_comment = preg_replace("/\[link=(.*?)\](.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"\\1\">\\2</a>", $x_comment); 

        $x_comment = preg_replace("/\[img\](http.*?)\[\/img\]/si", "<img src=\"\\1\"title=\"\\1\" alt=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(http.*?)\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\2\" alt=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img\](file.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\1\" alt=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(file.*?)\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\2\" alt=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img\](\:.*?)\[\/img\]/si", "<img src=\"". DOKU_URL . "lib/exe/fetch.php?media=\\1\" title=\"\\1\" alt=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(\:.*?)\](.*?)\[\/img\]/si", "<img src=\"". DOKU_URL . "lib/exe/fetch.php?media=\\1\" title=\"\\2\" alt=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\1\" style=\"max-width:850px;\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(.*?)\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\2\" style=\"max-width:850px;\" \/>", $x_comment);


/*---------------------------------------------------------------------------------
*  think about parsing content by dokuwiki renderer for dokuwiki syntax recognition
*        $x_comment = p_render('xhtml',p_get_instructions($x_comment),$info);
*        take care to strip IssueTracker syntax to prevent endless loop
---------------------------------------------------------------------------------*/

      return $x_comment;
    }
/******************************************************************************/
/* log issue modificaions
 * who changed what and when per issue
*/                                          
    function _log_mods($project, $issue, $usr, $column, $old_value, $new_value)
    {     global $conf;
          // get mod-log file contents
          if($conf['plugin']['issuetracker']['it_data']==false) $modfile = DOKU_CONF."../data/meta/".$project.'_'.$issue['id'].'.mod-log';
          else $modfile = DOKU_CONF."../". $conf['plugin']['issuetracker']['it_data'].$project.'_'.$issue['id'].'.mod-log';
          if (@file_exists($modfile))
              {$mods  = unserialize(@file_get_contents($modfile));}
          else 
              {$mods = array();}
          
          $cur_date = date('Y-m-d G:i:s');
          $mod_id = count($mods);
          if($new_value=='') $new_value = '[deleted]';
          $mods[$mod_id]['timestamp']   = $cur_date;
          $mods[$mod_id]['user']        = $usr;
          $mods[$mod_id]['field']       = $column;
          $mods[$mod_id]['old_value']   = $old_value;
          $mods[$mod_id]['new_value']   = $new_value;
          
          // Save issues file contents
          $fh = fopen($modfile, 'w');
          fwrite($fh, serialize($mods));
          fclose($fh);
    }
/******************************************************************************/
    global $ID;
    global $lang;
    global $conf;

    // Include the language file
    if ($conf['lang']=='') $conf['lang']=='en'; 
    if ($conf['lang']!=='') {
        $path = DOKU_PLUGIN.'issuetracker/lang/';
        // don't include once, in case several plugin components require the same language file
        @include($path.'en/lang.php');
        if ($conf['lang'] != 'en') @include($path.$conf['lang'].'/lang.php');
    }

    $exploded  = explode(' ',htmlspecialchars(stripslashes($_POST['id'])));
    $project   = $exploded[0];
    $id_issue  = intval($exploded[1]);
    $usr       = $_POST['usr'];    
    $currentID = $_POST['currentID'];
    $cur_date  = date('Y-m-d G:i:s');
 
    // get issues file contents
    if($conf['plugin']['issuetracker']['it_data']==false) $pfile = DOKU_CONF."../data/meta/".$project.'.issues';
    else $pfile = DOKU_CONF."../". $conf['plugin']['issuetracker']['it_data'].$project.'.issues';
    if (@file_exists($pfile))
        {$issues  = unserialize(@file_get_contents($pfile));}
    else 
        {$issues = array();}
    
    $field = strtolower(htmlspecialchars(stripslashes($_POST['field'])));
    $value = htmlspecialchars(stripslashes($_POST['value']));
    
    if(($field == 'resolution') && ($value !== false)) {
      $issues[$id_issue]['status'] = $lang['issue_resolved_status'];
    }
        
      $old_value = $issues[$id_issue][$field];
      $issues[$id_issue][$field] = $value;
      $issues[$id_issue]['modified'] = $cur_date;
      _log_mods($project, $issues[$id_issue], $usr, $field, $old_value, $value);
   // notification mails as long as status is not deleted
    if($conf['status_special']=='') $conf['status_special']='Deleted';
    if (stripos($conf['status_special'],$value) === false) {
      _emailForIssueMod($currentID, $project, $issues[$id_issue], $old_value, $field, $value, $usr);
    }
        // inform assigned workforce for new issue
    if ($field == 'assigned') {
        $issues[$id_issue]['assigned'] = $value;
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
        
        _emailToAssigneeMod($currentID, $project, $issues[$id_issue], $value, $usr);
    }
    // Save issues file contents
    $fh = fopen($pfile, 'w');
    fwrite($fh, serialize($issues));
    fclose($fh);
    echo $value;    
?>

