<?php
/******************************************************************************
**
**  action script related to IssueTracker
**  Action to display details of a selected issue
*/
/******************************************************************************
**  must run within Dokuwiki
**/
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

/******************************************************************************/
class action_plugin_issuetracker extends DokuWiki_Action_Plugin {

    var $parameter = "";

  /**
   * return some info
   */
  function getInfo(){
    return array(
         'author' => 'Taggic',
         'email'  => 'Taggic@t-online.de',
         'date'   => '2011-07-23',
         'name'   => 'Issue comments (action plugin component)',
         'desc'   => 'to display comments of a dedicated issue.',
         'url'    => 'http://forum.dokuwiki.org/thread/2456 '.
                     ' http://forum.dokuwiki.org/thread/7182',
         );
  }
/******************************************************************************
**  Register its handlers with the dokuwiki's event controller
*/
     function register(&$controller) {
         $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, '_handle_act', array());
         $controller->register_hook('TPL_ACT_UNKNOWN', 'BEFORE', $this, 'output', array());
     }

/******************************************************************************
**  Handle the action
*/
     function _handle_act(&$event, $param) {
         if ($event->data != 'showcase') return;

         $this->parameter = $_POST['showid'];
         $this->project = $_POST['project'];
         $event->preventDefault(); // https://www.dokuwiki.org/devel:events#event_object  
     }
/******************************************************************************
**  format string for comment label
*/
    function convertlabel($txt){ 
        $len = strlen($txt); 
        $res = ""; 
        for($i = 0; $i < $len; ++$i) { 
            $ord = ord($txt{$i}); 
            // replace all linefeeds          
            if($ord === 10){ $res .= "<br>";  } 
            else { $res .= $txt{$i}; }                    
        } 
        return $res; 
    } 

/******************************************************************************
**  Generate output
*/
    function output(&$data) {

         if ($data->data != 'showcase') return;
         $data->preventDefault();
//        if ($mode == 'xhtml'){            
             $renderer->info['cache'] = false;         
             $issue_id = $this->parameter;
             $project = $this->project;
//echo "Project  -> '" . $project . "'<br> Issue ID  -> '".$issue_id."'";             
             // get issues file contents
             $pfile = metaFN($project, '.issues');   
             if (@file_exists($pfile))
            	 {  $issues  = unserialize(@file_get_contents($pfile));}
             else
            	 {// promt error message that issue with ID does not exist
                  echo 'Project file does not exist: ' . $project . '.issues';
               }	                              
                                                    // Array  , project name
             $Generated_Table = $this->_details_render($issues, $project);                 
             $Generated_Header = '';
    
             //If comment to be added
             if (isset($_REQUEST['comment'])) 
             {  if (($_REQUEST['comment']) && (isset($_REQUEST['comment_issue_ID'])))
                   {

                   // check if captcha is to be used by issue tracker in general
                   if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                   else { $captcha_ok = ($this->_captcha_ok());}
                   
                   
                   if ($captcha_ok)
                         {  
                         
                            if (checkSecurityToken())
                            {
                               // get comment file contents
                               $comments_file = metaFN("ic_".$_REQUEST['comment_issue_ID'], '.cmnts');
  
                               if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                               else  {  $comments = array();  }
                                  	
                               //Add it to the issue file
                               $comment_id=count($comments);      
                               foreach ($comments as $value)
                                   {  if ($value['id'] >= $comment_id) { $comment_id=$value['id'] + 1; } }
                              
                               $comments[$comment_id]['id'] = $comment_id;    
                               $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                               $comments[$comment_id]['timestamp'] = htmlspecialchars(stripslashes($_REQUEST['timestamp']));
                               $comments[$comment_id]['comment'] = $_REQUEST['comment'];    
  
                               //Create comments file
                               $xvalue = io_saveFile($comments_file,serialize($comments));
                               $Generated_Header = '<div style="border: 3px green solid; background-color: lightgreen; width: 87%; margin: 0px; padding: 10px;">Your comment has been successfully stored with ID #'.$comment_id.'</div>';
                               // inform user (or assignee) about update
                               $this->_emailForIssueMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id]);                                 

                           
                               // update modified date
                               $issues[$_REQUEST['comment_issue_ID']]['modified'] = date('Y-m-d G:i:s'); 
                               $xvalue = io_saveFile($pfile,serialize($issues));
                               
                              // Cleanup comment variables
//                              unset($_REQUEST['comment']); 
                               
                             }
                        }
                   }
             }
             // Render            
             //$data->doc .= $Generated_Header.$Generated_Table.$Generated_feedback;
             echo $Generated_Header.$Generated_Table.$Generated_feedback;
