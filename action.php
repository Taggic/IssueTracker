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
         'date'   => '2011-08-07',
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
         if ($event->data === 'showcase') {
             $this->parameter = $_POST['showid'];
             $this->project = $_POST['project'];         
         }
         elseif ($event->data === 'showcaselink') {
            $this->parameter = $_GET['showid'];
            $this->project = $_GET['project'];
         }
         else return;
         
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
/******************************************************************************
**  Generate output
*/
    function output(&$data) {

         if (($data->data != 'showcase') && ($data->data != 'showcaselink')) return;
         $data->preventDefault();
//        if ($mode == 'xhtml'){            
             $renderer->info['cache'] = false;         
             $issue_id = $this->parameter;
             $project = $this->project;

             // get issues file contents
             $pfile = metaFN($project, '.issues');   
             if (@file_exists($pfile))
            	 {  $issues  = unserialize(@file_get_contents($pfile));}
             else
            	 {// promt error message that issue with ID does not exist
                  echo '<div class="it__negative_feedback">Project file does not exist: ' . $project . '.issues .</div><br>';
               }	                              
             
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
                               $comments_file = metaFN($project."_".$_REQUEST['comment_issue_ID'], '.cmnts');
        
                               if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                               else  {  $comments = array();  }
                                  	
                               //Add it to the comment file
                               $comment_id=count($comments);
                               $checkFlag=false;      
                               foreach ($comments as $value)
                                   {  if ($value['id'] >= $comment_id) { $comment_id=$value['id'] + 1; } 
                                      if ($_REQUEST['comment'] === $value['comment']) 
                                      {
                                          $Generated_Header = '<div class="it__negative_feedback">This comment does already exist and was not added again.</div><br>';
                                          $checkFlag=true; 
                                          break;
                                      }
                                   }
                               if ($checkFlag === false)
                               {
                                   $comments[$comment_id]['id'] = $comment_id;    
                                   $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                                   $comments[$comment_id]['timestamp'] = htmlspecialchars(stripslashes($_REQUEST['timestamp']));
                                   $comments[$comment_id]['comment'] = $_REQUEST['comment'];    
            
                                   //Create comments file
                                   $xvalue = io_saveFile($comments_file,serialize($comments));

                                   // inform user (or assignee) about update
                                   $this->_emailForIssueMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id]);                                 
            
                               
                                   // update modified date
                                   $issues[$_REQUEST['comment_issue_ID']]['modified'] = date('Y-m-d G:i:s'); 
                                   $xvalue = io_saveFile($pfile,serialize($issues));                                   
                                   $Generated_Header = '<div class="it__positive_feedback">Your comment has been successfully stored with ID #'.$comment_id.'.</div><br>';
                                   
                                }
                              // Cleanup comment variables
        //                              $_REQUEST['comment'] = ''                               
                             }
                        }
                   }
             }
             // Render 
                                                    // Array  , project name
             $Generated_Table = $this->_details_render($issues, $project);                 

           
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
        global $auth;
        $issue_id = $this->parameter;
        
//        echo 'Project = '.$project.'<br>Issue ID = '. $issue_id.'<br>';
        
        if ($issue_id === false) return;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
//        $user_grp = pageinfo();
        
        // get issues file contents
        $pfile = metaFN($project, '.issues');   
        if (@file_exists($pfile))
        	{  $issue  = unserialize(@file_get_contents($pfile));
  
             // check if ID exist
             $cFlag = false;
             foreach ($issue as $issue_item)  {
                if ($issue_item['id'] == $issue_id) {
                    $cFlag = true;
                    break;
                }
             }
             if ($cFlag === false) {
             // promt error message that issue with this ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">There does no Issue exist with ID '.$issue_id.'.</div><br>';
              echo $Generated_Header;
              return;
             }
          }
        else
        	{
              // promt error message that issue with ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">Project file does not exist: '.$pfile.'</div><br>';
              echo $Generated_Header;
              return;
          }	          
        
        // get detail information from issue comment file
        $cfile = metaFN($project."_".$issue_id, '.cmnts');
        if (@file_exists($cfile)) {$comments  = unserialize(@file_get_contents($cfile));}
        else {$comments = array();}

        $a_severity = $issue[$issue_id]['severity'];                  
        $severity_img = $imgBASE . implode('', explode(' ',strtolower($a_severity))).'.gif';
        $severity_img =' <IMG border=0 alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$severity_img.'" width=16 height=16> ';
        $a_status = $issue[$issue_id]['status'];
        $status_img = $imgBASE . implode('', explode(' ',strtolower($a_status))).'.gif';
        $status_img =' <IMG border=0 alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$status_img.'" width=16 height=16> ';


        //---------------------------------------------------------------------------------------------------------------------
        // do not show personal contact details if issue details not viewed by admin/assignee nor the original reporter itself
        //---------------------------------------------------------------------------------------------------------------------
        $user_mail = pageinfo();  //to get mail address of reporter
        $filter['grps']=$this->getConf('assign');
        $target = $auth->retrieveUsers(0,0,$filter);
        $target2 = $this->array_implode($target);
        $target2 = implode($target2);
        
        if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
        {   $__assigened  = $issue[$issue_id]['assigned'];
            $__reportedby = $issue[$issue_id]['user_mail'];
        }
        else 
        {   foreach($target as $_assignee)
              { if($_assignee['mail'] === $issue[$issue_id]['assigned'])
                {   $__assigened = $_assignee['name'];
                    break;
                }
              }
            $__reportedby = $issue[$issue_id]['user_name'];
        }
                   