//        }
    }

/******************************************************************************
**  Details form
*/
                       // Array  , project name
 function _details_render($issues, $project) {        
        // load issue details and display on page
        global $lang;
        $issue_id = $this->parameter;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
        
        // get issues file contents
        $pfile = metaFN($project, '.issues');   
        if (@file_exists($pfile))
        	{$issue  = unserialize(@file_get_contents($pfile));}
        else
        	{
              // promt error message that issue with ID does not exist
              echo 'Project file does not exist: ' . $pfile;
          }	          
        
        // get detail information from issue comment file
        $cfile = metaFN("ic_".$issue_id, '.cmnts');
        if (@file_exists($cfile)) {$comments  = unserialize(@file_get_contents($cfile));}
        else {$comments = array();}

//--------------------------------------
// Tables for the Issue details view:
//--------------------------------------
$issue_edit_head = '<div><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff >'.
                   '<TR>
                      <TD bgColor=#f0f0f0 vAlign=center colSpan=6 >
                      <P vAlign=center style="border-width:8px; border-color:#9999FF; border-style:outset; padding:5px;">
                        <FONT size=1><I>&nbsp['.$issue[$issue_id]['id'].']&nbsp;&nbsp;</I></FONT>
                        <FONT size=3 color=#00008f>'.
                          '<B><I><H class=formtitle>'.$issue[$issue_id]['title'].'</H></I></B></FONT></TR></TD></P>'.
                   '<TBODY>'. 
                   '<TR bgColor=#f0f0f0 vAlign=center >
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>ID:</B></TD>
                      <TD width="25%">'.$issue[$issue_id]['id'].'</label></TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>
                      <TD width="10%"><B>Project:</B></TD>
                      <TD>'.$project.'</TD></FONT>
                   </TR>';
                   
                    $a_severity = $issue[$issue_id]['severity'];                  
                    $severity_img = $imgBASE . implode('', explode(' ',strtolower($a_severity))).'.gif';
                    $severity_img =' <IMG border=0 alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$severity_img.'" width=16 height=16> ';
$issue_edit_head .= '<TR bgColor=#f0f0f0 vAlign=center>
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Severity:</B></TD>
                      <TD width="25%">'.$severity_img.$issue[$issue_id]['severity'].'</TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>'.
                      '<TD width="10%"><B>Product:</B></TD>
                      <TD>'.$issue[$issue_id]['product'].'</TD>
                   </FONT></TR>';
                   
                    $a_status = $issue[$issue_id]['status'];
                    $status_img = $imgBASE . implode('', explode(' ',strtolower($a_status))).'.gif';
                    $status_img =' <IMG border=0 alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$status_img.'" width=16 height=16> ';
$issue_edit_head .= '<TR bgColor=#f0f0f0 vAlign=center>
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Status:</B></TD>
                      <TD width="25%">'.$status_img.$issue[$issue_id]['status'].'</TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>
                      <TD width="10%"><B>Version:</B></TD>
                      <TD>'.$issue[$issue_id]['version'].'</TD>
                   </Font></TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center>                      
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Assigned:</B></TD>
                      <TD width="25%"><A  href="mailto:'.$issue[$issue_id]['assigned'].'">'.$issue[$issue_id]['assigned'].'</A></TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>'.
                      '<TD width="10%"><B>created:</B></TD>
                      <TD><SPAN class=date>'.$issue[$issue_id]['created'].'</SPAN></TD>
                   </FONT></TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center>
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Reporter:</B></TD>
                      <TD width="25%"><A  href="mailto:'.$issue[$issue_id]['user_mail'].'">'.$issue[$issue_id]['user_mail'].'</A></TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>'.                   
                      '<TD width="10%"><B>modified:</B></TD>
                      <TD><SPAN class=date>'.$issue[$issue_id]['modified'].'</SPAN></TD>
                   </FONT></TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center><FONT size=1><TD  colSpan=6 >&nbsp;</TD></FONT></TR>'.                       
                   '</TBODY>'.
                   '</TABLE></BR></div>';


$issue_client_details = '<DIV id=client_details><TABLE id=tab2 class=gridTabBox border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY>'.
                        '<TR><TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=3><FONT color=#ffffff><B>Client</B></FONT></TD></TR>'.
                        '<TR id=rowForcustomfield_10020><TD bgColor=#f0f0f0 width="1%"></TD><TD bgColor=#f0f0f0 vAlign=center width="25%"><B>Name:</B></TD><TD bgColor=#f0f0f0 width="75%">'.$issue[$issue_id]['user_name'].'</TD></TR>'.
                        '<TR id=rowForcustomfield_10030><TD bgColor=#f0f0f0 width="1%"></TD><TD bgColor=#f0f0f0 vAlign=center width="25%"><B>Email:</B></TD><TD bgColor=#f0f0f0 width="75%"><A href="mailto:'.$issue[$issue_id]['user_mail'].'">'.$issue[$issue_id]['user_mail'].'</A> </TD></TR>'.
                        '<TR id=rowForcustomfield_10040><TD bgColor=#f0f0f0 width="1%"></TD><TD bgColor=#f0f0f0 vAlign=center width="25%"><B>Phone:</B></TD><TD bgColor=#f0f0f0 width="75%">'.$issue[$issue_id]['user_phone'].'</TD></TR>'.
                        '<TR id=rowForcustomfield_10050><TD bgColor=#f0f0f0 width="1%"></TD><TD bgColor=#f0f0f0 vAlign=center width="25%"><B>Add client contact:</B></TD><TD bgColor=#f0f0f0 width="75%"><A href="mailto:'.$issue[$issue_id]['add_user_mail'].'">'.$issue[$issue_id]['add_user_mail'].'</A></TD></TR>'.
                        '</TBODY></TABLE></DIV>'; 

                        $x_comment = $this->convertlabel($issue[$issue_id]['description']);
$issue_initial_description = '<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY><TR>'.
        '<TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=2 >&nbsp;<FONT color=#ffffff><B>Initial description</B></FONT>&nbsp;</TD></TR></TBODY></TABLE>'.
        '<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%"><TBODY><TR>'.
        '<TD width="1%"><TD id=descriptionArea>'.$x_comment.'</TD></TR></TBODY></TABLE>';

$issue_attachments = '<DIV id=client_details><TABLE id=tab1 class=gridTabBox border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY>'.
                     '<TR><TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=3><FONT color=#ffffff><B>Links to symptom files</B></FONT></TD></TR>'.
                     '<TR><TD width="1%"></TD><TD bgColor=#ffffff vAlign=top colSpan=5>
                          1. <A href="'.$issue[$issue_id]['attachment1'].'"><IMG border=0 alt="symptoms 1" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment1'].'" href="'.$issue[$issue_id]['attachment1'].'">'.$issue[$issue_id]['attachment1'].'</A>'.
                     '<BR>2. <A href="'.$issue[$issue_id]['attachment2'].'"><IMG border=0 alt="symptoms 2" style="margin-right:0.5em" vspace=1em align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment2'].'" href="'.$issue[$issue_id]['attachment2'].'">'.$issue[$issue_id]['attachment2'].'</A>'.
                     '<BR>3. <A href="'.$issue[$issue_id]['attachment3'].'"><IMG border=0 alt="symptoms 3" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment3'].'" href="'.$issue[$issue_id]['attachment3'].'">'.$issue[$issue_id]['attachment3'].'</A>'.
                     '<BR><BR></TD></TR></TBODY></TABLE></DIV></BR>';              

$issue_comments_log ='<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY><TR>'.
        '<TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=2 >&nbsp;<FONT color=#ffffff><B>Comments (work log)</B></FONT>&nbsp;</TD></TR></TBODY></TABLE>'.
        '<TABLE border=0 cellSpacing=0 cellPadding=0 width="90%"></TABLE></DIV>';
              // loop through the comments
              if ($comments!=false) {              
                  foreach ($comments as $a_comment)
                  {
                        $x_id = $this->_get_one_value($a_comment,'id');
                        $x_mail = $this->_get_one_value($a_comment,'author');
                        $x_comment = $this->_get_one_value($a_comment,'comment');
                        $x_comment = $this->convertlabel($x_comment);
                        $issue_comments_log .= '<TABLE width="85%" style="margin-left:10px; margin-right:10px">'.
                                               '<TR><TD><FONT size=1><I><label>['.$this->_get_one_value($a_comment,'id').'] </label>&nbsp;&nbsp;&nbsp;'.
                                               '<label>'.$this->_get_one_value($a_comment,'timestamp').' </label>&nbsp;&nbsp;&nbsp;'.
                                               '<label><a href="mailto:'.$x_mail.'">'.$x_mail.'</a></label></I></FONT></TD></TR>'.
                                               '<TR><TD>'.$x_comment.'</TD></TR></TABLE><hr width="90%">';
                  }
              } 
              $issue_comments_log .= '</DIV>';