//--------------------------------------
// Tables for the Issue details view:
//--------------------------------------
$issue_edit_head = '<TABLE class="itd__title">'.
                   '<TR>
                      <TD colSpan=6 >
                      <P>
                        <FONT size=1><I>&nbsp['.$issue[$issue_id]['id'].']&nbsp;&nbsp;</I></FONT>
                        <FONT size=3 color=#00008f>'.
                          '<B><I><H class="itd_formtitle">'.$issue[$issue_id]['title'].'</H></I></B>
                        </FONT>
                      </P>
                      </TD>
                    </TR>'.                  
                   '<TBODY class="itd__details">'.                    
                   '<TR class="itd_tr_standard">
                      <TD class="it__left_indent"></TD>
                      <TD class="itd__col2">ID:</TD>
                      <TD class="itd__col3">'.$issue[$issue_id]['id'].'</TD>
                      <TD class="itd__col4"></TD>                   
                      <TD class="itd__col5">Project:</TD>
                      <TD class="itd__col6">'.$project.'</TD>
                    </TR>';
                   
$issue_edit_head .= '<TR class="itd_tr_standard">
                      <TD class="it__left_indent"></TD>
                      <TD class="itd__col2">Severity:</TD>
                      <TD class="itd__col3">'.$severity_img.$issue[$issue_id]['severity'].'</TD>
                      <TD class="itd__col4"></TD>                   
                      <TD class="itd__col5">Product:</TD>
                      <TD class="itd__col6">'.$issue[$issue_id]['product'].'</TD>
                    </TR>';
                   
$issue_edit_head .= '<TR class="itd_tr_standard">
                      <TD class="it__left_indent"></TD>
                      <TD class="itd__col2">Status:</TD>
                      <TD class="itd__col3">'.$status_img.$issue[$issue_id]['status'].'</TD>
                      <TD class="itd__col4"></TD>                   
                      <TD class="itd__col5">Version:</TD>
                      <TD class="itd__col6">'.$issue[$issue_id]['version'].'</TD>
                    </TR>';

$issue_edit_head .= '<TR class="itd_tr_standard">                      
                      <TD class="it__left_indent"></TD>
                      <TD class="itd__col2">Reported by:</TD>
                      <TD class="itd__col3"><A  href="mailto:'.$__reportedby.'">'.$__reportedby.'</A></TD>
                      <TD class="itd__col4"></TD>                   
                      <TD class="itd__col5">created:</TD>
                      <TD class="itd__col6">'.$issue[$issue_id]['created'].'</TD>
                    </TR>
                   
                    <TR class="itd_tr_standard">
                      <TD class="it__left_indent"></TD>
                      <TD class="itd__col2">Assigned to:</TD>
                      <TD class="itd__col3"><A  href="mailto:'.$__assigened.'">'.$__assigened.'</A></TD>
                      <TD class="itd__col4"></TD>                   
                      <TD class="itd__col5">modified:</TD>
                      <TD class="itd__col6">'.$issue[$issue_id]['modified'].'</TD>
                    </TR>
                    </TBODY></TABLE>';