$issue_add_comment ='<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY><TR>'.
        '<TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=2 >&nbsp;<FONT color=#ffffff><B>Add a new comment</B></FONT>&nbsp;</TD></TR></TBODY></TABLE>'.
        '<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';

$issue_add_comment .= '<script type="text/javascript" src="include/selectupdate.js"></script>'.
                     '<form class="comments__form" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'">';
                     
        // retrive basic information
        $user_mail = pageinfo();  //to get mail address of reporter
        $cur_date = date ('Y-m-d G:i:s');

        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        else {$u_mail_check =$user_mail['userinfo']['mail'];}

$issue_add_comment .= formSecurityToken(false). 
                     '<input type="hidden" name="project" type="text" value="'.$project.'"/>'.
                     '<input type="hidden" name="comment_file" type="text" value="'.$cfile.'"/>'.
                     '<input type="hidden" name="comment_issue_ID" type="text" value="'.$issue[$issue_id]['id'].'"/>'.
                     '<input type="hidden" name="author" type="text" value="'.$u_mail_check.'"/>'.        
                     '<input type="hidden" name="timestamp" type="text" value="'.$cur_date.'"/>'.        
                     '<td><textarea name="comment" type="text" cols="108" rows="7" value="'.$_REQUEST['comment'].'"></textarea></td></TABLE></DIV>';        
             
                      if ($this->getConf('use_captcha')==1) 
                      {        
                          $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {
              			         $issue_add_comment .= '<p>'.$helper->getHTML().'</p>';
              			      }
                      }
            
$issue_add_comment .= '<p><input  type="hidden" class="showid__option" name="showid" id="showid" type="text" size="10" value="'.$this->parameter.'"/>'.
     '<input class="button" id="showcase" type="submit" name="showcase" value="Add" title="Add");/></p>'.
                           '</form>';
                                           
        $ret = $issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment;
        return $ret;
    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                            
    function _emailForIssueMod($project,$issue,$comment)
    {
        if ($this->getConf('userinfo_email')==1)
        {
            $subject='Issue '.$issue['id'].' on '.$project.' was modified';            
            
            $body = 'Dear user,'.chr(10).chr(13).chr(10).chr(13).'Your reported issue was modified.'.chr(10).chr(13).
            'ID: '          .$issue['id'].chr(10).chr(13).
            'Status: '      .$issue['status'].chr(10).chr(13).
            'Product: '     .$issue['product'].chr(10).chr(13).
            'Version: '     .$issue['version'].chr(10).chr(13).
            'Severity: '    .$issue['severity'].chr(10).chr(13).
            'Creator: '     .$issue['user_name'].chr(10).chr(13).
            'Title: '       .$issue['title'].chr(10).chr(13).
            'Comment by: '  .$comment['author'].chr(10).chr(13).
            'submitted on:' .$comment['timestamp'].chr(10).chr(13).
            'Comment: '     .$comment['comment'].chr(10).chr(13).
            'see details:'  .chr(10).chr(13).chr(10).chr(13). 
            'best regards'.chr(10).chr(13).'Issue Tracker';

            $from=$this->getConf('email_address') ;
            $to=$issue['user_mail'];
            $cc=$issue['add_user_mail'];
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
        }
    }
/******************************************************************************/
/* pic-up a single value
*/
    function _get_one_value($issue, $key) {
        if (array_key_exists($key,$issue))
            return $issue[$key];
        return '';
    }
/******************************************************************************/
/* Captcha OK	    
*/
		function _captcha_ok()
		{        			
			$helper = null;		
			if(@is_dir(DOKU_PLUGIN.'captcha'))   $helper = plugin_load('helper','captcha');
				
			if(!is_null($helper) && $helper->isEnabled()) {	return $helper->check(); }
			
      return ($this->getConf('use_captcha'));
		}
    
/******************************************************************************/
}