$issue_client_details = '<TABLE class="itd__tables"><TBODY>
                        <TR>
                           <TD class="itd_tables_tdh" colSpan=3>Reporter Details</TD>
                        </TR>
                        <TR class="itd__tables_tr">
                           <TD class="it__left_indent"></TD>
                           <TD class="itd_tables_tdc2">Name:</TD>
                           <TD class="itd_tables_tdc3">'.$issue[$issue_id]['user_name'].'</TD>
                        </TR>';

                        //--------------------------------------------------------------------------------------------------------------
                        // do not show personal details if issue details diplayed by neigther admin/assignee nor the original user itself
                        //--------------------------------------------------------------------------------------------------------------
/*                        echo "current user = ".$user_mail['userinfo']['mail']."<br>".
                               "Reporting user = ".$issue[$issue_id]['user_mail']."<br>";
                          if($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) {echo "current user = Reporting user <br><br>";}
                             else {echo "current user != Reporting user <br><br>";}
                          if(strpos($target2,$user_mail['userinfo']['mail']) != false) {echo "current user is a member of assignees <br><br>";}
                             else {echo "current user is not a member of assignees <br><br>";}
*/                               
                        if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
                        {
$issue_client_details .= '<TR class="itd__tables_tr">
                            <TD class="it__left_indent"></TD>
                            <TD class="itd_tables_tdc2">Email:</TD>
                            <TD class="itd_tables_tdc3"><A href="mailto:'.$issue[$issue_id]['user_mail'].'">'.$issue[$issue_id]['user_mail'].'</A></TD>
                          </TR>
                          <TR class="itd__tables_tr">
                            <TD class="it__left_indent"></TD>
                            <TD class="itd_tables_tdc2">Phone:</TD>
                            <TD class="itd_tables_tdc3">'.$issue[$issue_id]['user_phone'].'</TD>
                          </TR>
                          <TR class="itd__tables_tr">
                            <TD class="it__left_indent"></TD>
                            <TD class="itd_tables_tdc2">Add contact:</TD>
                            <TD class="itd_tables_tdc3"><A href="mailto:'.$issue[$issue_id]['add_user_mail'].'">'.$issue[$issue_id]['add_user_mail'].'</A></TD>
                          </TR>
                          </TBODY></TABLE>'; 
                        }
                        else {
                          $issue_client_details .= '</TBODY></TABLE>';
                        }

                        $x_comment = $this->convertlabel($issue[$issue_id]['description']);

$issue_initial_description = '<TABLE class="itd__tables"><TBODY>
                                <TR>
                                  <TD class="itd_tables_tdh" colSpan=2 >Initial description</TD>
                                </TR>
                                <TR class="itd__tables_tr">
                                  <TD width="1%"></TD>
                                  <TD>'.$x_comment.'</TD>
                                </TR>
                              </TBODY></TABLE>';

$issue_attachments = '<TABLE class="itd__tables"><TBODY>
                      <TR>
                        <TD class="itd_tables_tdh">Links to symptom files</TD>
                      </TR>
                      <TR  class="itd__tables_tr">
                        <TD style="padding-left:0.45em;">1. <A href="'.$issue[$issue_id]['attachment1'].'"><IMG border=0 alt="symptoms 1" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment1'].'" href="'.$issue[$issue_id]['attachment1'].'">'.$issue[$issue_id]['attachment1'].'</A></TD>
                      </TR>'.
                     '<TR  class="itd__tables_tr">
                        <TD style="padding-left:0.45em;">2. <A href="'.$issue[$issue_id]['attachment2'].'"><IMG border=0 alt="symptoms 2" style="margin-right:0.5em" vspace=1em align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment2'].'" href="'.$issue[$issue_id]['attachment2'].'">'.$issue[$issue_id]['attachment2'].'</A></TD>
                      </TR>'.
                     '<TR  class="itd__tables_tr">
                        <TD style="padding-left:0.45em;">3. <A href="'.$issue[$issue_id]['attachment3'].'"><IMG border=0 alt="symptoms 3" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment3'].'" href="'.$issue[$issue_id]['attachment3'].'">'.$issue[$issue_id]['attachment3'].'</A></TD>
                      </TR>'.
                     '</TBODY></TABLE>';              

$issue_comments_log ='<TABLE class="itd__tables"><TBODY>
                      <TR>
                        <TD class="itd_tables_tdh" colSpan=2 >Comments (work log)</TD>
                      </TR>';
              // loop through the comments
              if ($comments!=false) {              
                  foreach ($comments as $a_comment)
                  {
                        $x_id = $this->_get_one_value($a_comment,'id');
                        $x_comment = $this->_get_one_value($a_comment,'comment');
                        $x_comment = $this->convertlabel($x_comment);
                        
                        //----------------------------------------------------------------------------------------------------------------
                        // do not show personal details if issue details diplayed by neigther admin/assignee nor the original user itself
                        //----------------------------------------------------------------------------------------------------------------
                        if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
                        {   $x_mail = '<a href="mailto:'.$this->_get_one_value($a_comment,'author').'">'.$this->_get_one_value($a_comment,'author').'</a>'; }
                        else {   $x_mail = '<i> (user details hidden) </i>';  }

                        $issue_comments_log .= '<TR  class="itd__tables_tr">
                                                  <TD class="itd_comment_trh"><label>['.$this->_get_one_value($a_comment,'id').'] </label>&nbsp;&nbsp;&nbsp;
                                                                            <label>'.$this->_get_one_value($a_comment,'timestamp').' </label>&nbsp;&nbsp;&nbsp;
                                                                            <label>'.$x_mail.'</label></TD>
                                                </TR>
                                                <TR  class="itd__tables_tr">
                                                  <TD class="itd_comment_tr">'.$x_comment.'</TD>
                                                </TR>';
                  }
              }
              $issue_comments_log .='</TBODY></TABLE>'; 

                     
        //--------------------------------------------------------------------------------------------------------------
        // only admin/assignees and reporter are allowed to add comments if only user edit option is set
        //--------------------------------------------------------------------------------------------------------------
        // retrive some basic information
        $cur_date = date ('Y-m-d G:i:s');
        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');
        
        $_cFlag = false;             
        if($user_check == false)
            { $_cFlag = true; } 
            
        elseif ($user_check == true) {
            if ($user_mail['perm'] > 1) 
            { $_cFlag = true; } }

/*        echo 'result of user_check = '.$user_check.'<br>'.
             'result of user_mail[perm] = '.$user_mail['perm'].'<br>'.
             'result of _cFlag = '.$_cFlag.'<br>';
*/
        if($_cFlag === true) {
$issue_add_comment ='<TABLE class="itd__tables">'.
                      '<TR>'.
                        '<TD class="itd_tables_tdh" colSpan=2 >Add a new comment</TD>
                      </TR><TR><TD>';
                      
$issue_add_comment .= '<script type="text/javascript" src="include/selectupdate.js"></script>'.
                      '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">';

$issue_add_comment .= formSecurityToken(false). 
                     '<input type="hidden" name="project" type="text" value="'.$project.'"/>'.
                     '<input type="hidden" name="comment_file" type="text" value="'.$cfile.'"/>'.
                     '<input type="hidden" name="comment_issue_ID" type="text" value="'.$issue[$issue_id]['id'].'"/>'.
                     '<input type="hidden" name="author" type="text" value="'.$u_mail_check.'"/>'.        
                     '<input type="hidden" name="timestamp" type="text" value="'.$cur_date.'"/>'.        
                     '<textarea name="comment" type="text" cols="106" rows="7" value=""></textarea>';        
             
                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_add_comment .= '<p>'.$helper->getHTML().'</p>'; }
                      }
                      
                      // check if only registered users are allowed to add comments
                      // ¦ perm — the user's permissions related to the current page ($ID)
                      $issue_add_comment .= '<input  type="hidden" class="showid__option" name="showid" id="showid" type="text" size="10" value="'.$this->parameter.'"/>'.
                                            '<input class="button" id="showcase" type="submit" name="showcase" value="Add" title="Add");/>'.
                                            '</form></TD></TR></Table>';
        }
        else {
           $wmsg = 'Please <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">Sign in</a> if you want to add a comment.'; 
           $issue_add_comment .= '<div class="it__standard_feedback">'.$wmsg.'</div>';                      
        }
                                           
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
            
            $body = 'Dear user,'.chr(10).chr(10).'Your reported issue was modified.'.chr(10).chr(13).
            'ID: '          .$issue['id'].chr(10).
            'Status: '      .$issue['status'].chr(10).
            'Product: '     .$issue['product'].chr(10).
            'Version: '     .$issue['version'].chr(10).
            'Severity: '    .$issue['severity'].chr(10).
            'Creator: '     .$issue['user_name'].chr(10).
            'Title: '       .$issue['title'].chr(10).
            'Comment by: '  .$comment['author'].chr(10).
            'submitted on:' .$comment['timestamp'].chr(10).
            'Comment: '     .$comment['comment'].chr(10).
            'see details:'  .chr(10).chr(10). 
            'best regards'.chr(10).'Issue Tracker';

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