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
require_once(DOKU_PLUGIN.'issuetracker/assilist.php'); 

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
         'date'   => '2016-06-02',
         'name'   => 'Issue comments (action plugin component)',
         'desc'   => 'to display details of a dedicated issue.',
         'url'    => 'https://www.dokuwiki.org/plugin:issuetracker',
         );
  }
/******************************************************************************
**  Register its handlers with the dokuwiki's event controller
*/
     function register(Doku_Event_Handler $controller) {
         $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, '_handle_act', array());
         $controller->register_hook('TPL_ACT_UNKNOWN', 'BEFORE', $this, 'output', array());
                                    //HTML_UPDATEPROFILEFORM_OUTPUT
         $controller->register_hook( 'AUTH_USER_CHANGE',
                          			'BEFORE',
                          			$this,
                          			'handle_usermod_before',
                          			array());
                                
          $controller->register_hook( 'AUTH_USER_CHANGE',
                          			'AFTER',
                          			$this,
                          			'handle_usermod_after',
                          			array());
//          $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, '_hookjs');                                    
     }

/******************************************************************************/
  /**
   * Hook js script into page headers.
   */
  function _hookjs(&$event, $param) {
    
    $css=DOKU_URL."lib/plugins/issuetracker/datepick/jsDatePick_ltr.css";
    $jquery=DOKU_URL."lib/plugins/issuetracker/datepick/jquery.1.4.2.js";
    $datepicker=DOKU_URL."lib/plugins/issuetracker/datepick/jsDatePick.min.1.3.js";

    
    $event->data["link"][] = array (  
                "rel" => "stylesheet",
                "media" => "all",
                "type" => "text/css",
				        "href" =>  $css
				        );



    $event->data["script"][] = array (  "type" => "text/javascript",
				        "charset" => "utf-8",
				        "_data" => "",
				        "src" =>  $jquery
				        );

    $event->data["script"][] = array (  "type" => "text/javascript",
				        "charset" => "utf-8",
				        "_data" => "",
				        "src" =>  $datepicker
				        );
                
    $event->data["script"][] = array (  "type" => "text/javascript",
				        "charset" => "utf-8",
				        "_data" => "
                window.onload = function(){
                      		new JsDatePick({
                      			useMode:2,
                      			target:'dev_start',
                      			dateFormat:'%d.%M.%Y'
                      		});
                          new JsDatePick({
                      			useMode:2,
                      			target:'dev_deadline',
                      			dateFormat:'%d.%M.%Y'
                      		});
                      	};",
				        "src" =>  ""
				        );
  }

/******************************************************************************
**  Handle the action
*/
     function _handle_act(&$event, $param) {
         if (($event->data === 'showcase') || ($event->data === 'store_resolution') || ($event->data === 'store_workaround')){
             $this->parameter  = $_POST['showid'];
             $this->project    = $_POST['itl_project'];
         }
         elseif ($event->data === 'showcaselink') {
            $this->parameter   = $_GET['showid'];
            $this->project     = $_GET['project'];
         }
         elseif($event->data === 'btn_add_contact') {
            $this->project     = $_POST['project'];
            $this->issue_ID    = $_POST['issue_ID'];
            $this->add_contact = $_POST['add_contact'];
         }
         elseif($event->data === 'btn_upd_addinfo') {
            $this->project       = $_POST['project'];
            $this->parameter     = $_POST['issue_ID'];
            $this->new_component = $_POST['new_component'];
            $this->trgt_version  = $_POST['trgt_version'];
            $this->itl_block     = $_POST['itl__block_filter'];
            $this->dev_start     = $_POST['dev_start'];
            $this->dev_deadline  = $_POST['dev_deadline'];
            $this->dev_progress  = $_POST['dev_progress'];
         }
         elseif ($event->data === 'it_search') {
            $this->parameter   = $_POST['it_str_search'];
            $this->project     = $_POST['itl_project'];
         }
         elseif ($event->data === 'issuelist_next') {
            $this->itl_start   = $_POST['itl_start'];
            $this->itl_step    = $_POST['itl_step'];
            $this->itl_next    = $_POST['itl_next'];
            $this->itl_pjct    = $_POST['itl_project'];
            $this->itl_sort    = $_POST['it_glbl_sort'];
            $this->itl_stat    = $_POST['itl_stat_filter'];
            $this->itl_sev     = $_POST['itl_sev_filter'];
            $this->itl_prod    = $_POST['itl__prod_filter'];
            $this->itl_vers    = $_POST['itl__vers_filter'];
            $this->itl_comp    = $_POST['itl__comp_filter'];
            $this->itl_block   = $_POST['itl__block_filter'];
            $this->itl_assi    = $_POST['itl__assi_filter'];
            $this->itl_reporter= $_POST['itl__user_filter'];
            $this->itl_myis    = $_POST['itl_myis_filter'];
            $this->project     = $_POST['itl_project'];
            $this->it_th_cols  = $_POST['it_th_cols'];

         }
         elseif ($event->data === 'issuelist_previous') {
            $this->itl_start   = $_POST['itl_start'];
            $this->itl_step    = $_POST['itl_step'];
            $this->itl_next    = $_POST['itl_next'];
            $this->itl_pjct    = $_POST['itl_project'];
            $this->itl_sort    = $_POST['it_glbl_sort'];
            $this->itl_stat    = $_POST['itl_stat_filter'];
            $this->itl_sev     = $_POST['itl_sev_filter'];
            $this->itl_prod    = $_POST['itl__prod_filter'];
            $this->itl_vers    = $_POST['itl__vers_filter'];
            $this->itl_comp    = $_POST['itl__comp_filter'];
            $this->itl_block   = $_POST['itl__block_filter'];
            $this->itl_assi    = $_POST['itl__assi_filter'];
            $this->itl_reporter= $_POST['itl__user_filter'];
            $this->itl_myis    = $_POST['itl_myis_filter'];
            $this->project     = $_POST['itl_project'];
            $this->it_th_cols  = $_POST['it_th_cols'];
         }
         elseif ($event->data === 'issuelist_filter') {
            $this->itl_start   = $_POST['itl_start'];
            $this->itl_step    = $_POST['itl_step'];
            $this->itl_next    = $_POST['itl_next'];
            $this->itl_pjct    = $_POST['itl_project'];
            $this->itl_sort    = $_POST['it_glbl_sort'];
            $this->itl_stat    = $_POST['itl_stat_filter'];
            $this->itl_sev     = $_POST['itl_sev_filter'];
            $this->itl_prod    = $_POST['itl__prod_filter'];
            $this->itl_vers    = $_POST['itl__vers_filter'];
            $this->itl_comp    = $_POST['itl__comp_filter'];
            $this->itl_block   = $_POST['itl__block_filter'];
            $this->itl_assi    = $_POST['itl__assi_filter'];
            $this->itl_reporter= $_POST['itl__user_filter'];
            $this->itl_myis    = $_POST['itl_myis_filter'];
            $this->project     = $_POST['itl_project'];
            $this->it_th_cols  = $_POST['it_th_cols'];
         }
         elseif ($event->data === 'issuelist_filterlink') {
            $this->itl_start   = $_GET['itl_start'];
            $this->itl_step    = $_GET['itl_step'];
            $this->itl_next    = $_GET['itl_next'];
            $this->itl_pjct    = $_GET['itl_project'];
            $this->itl_sort    = $_GET['it_glbl_sort'];
            $this->itl_stat    = $_GET['itl_stat_filter'];
            $this->itl_sev     = $_GET['itl_sev_filter'];
            $this->itl_prod    = $_GET['itl__prod_filter'];
            $this->itl_vers    = $_GET['itl__vers_filter'];
            $this->itl_comp    = $_GET['itl__comp_filter'];
            $this->itl_block   = $_GET['itl__block_filter'];
            $this->itl_assi    = $_GET['itl__assi_filter'];
            $this->itl_reporter= $_GET['itl__user_filter'];
            $this->itl_myis    = $_GET['itl_myis_filter'];
            $this->project     = $_GET['itl_project'];
            $this->it_th_cols  = $_POST['it_th_cols'];
         }
         elseif ($event->data === 'showmodlog') {
            $this->parameter   = $_GET['showid'];
            $this->project     = $_GET['project'];
         }
         elseif ($event->data === 'savecfgelement') {
          $this->parameter     = "addcfgelement";
          $this->elmnt_name    = $_POST['name1'];
          $this->elmnt_type    = $_POST['type1'];
         }
         elseif ($event->data === 'savecfgmatrix') {
          $this->parameter     = "cfgmatrix";
          $this->elmnt_type    = $_POST['type2'];
          $this->elmnt_name    = $_POST['name2'];
          $this->elmnt_childs  = $_POST['childs2'];
         }
         elseif ($event->data === 'deletecfgelement') {
          $this->parameter     = "deletecfgelement";
          $this->elmnt_type    = $_POST['type3'];
          $this->elmnt_name    = $_POST['name3'];
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
        $tmp = explode(chr(10),$txt);
        foreach($tmp as $line) {
          if((stripos($line,'ul]')!==false) || (stripos($line,'ol]')!==false) || (stripos($line,'li]')!==false)) {
              $res .= $line;
          }
          else $res .= $line."<br />";
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
             global $ID;
             
         if (($data->data == 'showcase') || ($data->data == 'showcaselink') || ($data->data == 'store_resolution') || ($data->data == 'store_workaround')) {
             $data->preventDefault();
    //        if ($mode == 'xhtml'){            
                 $renderer->info['cache'] = false;         
                 $issue_id = $this->parameter;
                 $project  = $this->project;                 
                 $user_grp = pageinfo();        
                 $usr      = $user_grp['userinfo']['name'] ;  //to log issue mods
                 $Generated_Header  = '';
                 $Generated_Message = '';

                 // get issues file contents
                 if($this->getConf('it_data')==false) $pfile = DOKU_CONF."../data/meta/".$project.'.issues';
                 else $pfile = DOKU_CONF."../". $this->getConf('it_data').$project.'.issues';

                 if (is_file($pfile))
                	 {  $issues  = unserialize(@file_get_contents($pfile));}
                 elseif(strlen($project)>1)
                	 {// promt error message that issue with ID does not exist   
                      echo '<div class="it__negative_feedback">'.sprintf($this->getLang('msg_pfilemissing'), $project) . '</div><br />';
                   }	                              
                 
                 // showcase can refer to multiple issues in the event of multi-project tuned on
                 // 1. check if multiproject is on
                 if(($data->data == 'showcase') && ($this->getConf('multi_projects')!== false)) {
                   // 2. get list of projects and issues
                   $issues = $this->_get_issues($project, true);

                   // 3. filter for related issue id
                   foreach($issues as $issue) {
                      if($issue['id']==$issue_id) {
                        $tmp[] = $issue;
                        $pstring = sprintf("showid=%s&amp;project=%s", urlencode($issue['id']), urlencode($issue['project']));
                        $p = $p + 1;
                        $referrer = "p".$p;
                        $itl_item_title .= '<label for="'.$referrer.'">'.$issue['project'].': </label>&nbsp'.
                                           '<a  name="'.$referrer.'"   id="'.$referrer.'" href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$issue['title'].'">'.$issue['title'].'</a><br />';
                      }   
                   }
                   $issues = $tmp;
                   if(count($issues)>1) {
                       // list issues
                       echo $this->getLang('msg_showCase') . '<br />';
                       echo $itl_item_title;
                       return;
                   }
                   elseif(count($issues)===1) {
                     // just one issue but of which project ?
                      $project = $issue['project'];
                   }
                 }

                 //If comment to be deleted
                 elseif ($_REQUEST['del_cmnt']==='TRUE') {
                     // check if captcha is to be used by issue tracker in general
                     if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                     else { $captcha_ok = ($this->_captcha_ok());}
                     if ($captcha_ok)
                     {    if (checkSecurityToken())
                          {  // get comment file contents
                             if($this->getConf('it_data')==false) $comments_file = DOKU_CONF."../data/meta/".$project."_".$_REQUEST['comment_issue_ID'].'.cmnts';
                             else $comments_file = DOKU_CONF."../". $this->getConf('it_data').$project."_".$_REQUEST['comment_issue_ID'].'.cmnts';
                             if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                             else  {  $txt='Comments file does not exist.';  }
                             // delete fieldset from $comments array
                             $comment_id = htmlspecialchars(stripslashes($_REQUEST['comment_id']));
                             $comment_id = htmlspecialchars($_REQUEST['comment_id']);
                             //$comments[$comment_id]
                             unset($comments[$comment_id]);
                             // store comments to file
                             $xvalue = io_saveFile($comments_file,serialize($comments));
                             if($this->getConf('mail_modify_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id],'delete');
                             //sprintf($format, $num
                             msg(sprintf($this->getLang('msg_commentdeltrue'),$comment_id),1);
//                             $Generated_Header = '<div class="it__positive_feedback">'.sprintf($this->getLang('msg_commentdeltrue'),$comment_id).'</div><br />';
                          }
                      }
                 }
                 //Comment to be added  or modified
                 elseif ((isset($_REQUEST['comment'])) || (isset($_REQUEST['comment_id']))) 
                 {  if ((($_REQUEST['comment']) || (isset($_REQUEST['comment_id']))) && (isset($_REQUEST['comment_issue_ID'])))
                       {        
                       // check if captcha is to be used by issue tracker in general
                       if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                       else { $captcha_ok = ($this->_captcha_ok());}

                       if (@file_exists($pfile))
                    	 {  $issues  = unserialize(@file_get_contents($pfile)); }
                        else
                    	 {  msg('Issue file not found !'.NL.$pfile,-1);
                          return false; }
                       
                       if ($captcha_ok)
                             {                           
                                if (checkSecurityToken())
                                {  // get comment file contents
                                   if($this->getConf('it_data')==false) $comments_file = DOKU_CONF."../data/meta/".$project."_".$_REQUEST['comment_issue_ID'].'.cmnts';
                                   else $comments_file = DOKU_CONF."../". $this->getConf('it_data').$project."_".$_REQUEST['comment_issue_ID'].'.cmnts';
            
                                   if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                                   else  {  $comments = array();  }
                                   $checkFlag=false;
                                   
                                   //Add new comment to the comment file
                                   $comment_id=count($comments);
                                   // check if comment content already exist
                                   foreach ($comments as $value)
                                       {  if ($value['id'] >= $comment_id) { $comment_id=$value['id'] + 1; }
                                          if ($_REQUEST['comment'] === $value['comment']) 
                                          {   msg($this->getLang('msg_commentfalse').'.',-1);
//                                              $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_commentfalse').'</div><br />';
                                              $checkFlag=true; 
                                          }
                                       }
                                   //If comment to be modified
                                   if (($checkFlag === false) && (isset($_REQUEST['comment_id'])))
                                   { $comment_id = htmlspecialchars(stripslashes($_REQUEST['comment_id']));
                                     if ($_REQUEST['comment_mod'] === $comments[$comment_id]['comment']) 
                                        {   msg($this->getLang('msg_commentfalse').$comment_id.'.',-1);
//                                            $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_commentmodfalse').$comment_id.'</div><br />';
                                            $checkFlag=true; 
                                        }
                                     else
                                     {  $cur_date = date($this->getConf('d_format'));
                                        $comments[$comment_id]['mod_timestamp'] = $cur_date;
//                                        $comments[$comment_id]['comment'] = htmlspecialchars(stripslashes($_REQUEST['comment_mod']));
                                        $comments[$comment_id]['comment'] = htmlspecialchars($_REQUEST['comment_mod']);
//                                        $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commentmodtrue').$comment_id.'.</div><br />';
                                        //Create comments file
                                        $xvalue = io_saveFile($comments_file,serialize($comments));
                                        if(($this->getConf('mail_modify_comment') ===1) && ($_REQUEST['minor_mod']!=="true")) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'modify');
                                        msg($this->getLang('msg_commenttrue').$comment_id.'.',1);
//                                        $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commenttrue').$comment_id.'.</div><br />';
                                      }
                                   }
                                   //If comment to be added
                                   elseif ($checkFlag === false)
                                   {   $comments[$comment_id]['id'] = $comment_id;
                                       $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                                       $cur_date = date($this->getConf('d_format'));
                                       $comments[$comment_id]['timestamp'] = $cur_date;
//                                       $comments[$comment_id]['comment'] = htmlspecialchars(stripslashes($_REQUEST['comment']));
                                       $comments[$comment_id]['comment'] = htmlspecialchars($_REQUEST['comment']);
                                       //Create comments file
                                       $xvalue = io_saveFile($comments_file,serialize($comments)); 
                                       if($this->getConf('mail_add_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'new');
                                       msg($this->getLang('msg_commenttrue').$comment_id.'.',1);
//                                       $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commenttrue').$comment_id.'.</div><br />';
                                    }
                                    // update issues modification date
                                    if ($checkFlag === false)
                                    {   // inform user (or assignee) about update
                                        // update modified date
                                        $cur_date = date($this->getConf('d_format'));
                                        $issues[$_REQUEST['comment_issue_ID']]['modified'] = $cur_date; 
                                        $xvalue = io_saveFile($pfile,serialize($issues));
                                        // if($this->getConf('mail_modify_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'modify');
                                        $anker_id = 'resolved_'. uniqid((double)microtime()*1000000,1);                                   
                                    }
                                 }
                            }
                       }
                 }
                 elseif (isset($_REQUEST['mod_add_data']))
                 {
                    $old_value = '<u>Planning info modified:</u><br />'.
                                  'Component: '.$issues[$issue_id]['component'].'<br />'.
                                  'Target version: '.$issues[$issue_id]['trgt_version'].'<br />'.
                                  'Test blocking: '.$issues[$issue_id]['tblock'].'<br />'.
                                  'Begin: '.$issues[$issue_id]['dev_start'].'<br />'.
                                  'Deadline: '.$issues[$issue_id]['dev_deadline'].'<br />'.
                                  'Progress: '.$issues[$issue_id]['dev_progress'];
                    
                    $issues[$issue_id]['component']     = $_REQUEST['new_component'];
                    $issues[$issue_id]['trgt_version']  = $_REQUEST['trgt_version'];
                    $issues[$issue_id]['tblock']        = $_REQUEST['itl__block_filter'];
                    $issues[$issue_id]['dev_start']     = $_REQUEST['dev_start'];
                    $issues[$issue_id]['dev_deadline']  = $_REQUEST['dev_deadline'];
                    $issues[$issue_id]['dev_progress']  = $_REQUEST['dev_progress'];

                    //save issue-file
                    $xvalue = io_saveFile($pfile,serialize($issues));
                    $new_value = '<u>Planning info modified:</u><br />'.
                                  'Component: '.$issues[$issue_id]['component'].'<br />'.
                                  'Target version: '.$issues[$issue_id]['trgt_version'].'<br />'.
                                  'Test blocking: '.$issues[$issue_id]['tblock'].'<br />'.
                                  'Begin: '.$issues[$issue_id]['dev_start'].'<br />'.
                                  'Deadline: '.$issues[$issue_id]['dev_deadline'].'<br />'.
                                  'Progress: '.$issues[$issue_id]['dev_progress'];
                    
//                    echo $issue_id.' ... '.$old_value.'<br>'.'<br>'.$new_value.'<br>';                    
                    
                    $this->_log_mods($project, $issues[$issue_id], $usr, 'planning data', $old_value, $new_value);
                 }  
                  elseif (isset($_REQUEST['mod_description']))
                 {  
                    // check if captcha is to be used by issue tracker in general
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    
                    if ($captcha_ok)
                    {   if (checkSecurityToken())
                        {   // find issue and description
                              $issue_id = trim($_REQUEST['comment_issue_ID']);
                              $cFlag = false;
     
                              foreach ($issues as $value)
                              { $ist = $value['id'];
                                if ($value['id'] == $issue_id) 
                                { $cFlag = true;
                                  break;}
                              }

                              if ($cFlag === true)
                              {   $old_value = $issues[$issue_id]['description'];
//                                  $issues[$issue_id]['description'] = htmlspecialchars(stripslashes($_REQUEST['description_mod']));
                                  $issues[$issue_id]['description'] = htmlspecialchars($_REQUEST['description_mod']);
                                  //save issue-file
                                  $xvalue = io_saveFile($pfile,serialize($issues));
                                  if(($this->getConf('mail_modify__description') ===1) && ($_REQUEST['minor_mod']!=="true")) $this->_emailForDscr($_REQUEST['project'], $issues[$issue_id]);
                                  $this->_log_mods($project, $issues[$issue_id], $usr, 'description mod', $old_value, $issues[$issue_id]['description']);
                                  msg($this->getLang('msg_descrmodtrue'),1);
                      //            $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_descrmodtrue').$issue_id.'</div>';
                              }
                              else { msg("Issue with ID: $issue_id not found.",-1); }
                        }
                    }
                 }  
                 elseif(isset($_REQUEST['mod_contacts']))
                 {  // check if captcha is to be used by issue tracker in general
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    if ($captcha_ok)
                    {   if (checkSecurityToken())
                        {   // find issue and description
                            $issue_id = trim($_REQUEST['issue_ID']);
                            $a1 = $_REQUEST['add_contact'];
                            $a1 = preg_replace("/ +/", ',', $a1);
                            $a1 = preg_replace('/;/',',',$a1); 
                            $a1 = preg_replace('/,+/',',',$a1);
                            $xvalue = false;
                            //check if register or unregister a follower
                            if((strlen($a1)>0) && (stripos($issues[$issue_id]['add_user_mail'],$a1) === false)) {
                                $issues[$issue_id]['add_user_mail'] .= ",".$a1; 
                                //save issue-file
                                $xvalue = io_saveFile($pfile,serialize($issues));
                                if($xvalue!==false) { msg(sprintf($this->getLang('msg_addFollower_true'),$issue_id).$a1,1);}
                                $this->_log_mods($project, $issues[$issue_id], $usr, 'follower added', '', $a1);
                            }
                            // delete mail address from followers
                            elseif((strlen($a1)>0) && (stripos($issues[$issue_id]['add_user_mail'],$a1) !== false)) {
                                $tmp = explode(',', $issues[$issue_id]['add_user_mail']);
                                foreach($tmp as $email) {
                                    if (stripos($email,$a1) === false) $ret_mails .= $email.',';
                                } 
                                //save issue-file
                                $issues[$issue_id]['add_user_mail'] = $ret_mails;
                                $xvalue = io_saveFile($pfile,serialize($issues));
                                if($xvalue!==false) { msg(sprintf($this->getLang('msg_rmvFollower_true'),$issue_id).$a1,1);}
                                $this->_log_mods($project, $issues[$issue_id], $usr, 'follower deleted', $a1, '');
                            }
                            if($xvalue===false) { msg(sprintf($this->getLang('msg_addFollower_failed'),$issue_id).$a1,-1); }
                        }
                    }
                 }
                 elseif(isset($_REQUEST['mod_symptomlinks']))
                 {
                    // check if captcha is to be used by issue tracker in general
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    
                    if ($captcha_ok)
                    {   if (checkSecurityToken())
                        {   // find issue and description
                              $issue_id = trim($_REQUEST['comment_issue_ID']);
                              $cFlag = false;
     
                              foreach ($issues as $value)
                              { if ($value['id'] == $issue_id) 
                                { $cFlag = true;
                                  break;}
                              }

                              if ($cFlag === true) {   
// *****************************************************************************
// upload a symptom file
// *****************************************************************************
                                    $mime_type1 = $_FILES['attachment1']['type'];
                                    if(($this->getConf('upload')> 0) && (strlen($mime_type1)>1)) {
                                      $Generated_Header = $this->_symptom_file_upload($issues,$issue_id,'attachment1');
                                    }
                                    $mime_type2 = $_FILES['attachment2']['type'];
                                    if(($this->getConf('upload')> 0) && (strlen($mime_type2)>1)) {
                                      $Generated_Header = $this->_symptom_file_upload($issues,$issue_id,'attachment2');
                                    }
                                    $mime_type3 = $_FILES['attachment3']['type'];
                                    if(($this->getConf('upload')> 0) && (strlen($mime_type3)>1)) {
                                      $Generated_Header = $this->_symptom_file_upload($issues,$issue_id,'attachment3');
                                    }
                                                                      //save issue-file
                                   $xvalue = io_saveFile($pfile,serialize($issues));
                                   $new_value = $issues[$issue_id]['attachment1'].'<br />'.$issues[$issue_id]['attachment2'].'<br />'.$issues[$issue_id]['attachment3'];
                                   $this->_log_mods($project, $issues[$issue_id], $usr, 'symptom links', $old_value, $new_value);
                              }
                              else { msg("Issue with ID: $issue_id not found.",-1); }
                        }
                    }
                 }                 
                 elseif (isset($_REQUEST['add_resolution'])) 
                 {  $renderer->info['cache'] = false;     
          
                    if (@file_exists($pfile))
                    	{ $issues  = unserialize(@file_get_contents($pfile)); }
                    else
                    	{ msg('Issue file not found !'.NL.$pfile,-1);
                        return false; }            	          

                    // check if captcha is to be used by issue tracker in general
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    
                    if ($captcha_ok)
                      {   if (checkSecurityToken())
                          {   //Add resolution text to the issue file and set the issue to solved
                              $issue_id = trim($_REQUEST['comment_issue_ID']);
                              $cFlag = false;
     
                              foreach ($issues as $value)
                                  { $ist = $value['id'];
                                    if ($value['id'] == $issue_id) 
                                    { $cFlag = true;
                                      break;}
                                  }

                              if ($cFlag === true)
                              {   $old_value                       = $issues[$issue_id]['resolution'];
//                                  $issues[$issue_id]['resolution'] = htmlspecialchars(stripslashes($_REQUEST['x_resolution']));
                                  $issues[$issue_id]['resolution'] = htmlspecialchars($_REQUEST['x_resolution']);
                                  $issues[$issue_id]['status']     = $this->getLang('issue_resolved_status');
                                  $xuser                           = $issues[$issue_id]['user_mail'];
                                  $xdescription                    = $issues[$issue_id]['description'];

                                  //save issue-file
                                  $xvalue                          = io_saveFile($pfile,serialize($issues));
                                  $anker_id                        = 'resolved_'. uniqid((double)microtime()*1000000,1);                                   
                                  if($this->getConf('mail_modify_resolution') ===1) $this->_emailForRes($_REQUEST['project'], $issues[$_REQUEST['comment_issue_ID']]);
//                                  $Generated_Message               = '<div class="it__positive_feedback"><a href="#'.$anker_id.'"></a>'.$this->getLang('msg_resolution_true').$issue_id.'</div>';
                                  msg($this->getLang('msg_resolution_true').$issue_id.'.',1);
                                  $usr                             = $_POST['usr'];                                                                    
                                  $this->_log_mods($project, $issues[$issue_id], $usr, 'resolution', $old_value, $issues[$issue_id]['resolution']);
                              }
                              else { msg("Issue with ID: $issue_id not found.",-1); }
                                
                          }
                      }        
                 }
                 elseif(isset($_REQUEST['mod_severity']))
                 {  // check if captcha is to be used by issue tracker in general
//                    msg("severity mod detected",0);
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    if ($captcha_ok)
                    {   if (checkSecurityToken())
                        {    $old_value = $issues[$issue_id]['severity'];
                             $issue_id  = htmlspecialchars(stripslashes($_REQUEST['issue_ID']));
                             $project   = htmlspecialchars(stripslashes($_REQUEST['project']));
                             $usr       = htmlspecialchars(stripslashes($_REQUEST['ausr']));
                             $column    = 'severity';
                             $issues[$issue_id]['severity'] = htmlspecialchars(stripslashes($_REQUEST['new_severity']));
//                             echo 'new severity = '.$issues[$issue_id]['severity'].'<br />';
                            //save issue-file
                            $xvalue     = io_saveFile($pfile,serialize($issues));                    // $project, $issue,             $old_value, $column, $new_value              
                            if($this->getConf('userinfo_email') >0) $this->_emailForIssueMod($project, $issues[$issue_id], $old_value, $column, $issues[$issue_id]['severity']);
                            $this->_log_mods($project, $issues[$issue_id], $usr, $column, $old_value, $issues[$issue_id]['severity']);
                            msg($this->getLang('msg_severitymodtrue'),1);
                        }
                    }   
                 }
                 elseif(isset($_REQUEST['mod_status']))
                 {  // check if captcha is to be used by issue tracker in general
//                    msg("status mod detected",0);
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    if ($captcha_ok)
                    {   if (checkSecurityToken())
                        {    $old_value = $issues[$issue_id]['status'];
                             $issue_id  = htmlspecialchars(stripslashes($_REQUEST['issue_ID']));
                             $project   = htmlspecialchars(stripslashes($_REQUEST['project']));
                             $usr       = htmlspecialchars(stripslashes($_REQUEST['busr']));
                             $value     = $_REQUEST['new_status'];
                             $column    = 'status';
                             $issues[$issue_id]['status'] = htmlspecialchars(stripslashes($_REQUEST['new_status']));
//                             echo 'new status = '.$issues[$issue_id]['status'].'<br />';
                            //save issue-file
                            $xvalue     = io_saveFile($pfile,serialize($issues));                                                      
                            
                            if(($this->getConf('status_special')!=='') && (stripos($this->getConf('status_special'),$value) === false)) {
                                                                                              // $project, $issue,             $old_value, $column, $new_value
                                if($this->getConf('userinfo_email') >0) $this->_emailForIssueMod($project, $issues[$issue_id], $old_value, $column, $issues[$issue_id]['status']);
                            }
                            $this->_log_mods($project, $issues[$issue_id], $usr, $column, $old_value, $issues[$issue_id]['status']);
                            msg($this->getLang('msg_statusmodtrue'),1);
                        }
                    }
                 }
/* Workaround to be stored ---------------------------------------------------*/
                 elseif(isset($_REQUEST["mod_wround"]))
                 {  // check if captcha is to be used by issue tracker in general
                    if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                    else { $captcha_ok = ($this->_captcha_ok());}
                    if ($captcha_ok)
                    {   if (checkSecurityToken())
                        {   // find issue and description
                            if (@file_exists($pfile))
                    	      {  $issues  = unserialize(@file_get_contents($pfile)); }
                            else
                    	      {  msg('Issue file not found !'.NL.$pfile,-1);
                               return false; }
        
                             // get comment file contents
                             if($this->getConf('it_data')==false) $comments_file = DOKU_CONF."../data/meta/".$project."_".$_REQUEST['comment_issue_ID'].'.cmnts';
                             else $comments_file = DOKU_CONF."../". $this->getConf('it_data').$project."_".$_REQUEST['comment_issue_ID'].'.cmnts';
        
                             if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                             else  {  $comments = array();  }
                             $checkFlag=false;
                             $comment_id = 0;
                             $comments[$comment_id] = array();
                             $comments[$comment_id]['id'] = "WA";
                             $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                             $cur_date = date($this->getConf('d_format'));
                             $comments[$comment_id]['timestamp'] = $cur_date;
//                             $comments[$comment_id]['wround_mod'] = htmlspecialchars(stripslashes($_REQUEST['wround_mod']));
                             $comments[$comment_id]['wround_mod'] = htmlspecialchars($_REQUEST['wround_mod']);
                             //Create comments file
                             $xvalue = io_saveFile($comments_file,serialize($comments)); 
                             if($this->getConf('mail_add_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'workaround');
                             msg($this->getLang('msg_wroundtrue').'.',1);
        
                          // update issues modification date
                          if ($checkFlag === false)
                          {   // inform user (or assignee) about update
                              // update modified date
                              $cur_date = date($this->getConf('d_format'));
                              $issues[$_REQUEST['comment_issue_ID']]['modified'] = $cur_date; 
                              $xvalue = io_saveFile($pfile,serialize($issues));
                              $anker_id = 'resolved_'. uniqid((double)microtime()*1000000,1);                                   
                          }
                        }
                     }  
                 }
                 // Render 
                                                        // Array  , project name
                 $Generated_Table = $this->_details_render($issues, $project);                 
                 //$data->doc .= $Generated_Header.$Generated_Table.$Generated_feedback;
        }
/* scrolling next/previous issues --------------------------------------------*/ 
        elseif (($data->data == 'issuelist_next') || ($data->data == 'issuelist_previous') || ($data->data == 'issuelist_filter') || ($data->data == 'issuelist_filterlink'))  
        {
                 $data->preventDefault();
                 $renderer->info['cache'] = false;         
                 $project = $this->itl_pjct;
                 $itl_start = $this->itl_start;
                 $step = $this->itl_step;
                 if ($step == '') {$step=10;}
                 $itl_next = $this->itl_next;
                 $a = $this->itl_pjct;
                 if ($this->getConf('multi_projects')==0) {
                    $all = false;
                 }
                 else {
                    $all = true;  
                 }

                 // get issues file contents
                  $all = true;
                  $issues = $this->_get_issues($project, $all);
                  
                  // global sort of issues array
                  $sort_key = $this->itl_sort;
                  $issues = $this->_issues_globalsort($issues, $sort_key);

                 if ($data->data == 'issuelist_next') {
                    $start = $itl_next;
                    if ($start<0)  { $start='0'; }
                    elseif($start>count($issues)) {$start=count($issues)-step;}
                    $next_start = $start + $step;
                    if ($next_start>count($issues)) {
                      $next_start=count($issues);
                      $start = $next_start - $step;
                      if ($start<0) { $start='0'; }
                      }                              
//                    echo 'start = '.$start.';  step = '.$step.';  next_start = '.$next_start.'<br />';
                 }
                 elseif ($data->data == 'issuelist_previous') {
                    $start = $itl_start - $step;
                    if ($start<0) { $start='0'; $next_start = $start + $step;}                    
                    else $next_start = $itl_start;
                 }
                 elseif ($data->data == 'issuelist_filter') {
                    $start = $itl_start;
                    $next_start = $start + $step;                    
                    if ($next_start>count($issues)) { $next_start=count($issues); }                 
                 }
                 elseif ($data->data == 'issuelist_filterlink') {
                    $start = 0;
                    $step = count($issues);
                    $next_start = count($issues);                    
                    if ($next_start>count($issues)) { $next_start=count($issues); }                 
                 }

                $filter = array();
                $filter['status']    = $this->itl_stat;
                $filter['severity']  = $this->itl_sev;
                $filter['product']   = $this->itl_prod;
                $filter['version']   = $this->itl_vers;
                $filter['component'] = $this->itl_comp;
                $filter['tblock']    = $this->itl_block;
                $filter['assignee']  = $this->itl_assi;
                $filter['reporter']  = $this->itl_reporter;
                $filter['myissues']  = $this->itl_myis;
                $filter['tblock']    = $this->itl_block;

                if ($filter['severity']   == '') { $filter['severity']   = 'ALL' ;}
                if ($filter['status']     == '') { $filter['status']     = 'ALL' ;}
                if ($filter['product']    == '') { $filter['product']    = 'ALL' ;}
                if ($filter['version']    == '') { $filter['version']    = 'ALL' ;}
                if ($filter['component']  == '') { $filter['component']  = 'ALL' ;}
                if ($filter['tblock']     == '') { $filter['tblock']     = false ;}
                else { $filter['tblock']  = true ;}
                if ($filter['assignee']   == '') { $filter['assignee']   = 'ALL' ;}
                if ($filter['reporter']   == '') { $filter['reporter']   = 'ALL' ;}
            	  if ($filter['myissues']   == '') { $filter['myissues']   = false ;}
                else { $filter['myissues']= true ;}

                if ($filter['myissues']      == '') {$filter['myissues'] = false;}
                else {$filter['myissues'] = true;}                

                $Generated_Header  = '';                       
                $Generated_Table   = $this->_table_render($this->project,$a,$step,$start,$next_start,$filter,$all); 
                $Generated_Scripts = $this->_scripts_render($project);
        }
        elseif ($data->data == 'showmodlog'){
            global $auth;
            $data->preventDefault();
            $issue_id = $this->parameter;
            $project = $this->project;
            $Generated_Header  = '';
            $Generated_Scripts = '';
            $Generated_Report  = '';
            $Generated_Message = '';

            // get mod-log file contents
            if($this->getConf('it_data')==false) $modfile = DOKU_CONF."../data/meta/".$project.'_'.$issue_id.'.mod-log';
            else $modfile = DOKU_CONF."../". $this->getConf('it_data').$project.'_'.$issue_id.'.mod-log';
            if (@file_exists($modfile))
                {$mods  = unserialize(@file_get_contents($modfile));}
            else 
                {msg('No Modification log file found for this issue',-1);
                 return;}

            $Generated_Table  .= '<h1>'.$this->getLang('h_modlog').$issue_id.'</h1>';
            $Generated_Table  .= '<div class="dokuwiki"><table class="inline tbl_showmodlog">'.NL;
            $Generated_Table  .= '<tr><th>Date</th><th>User</th><th>Field</th><th>old Value</th><th>new Value</th></tr>'.NL;

            foreach($mods as $mod) {          
                
                if($rowEven==="it_roweven") $rowEven="it_rowodd";
                else $rowEven="it_roweven";
                        
                $Generated_Table  .= '<tr class="'.$rowEven.'" >'.NL;
                $Generated_Table  .= '  <td>'.date($this->getConf('d_format'),strtotime($this->_get_one_value($mod,'timestamp'))).'</td>'.NL;
                $Generated_Table  .= '  <td>'.$this->_get_one_value($mod,'user').'</td>'.NL;
                $Generated_Table  .= '  <td>'.$this->_get_one_value($mod,'field').'</td>'.NL;
                $Generated_Table  .= '  <td>'.$this->_get_one_value($mod,'old_value').'</td>'.NL;
                
                $__assigened       = $this->_get_one_value($mod,'new_value');
                $__assigened       = $this->xs_format($__assigened);
                if((stripos($this->_get_one_value($mod,'field'),'assign')!== false) && ($this->getConf('auth_ad_overflow') == false)) {

                    $filter['grps']=$this->getConf('assign');
                    $target = $auth->retrieveUsers(0,0,$filter);
          
                    foreach($target as $_assignee)
                    { if($_assignee['mail'] === $__assigened) {   
                        $__assigened = $_assignee['name'];
                        break; }
                    }
                }
                $Generated_Table  .= '  <td>'.$__assigened.'</td>'.NL;
                $Generated_Table  .= '</tr>'.NL;
            }

            $Generated_Table  .= '</table></div>'.NL;
            // build parameter for $_GET method
            $pstring = sprintf("showid=%s&amp;project=%s", urlencode($issue_id), urlencode($project));
            msg($ID,0);
            $itl_item_title = '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$this->getLang('back').'">'.$this->getLang('back').'</a>';
            $Generated_Table  .= $itl_item_title.NL;        
        }
        elseif ($data->data == 'it_search'){
            $data->preventDefault();
            include('itsearch.php');
        }
/*        elseif ($data->data == 'savecfgelement'){
          // add a new element to the config-matrix
          $data->preventDefault();
          $cfg_file = DOKU_PLUGIN."issuetracker/conf/it_matrix.cfg";
          $new_element = array();
          if (@file_exists($cfg_file)) { $new_element  = unserialize(@file_get_contents($cfg_file)); }    

          // elmnt_type | elmnt_name | rel_childs
          $new_element[]= array(
                                'elmnt_type' => $_POST['type1'] , 
                                'elmnt_name' => $_POST['name1'] , 
                                'rel_childs' => "");

          //save cfg-matrix file
          asort($new_element);
          $xvalue = io_saveFile($cfg_file,serialize($new_element));
          if($xvalue>0) msg("New element added to the config matrix.",1);
          else          msg("New element cannot be added to the config matrix.",-1);
          $backlink = str_replace("&do=savecfgelement","",$ID);
          echo '<a href="'.DOKU_URL.'doku.php?id='.$backlink.'">back</a> <br/>';
        }
        elseif ($data->data == 'savecfgmatrix'){
          $data->preventDefault();
          msg("implement code to store the newly added relation of an element and its childs",0);
          echo 'elmnt_type: '  .$_POST['type2'].'<br />'; 
          echo 'elmnt_name: '  .$_POST['name2'].'<br />';
          echo 'elmnt_childs 1: '.$_POST['childs_1'].'<br />';
          echo 'elmnt_childs 2: '.$_POST['childs_2'].'<br />';
          echo 'elmnt_childs 3: '.$_POST['childs_3'].'<br />';
          echo 'elmnt_childs 4: '.$_POST['childs_4'].'<br />';
          echo 'elmnt_childs 5: '.$_POST['childs_5'].'<br />';
        }
        elseif ($data->data == 'deletecfgelement'){
          $data->preventDefault();
//          msg("implement code to delete an existing element and all its references",0);
//          echo 'elmnt_type: '.$_POST['type3'].'<br />'; 
//          echo 'elmnt_name: '.$_POST['name3'].'<br />';
          // open cfg-matrix
          $data->preventDefault();
          $cfg_file = DOKU_PLUGIN."issuetracker/conf/it_matrix.cfg";
          $elements = array();
          if (@file_exists($cfg_file)) { $elements  = unserialize(@file_get_contents($cfg_file)); }
          else return;    
          // loop through matrix elements and delete all references + element themselves
          foreach($elements as $key => &$item) {
            if (($item['elmnt_type']===$_POST['type3']) & ($item['elmnt_name']===$_POST['name3'])) {
                // delete completely from array
                unset($elements[$key]);
            }
            if(stripos($item['rel_childs'],$_POST['name3'])!==false){
              $temp = explode(",",$item['rel_childs']);
              foreach($temp as $k => $i){
                if($i===$_POST['name3']) unset($temp[$k]);
              }
              $item['rel_childs'] = implode(",",$temp);
            }
          }
          //save cfg-matrix file
          $xvalue = io_saveFile($cfg_file,serialize($elements));
          if($xvalue>0) msg("Element completely deleted from config matrix.",1);
          else          msg("Element cannot be deleted from config matrix.",-1);
          $backlink = str_replace("&do=savecfgelement","",$ID);
          echo '<a href="'.DOKU_URL.'doku.php?id='.$backlink.'">back</a> <br/>';
        }
*/
        else return;
        
        // Render            
        echo $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report.$Generated_Message;
    }
/******************************************************************************/
/* Create table scripts
*/
    function _scripts_render($project)
    {
        // load status values from config into select control
        $s_counter = 0;
        $status = explode(',', $this->getConf('status')) ;
        foreach ($status as $x_status)
        {
            $s_counter = $s_counter + 1;
            $x_status = trim($x_status);
            $STR_STATUS = $STR_STATUS . "case '".$x_status."':  val = ".$s_counter."; break;";
            $pattern = $pattern . "|" .  $x_status;
            $x_status_select = $x_status_select . "['".$x_status."','".$x_status."'],";
        }
        
        // Build string to load products select
        $products = explode(',', $this->getConf('products')) ;
        foreach ($products as $x_products)
        {
            $x_products = trim($x_products);
            $x_products_select = $x_products_select . "['".$x_products."','".$x_products."'],";
        } 
        
        // Build string to load severity select
        $severity = explode(',', $this->getConf('severity')) ;
        foreach ($severity as $x_severity)
        {
            $x_severity = trim($x_severity);
            $x_severity_select = $x_severity_select . "['".$x_severity."','".$x_severity."'],";
        } 
        
        // see issue 37: AUTH:AD switch to provide text input instead 
        // select with retriveing all_users from AD
        if($this->getConf('auth_ad_overflow') == false) {
            global $auth;
            global $conf;
            $filter['grps']  = $this->getConf('assign');
            $target          = $auth->retrieveUsers(0,0,$filter);
            $shw_assignee_as = $this->getConf('shw_assignee_as');
            if(stripos("login, mail, name",$shw_assignee_as) === false) $shw_assignee_as = "login";
//--------------------------------------------------------------------------------------------
// Build 'assign to' list from a simple textfile
        	  // 1. check if file exist else use configuration 
        	  if($this->getConf('assgnee_list')==="") {
                foreach ($target as $key => $x_umail)
                {       // show assignee by login, name, mail
                        if($shw_assignee_as=='login') $x_umail_select = $x_umail_select . "['".$key."','".$x_umail['mail']."'],";
                        else $x_umail_select = $x_umail_select . "['".$x_umail[$shw_assignee_as]."','".$x_umail['mail']."'],";
                }      
            }
            else{
                $x_umail_select = __get_assignees_from_files($this->getConf('assgnee_list'));                	 
              }
//--------------------------------------------------------------------------------------------

            $x_umail_select .= "['',''],";
            $authAD_selector = "TableKit.Editable.selectInput('assigned',{}, [".$x_umail_select."]);";
        }

        //hack if DOKU_BASE is not properly set
        if(strlen(DOKU_BASE) < strlen(DOKU_URL)) $BASE = DOKU_URL."lib/plugins/issuetracker/";
        else $BASE = DOKU_BASE."lib/plugins/issuetracker/";

        return    "<script type=\"text/javascript\" src=\"".$BASE."prototype.js\"></script><script type=\"text/javascript\" src=\"".$BASE."fabtabulous.js\"></script>
        <script type=\"text/javascript\" src=\"".$BASE."tablekit.js\"></script>
        <script type=\"text/javascript\">
            TableKit.options.editAjaxURI = '".$BASE."edit.php';
            TableKit.Editable.selectInput('status',{}, [".$x_status_select."]);
            TableKit.Editable.selectInput('product',{}, [".$x_products_select."]);
            TableKit.Editable.selectInput('severity',{}, [".$x_severity_select."]);
            ".$authAD_selector."
            TableKit.Editable.multiLineInput('description');
            TableKit.Editable.multiLineInput('resolution');
            var _tabs = new Fabtabs('tabs');
            $$('a.next-tab').each(function(a) {
                Event.observe(a, 'click', function(e){
                    Event.stop(e);
                    var t = $(this.href.match(/#(\w.+)/)[1]+'-tab');
                    _tabs.show(t);
                    _tabs.menu.without(t).each(_tabs.hide.bind(_tabs));
                }.bindAsEventListener(a));
            });
        </script>";
    }
/******************************************************************************/
/* Create list of next/previous Issues
*/                         
    function _table_render($project,$a,$step,$start,$next_start,$filter,$all=false)
    {
        global $ID;
        $imgBASE       = DOKU_BASE."lib/plugins/issuetracker/images/";
        $style         = ' style="text-align:center; white-space:pre-wrap;">';
        $user_grp      = pageinfo();
        $noStatIMG     = $this->getConf('noStatIMG');
        $noSevIMG      = $this->getConf('noSevIMG');

        // get issues file contents
        //$all = true;
        $issues = $this->_get_issues($project, $all);       
        // global sort of issues array
        $sort_key = $this->itl_sort;
        $issues = $this->_issues_globalsort($issues, $sort_key);
        // global sort of issues array
        $sort_key = $this->itl_sort;
        $issues = $this->_issues_globalsort($issues, $sort_key);
        // 
        $dynatable_id = "t_".uniqid((double)microtime()*1000000,1);
        
        if ($start>count($issues)) $start=count($issues)-$step;                
        if(array_key_exists('userinfo', $user_grp))
        {
            foreach ($user_grp['userinfo']['grps'] as $ugrp)
            {
                $user_grps = $user_grps . $ugrp;
            }
        }
        else
        {   $user_grps = 'all';  }

        $ret = '<br /><br /><form class="issuetracker__form2" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'"><p>';
        $ret .= formSecurityToken(false).'<input type="hidden" name="do" value="show" />';        

        // the user maybe member of different user groups
        // check if one of its assigned groups match with configuration
        $allowed_users = explode('|', $this->getConf('assign'));
        $cFlag = false;
        foreach ($allowed_users as $w) 
        { // check if one of the assigned user roles does match with current user roles

            if (strpos($user_grps,$w)!== false)
            {   $cFlag = true;
                break;  } 
        }      
        // members of defined groups allowed $user_grps changing issue contents 
        if (($cFlag === true) || ($this->getConf('registered_users')== 0))      
        {   
            if(($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) { $th_project = "<th id='project'>".$this->getLang('th_project')."</th>"; }
            $head = "<div class='itl__table'><table id='".$dynatable_id."' class='sortable editable resizable inline' width='100%'>".NL.
                    "<thead><tr>".NL.
                     $th_project.NL.
                    "<th class='".$this->getConf('listview_sort')."' id='id'>".$this->getLang('th_id')."</th>".NL.
                    "<th id='created'>"   .$this->getLang('th_created')   ."</th>".NL.
                    "<th id='product'>"   .$this->getLang('th_product')   ."</th>".NL.
                    "<th id='version'>"   .$this->getLang('th_version')   ."</th>".NL.
                    "<th id='severity'>"  .$this->getLang('th_severity')  ."</th>".NL.
                    "<th id='status'>"    .$this->getLang('th_status')    ."</th>".NL.
                    "<th id='user_name'>" .$this->getLang('th_user_name')  ."</th>".NL.
                    "<th id='title'>"     .$this->getLang('th_title')     ."</th>".NL.
                    "<th id='assigned'>"  .$this->getLang('th_assigned')  ."</th>".NL. 
                    "<th id='resolution'>".$this->getLang('th_resolution')."</th>".NL.
                    "<th id='modified'>"  .$this->getLang('th_modified')  ."</th>".NL.
                    "</tr></thead>".NL;        
            $body = '<tbody>'.NL;
        
            // Note: The checked attribute is a boolean attribute. 
            // It is enough if checked is mentioned to hook the checkbox !
            if($filter['myissues'] == '') { $filter['myissues'] = false; }
            else { $filter['myissues'] = true; }
            $max_vissible = $step;

            for ($i=$next_start-1;$i>=0;$i=$i-1)
            {   // check start and end of rows to be displayed
                if($i>count($issues)) break;  // prevent the php-warning
                if(count($issues)> $i) {
                    $issue = $issues[$i];
                    $a_project    = strtolower($this->_get_one_value($issue,'project'));
                    $a_status     = strtolower($this->_get_one_value($issue,'status'));
                    $a_severity   = strtolower($this->_get_one_value($issue,'severity'));
                    $a_product    = strtoupper($this->_get_one_value($issue,'product'));
                    $a_version    = strtoupper($this->_get_one_value($issue,'version'));
                    $a_component  = strtoupper($this->_get_one_value($issue,'component'));
                    $a_tblock     = strtoupper($this->_get_one_value($issue,'tblock'));
                    $a_assignee   = strtoupper($this->_get_one_value($issue,'assignee'));
                    $a_reporter   = strtoupper($this->_get_one_value($issue,'user_name'));

                    if ((($filter['status']    =='ALL') || (stristr($filter['status'],$a_status)          != false)) && 
                        (($filter['severity']  =='ALL') || (stristr($filter['severity'],$a_severity)      != false)) && 
                        (($filter['product']   =='ALL') || (stristr($filter['product'],$a_product)        != false)) &&
                        (($filter['version']   =='ALL') || (stristr($filter['version'],$a_version)        != false)) &&
                        (($filter['component'] =='ALL') || (stristr($filter['component'],$a_component)    != false)) &&
                        (($filter['assignee']  =='ALL') || (stristr($filter['assignee'],$a_assignee)      != false)) &&
                        (($filter['reporter']  =='ALL') || (stristr($filter['reporter'],$a_reporter)      != false)) &&
                        (($filter['tblock']  == false)   || (($a_tblock != false) && ($filter['tblock'] == true))) &&
                        (($data['myissues']  == false  ) || ($this->_find_myissues($issue, $user_grp) == true)))
                   {   
                        
                        if ($y>=$step) break;
                        if ((stripos($this->getConf('status_special'),$a_status) !== false) && (stripos($filter['status'],$this->getConf('status_special')) === false)) continue;                   
                        $y=$y+1;
                        // check if status image or text to be displayed
                        if ($noStatIMG == false) {                    
                            $status_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($a_status))).'.gif';
//                          if(!file_exists(str_replace("//", "/", DOKU_INC.$status_img)))  { $status_img = $imgBASE . 'status.gif' ;}
                            $status_img ='  class="it_center"><span style="display : none;">'.$a_status.'</span><img border="0" alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16">';
                        }                    
                        else { $status_img = $style.strtoupper($a_status); }
                        // check if severity image or text to be displayed                                            
                        if ($noSevIMG == false) {                    
                            $severity_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($a_severity))).'.gif';
        
//                          if(!file_exists(str_replace("//", "/", DOKU_INC.$severity_img)))  { $severity_img = $imgBASE . 'status.gif' ;}
                            $severity_img ='  class="it_center"><span style="display : none;">'.$a_severity.'</span><img border="0" alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16">';
                        }
                        else { $severity_img = $style.strtoupper($a_severity); }
                        
                        
                        $it_issue_username = $this->_get_one_value($issue,'user_name');
                        $a_project = $this->_get_one_value($issue,'project');
                        if(($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) 
                        {   $td_project = '<td class="itl__td_standard">'.$a_project.'</td>'; }
                        elseif($this->getConf('multi_projects')!==0)
                        {   $td_project = ""; }
                        
                        // build parameter for $_GET method
//                            $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($this->_get_one_value($issue,'project')));
                            $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($a_project));
                            $itl_item_title = '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$this->_get_one_value($issue,'title').'">'.$this->_get_one_value($issue,'title').'</a>';
                        
                    if((($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) || $project == $a_project  )
                    {   if($rowEven==="it_roweven") $rowEven="it_rowodd";
                        else $rowEven="it_roweven";        
                        $body .= '<tr id = "'.$a_project.' '.$this->_get_one_value($issue,'id').'" class="'.$rowEven.'" >'.NL.
                                  $td_project.NL.              
                                 '<td class="itl__td_standard">'.$this->_get_one_value($issue,'id').'</td>'.NL.
                                 '<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'created'))).'</td>'.NL.
                                 '<td class="itl__td_standard">'.$this->_get_one_value($issue,'product').'</td>'.NL.
                                 '<td class="itl__td_standard">'.$this->_get_one_value($issue,'version').'</td>'.NL.
                                 '<td'.$severity_img.'</td>'.NL.
                                 '<td'.$status_img.'</td>'.NL.
                                 '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'user_mail').'">'.$it_issue_username.'</a></td>'.NL. 
                                 '<td class="canbreak itl__td_standard">'.$itl_item_title.'</td>'.NL;
                                 
                        // check how the assignee to be displayed: login, name or mail
                        $a_display = $this->_get_assignee($issue,'assigned');
                        $body .= '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'assigned').'">'.$a_display.'</a></td>'.NL. 
                                 '<td class="canbreak itl__td_standard">'.$this->xs_format($this->_get_one_value($issue,'resolution')).'</td>'.NL.
                                 '<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'modified'))).'</td>'.NL.
                                 '</tr>'.NL;        
                    }
                  }
                }
            } 
            $body .= '</tbody></table></div>';
        } 
        else       
        {   
            //Build table header according settings or syntax
            if(strlen($this->it_th_cols)>0) {
              $configs = explode(',', strtolower($this->it_th_cols));
            }
            else {
              $configs = explode(',', $this->getConf('shwtbl_usr')) ;
            }
            if(($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) 
            { $th_project = "<th id='project'>".$this->getLang('th_project')."</th>"; }

            $reduced_header ='';
            $reduced_header = "<div class='itl__table'><table id='".$dynatable_id."' class='sortable resizable inline' width='100%'>".NL.
                    "<thead><tr>".NL.$th_project.NL."<th class='".$this->getConf('listview_sort')."' id='id'>".$this->getLang('th_id')."</th>".NL;

            foreach ($configs as $config)
            {
                $reduced_header .= "<th id='".$config."'>".$this->getLang('th_'.$config)."</th>".NL;
            }

            $reduced_header .= "</tr></thead>".NL;

            //Build rows according settings
            $reduced_issues='';
            for ($i=$next_start-1;$i>=0;$i=$i-1)
            {   // check start and end of rows to be displayed
                if($i>count($issues)) break;  // prevent the php-warning
                if(count($issues)> $i) {
                        $issue = $issues[$i];                    
                        $a_project    = strtolower($this->_get_one_value($issue,'project'));
                        $a_status     = strtoupper($this->_get_one_value($issue,'status'))   ;
                        $a_severity   = strtoupper($this->_get_one_value($issue,'severity')) ;
                        $a_product    = strtoupper($this->_get_one_value($issue,'product'))  ;
                        $a_version    = strtoupper($this->_get_one_value($issue,'version'))  ;
                        $a_component  = strtoupper($this->_get_one_value($issue,'component'));
                        $a_tblock     = strtoupper($this->_get_one_value($issue,'tblock'))   ;
                        $a_assignee   = strtoupper($this->_get_one_value($issue,'assignee')) ;
                        $a_reporter   = strtoupper($this->_get_one_value($issue,'user_name'));
                        
                    if ((($filter['status']    =='ALL') || (stristr($filter['status'],$a_status)          != false)) && 
                        (($filter['severity']  =='ALL') || (stristr($filter['severity'],$a_severity)      != false)) && 
                        (($filter['product']   =='ALL') || (stristr($filter['product'],$a_product)        != false)) &&
                        (($filter['version']   =='ALL') || (stristr($filter['version'],$a_version)        != false)) &&
                        (($filter['component'] =='ALL') || (stristr($filter['component'],$a_component)    != false)) &&
                        (($filter['assignee']  =='ALL') || (stristr($filter['assignee'],$a_assignee)      != false)) &&
                        (($filter['reporter']  =='ALL') || (stristr($filter['reporter'],$a_reporter)      != false)) &&
                        (($filter['tblock']  == false)   || (($a_tblock != false) && ($filter['tblock'] == true))) &&
                        (($data['myissues']  == false  ) || ($this->_find_myissues($issue, $user_grp) == true)))
                    {   
                        
                        if ((stripos($this->getConf('status_special'),$a_status) !== false) && (stripos($filter['status'],$this->getConf('status_special')) === false)) continue;

                        if($this->getConf('multi_projects')!==0 || $project == $a_project  )
                        {     if ($y>=$step) break;
                              $y=$y+1;
      
                              if($rowEven==="it_roweven") $rowEven="it_rowodd";
                              else $rowEven="it_roweven";
                              
                              $reduced_issues = $reduced_issues.'<tr id = "'.$a_project.' '.$this->_get_one_value($issue,'id').'" class="'.$rowEven.'" >'.
                                                                '<td'.$style.$this->_get_one_value($issue,'id').'</td>';
                              foreach ($configs as $config)
                              {
                                  $isval = $this->_get_one_value($issue,strtolower($config));
                                  // check if status image or text to be displayed
                                  if ($config == 'status')
                                  {
                                      if ($noStatIMG == false) {                    
                                          $status_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($isval))).'.gif';
                                          $reduced_issues .='<td  class="it_center"><span style="display : none;">'.$a_status.'</span><img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"></td>';
                                      }
                                      else { $reduced_issues .= '<td'.$style.$isval; }
                                  }                                            
                                  // check if severity image or text to be displayed
                                  elseif ($config == 'severity')
                                  {
                                      if ($noSevIMG == false) {                    
                                          $severity_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($isval))).'.gif';
                                          $reduced_issues .='<td  class="it_center"><span style="display : none;">'.$a_severity.'</span><img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"></td>';
                                      }
                                      else { $reduced_issues .= '<td'.$style.$isval.'</td>'; }
                                  }
                                  elseif ($config == 'title')
                                  {   // build parameter for $_GET method
          //                            $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($this->_get_one_value($issue,'project')));
                                      $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), $a_project);
                                      $reduced_issues .='<td>'.
                                                        '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$isval.'">'.$isval.'</a></td>';
                                  }
                                  elseif ($config == 'created')
                                  {   $reduced_issues .='<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'created'))).'</td>'.NL;
                                  }
                                  elseif ($config == 'modified')
                                  {   $reduced_issues .='<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'modified'))).'</td>'.NL;
                                  }
                                  elseif ($config == 'resolution')
                                  {   $reduced_issues .='<td class="canbreak itl__td_standard">'.$this->xs_format($this->_get_one_value($issue,'resolution')).'</td>'.NL;
                                  }
                                  elseif ($config == 'description')
                                  {   $reduced_issues .='<td class="canbreak itl__td_standard">'.$this->xs_format($this->_get_one_value($issue,'description')).'</td>'.NL;
                                  }
                                  else 
                                  {   $reduced_issues .= '<td'.$style.$isval.'</td>';
                                  }
                              }
                              $reduced_issues .= '</tr>';
                          }
                    }
                }
            }
            
            $head = NL.$reduced_header.NL;
            $body = '<tbody>'.$reduced_issues.'</tbody></table></div>';          
        }
        
        if ($filter['product']==="") {$filter['product']='ALL';}
            
            if($i<=0) 
            {    $next_start = count($issues);
                 $start = $next_start - $step;
                 if($start<0) $start=0;
            }
            else {
                $start = $i;
                $next_start = $start + $step; 
                if($next_start>count($issues)) $next_start = count($issues);
            }
// -----------------------------------------------------------------------------
// Control render        
        if($this->itl_myis==false)  { $filter['myissues'] = ''; }
        else {$filter['myissues'] = "checked='true'";}                

        if($this->itl_block==false) { $filter['tblock'] = ''; }
        else $filter['tblock'] = "checked='true'";

        $a_result = $this->_count_render($issues,$start,$step,$next_start,$filter,$project);
        $li_count =$a_result[1];
        $count = $a_result[0];                

        $ret = '<div>'.NL.
               '<script  type="text/javascript">'.NL. 
               '        function changeAction(where) {'.NL. 
               '           if(where==1) {'.NL. 
               '              document.forms["myForm"].action = "doku.php?id=' . $ID . '&do=issuelist_previous";'.NL. 
               '           }'.NL. 
               '           else if(where==2){'.NL. 
               '              document.forms["myForm"].action = "doku.php?id=' . $ID . '&do=issuelist_next";'.NL. 
               '           }'.NL. 
               '           else if(where==3){'.NL. 
               '              document.forms["myForm"].action = "doku.php?id=' . $ID . '&do=issuelist_filter";'.NL. 
               '           }'.NL. 
               '           document.forms["myForm"].submit();'.NL. 
               '        }'.NL. 
               '     </script>'.NL.
               '<table class="itl__t1"><tbody>'.NL.
               '<tr class="itd__tables_tr">'.NL.
                  '<td colspan="4" align="left" valign="middle" height="40">'.NL.
                      '<label class="it__cir_projectlabel">'.sprintf($this->getLang('lbl_issueqty'),$project).$count.'</label>'.NL.
                  '</td>'.NL.
                  '<td class="itl__showdtls" rowspan="2" width="35%">'.$li_count.'</td>'.NL.
               '</tr>'.NL.

               '<tr class="itd__tables_tr">'.NL.
               '   <td align ="left" valign="top" width="15%">'.NL;

               $ret .= '   <p class="it__cir_projectlabel">'.'<label for="itl_step"           style="align:left;">'.$this->getLang('lbl_scroll')                                                 .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Severity')=== false)      $ret .= '<label for="itl_sev_filter"   style="align:left;">'.$this->getLang('lbl_filtersev')     .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Status')=== false)        $ret .= '<label for="itl_stat_filter"  style="align:left;">'.$this->getLang('lbl_filterstat')    .'</label><br />'.NL;
               $ret .= '</p><p class="it__cir_projectlabel">'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Product')=== false)       $ret .= '<label for="itl__prod_filter" style="align:left;">'.$this->getLang('lbl_filterprod')    .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Version')=== false)       $ret .= '<label for="itl__vers_filter" style="align:left;">'.$this->getLang('lbl_filtervers')    .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Component')=== false)     $ret .= '<label for="itl__comp_filter" style="align:left;">'.$this->getLang('lbl_filtercomp')    .'</label><br />'.NL;
               $ret .= '</p><p class="it__cir_projectlabel">'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Test blocking')=== false) $ret .= '<label for="itl__block_filter" style="align:left;">'.$this->getLang('lbl_filterblock')  .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Assignee')=== false)      $ret .= '<label for="itl__assi_filter" style="align:left;">'.$this->getLang('lbl_filterassi')    .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Reporter')=== false)      $ret .= '<label for="itl__user_filter" style="align:left;">'.$this->getLang('lbl_filterreporter').'</label><br />'.NL;
               $ret .= '</p><p class="it__cir_projectlabel">'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'MyIssues')=== false)             $ret .= '<label for="itl_myis_filter"  style="align:left;">'.$this->getLang('cbx_myissues')      .'</label><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Sort by')=== false)              $ret .= '<label for="it_glbl_sort"     style="align:left;">'.$this->getLang('lbl_sort')          .'</label>'.NL;
               $ret .= '</p></td>'.NL;

               $ret .= '   <td align ="left" valign="top" width="20%">'.NL.
               '    <form name="myForm" action="" method="post">'.NL.
               '       <input                          type="hidden" name="itl_start"        id="itl_start"        value="'.$start.'"/>'.NL.
               '       <input                          type="hidden" name="itl_step"         id="itl_step"         value="'.$step.'"/>'.NL.
               '       <input                          type="hidden" name="itl_next"         id="itl_next"         value="'.$next_start.'"/>'.NL.
               '       <input                          type="hidden" name="itl_project"      id="itl_project"      value="'.$project.'"/>'.NL.
               '       <input                          type="hidden" name="it_th_cols"       id="it_th_cols"       value="'.$data['columns'].'"/>'.NL.
               '       <input class="itl__buttons"     type="button" name="showprevious"                           value="'.$this->getLang('btn_previuos').'" title="'.$this->getLang('btn_previuos_title').'" onClick="changeAction(1)"/>'.NL.
               '       <input class="itl__step_input"  type="text"   name="itl_step"         id="itl_step"         value="'.$step.'"/>'.NL.
               '       <input class="itl__buttons"     type="button" name="shownext"                               value="'.$this->getLang('btn_next').'"     title="'.$this->getLang('btn_next_title').'"     onClick="changeAction(2)"/><p>'.NL;
               
                   if(stripos($this->getConf('ltdListFilters'),'Filter Severity')=== false)      $ret .= '<input class="itl__sev_filter"  type="text"      name="itl_sev_filter"   id="itl_sev_filter"   value="'.$filter['severity'].'"/><br />'.NL;                         
                   if(stripos($this->getConf('ltdListFilters'),'Filter Status')=== false)        $ret .= '<input class="itl__stat_filter" type="text"      name="itl_stat_filter"  id="itl_stat_filter"  value="'.$filter['status'].'"/><br />'.NL;
               $ret .= '</p><p>'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Product')=== false)       $ret .= '<input class="itl__prod_filter" type="text"      name="itl__prod_filter" id="itl__prod_filter" value="'.$filter['product'].'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Version')=== false)       $ret .= '<input class="itl__prod_filter" type="text"      name="itl__vers_filter" id="itl__vers_filter" value="'.$filter['version'].'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Component')=== false)     $ret .= '<input class="itl__prod_filter" type="text"      name="itl__comp_filter" id="itl__comp_filter" value="'.$filter['component'].'"/><br />'.NL;
               $ret .= '</p><p>'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Test blocking')=== false) $ret .= '<input                          type="checkbox"  name="itl__block_filter" id="itl__block_filter" '     .$filter['tblock'].' " /><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Assignee')=== false)      $ret .= '<input class="itl__prod_filter" type="text"      name="itl__assi_filter" id="itl__assi_filter" value="'.$filter['assignee'].'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Reporter')=== false)      $ret .= '<input class="itl__prod_filter" type="text"      name="itl__user_filter" id="itl__user_filter" value="'.$filter['reporter'].'"/><br />'.NL;
               $ret .= '</p><p>'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'MyIssues')=== false)             $ret .= '<input                          type="checkbox"  name="itl_myis_filter" id="itl_myis_filter" '         .$filter['myissues'].' value="'.$filter['myissues'].'" title="'.$this->getLang('cbx_myissues').'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Sort by')=== false)              $ret .= '<input class="itl__sev_filter"  type="text"      name="it_glbl_sort" id="it_glbl_sort" value="'.$glbl_sort.'"/><br />'.NL;
               $ret .= '</p><input class="itl__buttons" type="button" name="go" value="'.$this->getLang('btn_go').'" title="'.$this->getLang('btn_go').'" onClick="changeAction(3)"/><br />'.NL;
               $ret .= '</form>'.NL.                      
               '   </td>'.NL.
               '   <td width="2%">&nbsp;</td>'.NL.
               '   <td class="itl__showdtls" align ="left" width="40%">'.NL.
               '    <form  method="post" action="doku.php?id=' . $ID . '&do=showcase">'.NL.
               '       <label class="it__searchlabel">'.$this->getLang('lbl_showid').'</label>'.NL.
               '       <input class="itl__sev_filter"    type="text"   name="showid"          id="showid"          value="0"/>'.NL.
               '       <input                            type="hidden" name="project"         id="project"         value="'.$project.'"/>'.NL.
               '       <input                            type="hidden" name="itl_sev_filter"  id="itl_sev_filter"  value="'.$filter['severity'].'"/>'.NL.
               '       <input                            type="hidden" name="itl_stat_filter" id="itl_stat_filter" value="'.$filter['status'].'"/>'.NL.
               '       <input                            type="hidden" name="itl_myis_filter" id="itl_myis_filter" '.$filter['myissues'].' value="'.$filter['myissues'].'"/>'.NL.
               '       <input class="itl__showid_button" type="submit" name="showcase"        id="showcase"        value="'.$this->getLang('btn_showid').'"    title="'.$this->getLang('btn_showid_title').'"/>'.NL.
               '    </form><br />'.NL.
               '    <form  method="post" action="doku.php?id=' . $ID . '&do=it_search">'.NL.
               '       <label class="it__searchlabel">'.$this->getLang('lbl_search').'</label>'.NL.
               '       <input class="itl__sev_filter"    type="text"   name="it_str_search"   id="it_str_search"   value="'.$search.'"/>'.NL.
               '       <input                            type="hidden" name="project"         id="project"         value="'.$project.'"/>'.NL.
               '       <input class="itl__search_button" type="submit" name="searchcase"      id="searchcase"      value="'.$this->getLang('btn_search').'" title="'.$this->getLang('btn_search_title').'"/>'.NL.
               '    </form>'.NL.
               '   </td>'.NL.
               '</tr>'.NL.'</tbody>'.NL.'</table>'.NL.'</div>'.NL;

         $usr  = '<span style="display:none;" id="currentuser">'.$user_grp['userinfo']['name'].'</span>' ; // to log issue mods
         $usr .= '<span style="display:none;" id="currentID">'.urlencode($ID).'</span>' ; // to log issue mods
         $a_lang  = '<span style="display:none;" name="table_kit_OK" id="table_kit_OK">'.$this->getLang('table_kit_OK').'</span>'; // for tablekit.js
         $a_lang .= '<span style="display:none;" name="table_kit_Cancel" id="table_kit_Cancel">'.$this->getLang('table_kit_Cancel').'</span>'; // for tablekit.js

         $ret  = $a_lang.$usr.$ret.$head.$body;              
        return $ret;
    }
/******************************************************************************
**  Details form
*/
                       // Array  , project name
 function _details_render($issues, $project) {        
        // load issue details and display on page
        global $lang;
        global $auth;
        global $ID;
        $issue_id = $this->parameter;
        if (!$issue_id) { $issue_id = '0'; }
        
/*      ------------------------------------------------------------------------
 *        introduced due to issue #159
 *      ------------------------------------------------------------------------        
 *      - check ACL of current user for current ID 
 *        (AUTH_READ permission is necessary at least)                        
 *        return $ret    where $ret is empty and finally no content will be displayed */
        if(auth_quickaclcheck($ID)<1) { return $ret; }
/*      - ignore function also if page content does not contain the IssueTracker 
 *        syntax due to the function can be invoked just by action parameters
 *        what would bypass the DokuWiki ACL                                  
 *        return $ret    where $ret is empty and finally no content will be displayed */
        $rawText = rawWiki($ID);
        if(stripos($rawText, "{{issuetracker>") == false) { return $ret; }
//      ------------------------------------------------------------------------ 

       
        if ($issue_id === false) return;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
//        $user_grp = pageinfo();
        
        // get issues file contents
        $issue = $this->get_issues_file_contents($project, $issue_id);
        if((!$issue) && (strlen($project)>1)) {
          sprintf("showid=%s&amp;project=%s", urlencode($issue[$issue_id]['id']), urlencode($project));
          sprintf($this->getLang('msg_inotexisting1'),$project,$issue_id,DOKU_URL,$ID);
//          msg("The issue does not exist at the given project. <br /> Project = $project <br /> Issue ID = $issue_id <br /> <a href='".DOKU_URL.'doku.php?id='.$ID."'> << back</a>");
          return false;
        }	          
        elseif(!$issue) {
          msg($this->getLang('msg_issuemissing').$issue_id.'<br /> <a href="'.DOKU_URL.'doku.php?id='.$ID.'"> << back</a>');
          return false;
        }	          
        
        // get detail information from issue comment file
        if($this->getConf('it_data')==false) $cfile = DOKU_CONF."../data/meta/".$project."_".$issue_id.'.cmnts';
        else $cfile = DOKU_CONF."../". $this->getConf('it_data').$project."_".$issue_id.'.cmnts';
        if (@file_exists($cfile)) {$comments  = unserialize(@file_get_contents($cfile));}
        else {$comments = array();}

        $a_severity = $issue[$issue_id]['severity'];                  
        $severity_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($a_severity))).'.gif';
        $severity_img =' <img border="0" alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"> ';
        $a_status = $issue[$issue_id]['status'];
        $status_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($a_status))).'.gif';
        $status_img =' <img border="0" alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"> ';
        $a_product = $issue[$issue_id]['product'];

        //---------------------------------------------------------------------------------------------------------------------
        // do not show personal contact details if issue details not viewed by admin/assignee nor the original reporter itself
        //---------------------------------------------------------------------------------------------------------------------
        $user_mail = pageinfo();  //to get mail address of reporter
        if($this->getConf('auth_ad_overflow') == false) {
            $filter['grps']=$this->getConf('assign');
            $target = $auth->retrieveUsers(0,0,$filter);
            $target2 = $this->array_implode($target);
            $target2 = implode($target2);
        
            if((($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or 
                 (strpos($target2,$user_mail['userinfo']['mail']) != false)) && 
                ($this->getConf('shw_mail_addr') === 1))
            {   $__assigened      = $this->_get_assignee($issue[$issue_id],'assigned');
                $__assigenedaddr  = $issue[$issue_id]['assigned'];
                $__reportedby     = $issue[$issue_id]['user_name'];
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
                $mail_allowed     = true;
            }
            elseif($user_mail['userinfo']['mail']  === $issue[$issue_id]['assigned'])
            {   $__assigenedaddr = $user_mail['userinfo']['mail'];
                $__assigened     = $user_mail['userinfo']['name'];
                $__reportedby     = $issue[$issue_id]['user_name'];
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
                $mail_allowed    = true;
            }
            else
            {   foreach($target as $_assignee)
                  { if($_assignee['mail'] === $user_mail['userinfo']['mail'])
                    {   $__assigened     = $_assignee['name'];
                        $mail_allowed    = false;
                        break;
                    }
                    if($_assignee['mail'] === $issue[$issue_id]['assigned'])
                    {   $__assigened     = $_assignee['name'];
                        $mail_allowed    = false;
                        break;
                    }
                  }
                $__reportedby     = $issue[$issue_id]['user_name'];
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
            } 
        }
        else {  // auth_ad_overflow = true
                $__assigened      = $this->_get_assignee($issue[$issue_id],'assigned');
                $__assigenedaddr  = $issue[$issue_id]['assigned'];
                
                if($this->getConf('shw_mail_addr') === 1) { $__reportedby = $issue[$issue_id]['user_mail']; }
                else                                      { $__reportedby = $issue[$issue_id]['user_name']; }
                
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
                $mail_allowed     = true;
        }
                   
// scripts for xsEditor -------------------------------------------------------
$issue_edit_head .= '<span>
         <script type="text/javascript">
          function resizeBoxId(obj,size) {
              var arows = document.getElementById(obj).rows;
              document.getElementById(obj).rows = arows + size;
          }
          
          function doHLine(tag1,obj)
          {
          textarea = document.getElementById(obj);
          	// Code for IE
          		if (document.selection) 
          			{
          				textarea.focus();
          				var sel = document.selection.createRange();
          				//alert(sel.text);
          				sel.text = tag1 + sel.text;
          			}
             else 
              {  // Code for Mozilla Firefox
          		var len = textarea.value.length;
          	  var start = textarea.selectionStart;
          		var end = textarea.selectionEnd;

          		var scrollTop = textarea.scrollTop;
          		var scrollLeft = textarea.scrollLeft;

                  var sel = textarea.value.substring(start, end);
          	      //alert(sel);
          		    var rep = tag1 + sel;
                  textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);

          		textarea.scrollTop = scrollTop;
          		textarea.scrollLeft = scrollLeft;
          	}
          }

          function doAddTags(tag1,tag2,obj)
          {
          textarea = document.getElementById(obj);
          	// Code for IE
          		if (document.selection) 
          			{
          				textarea.focus();
          				var sel = document.selection.createRange();
          				//alert(sel.text);
          				sel.text = tag1 + sel.text + tag2;
          			}
             else 
              {  // Code for Mozilla Firefox
          		var len = textarea.value.length;
          	    var start = textarea.selectionStart;
          		var end = textarea.selectionEnd;

          		var scrollTop = textarea.scrollTop;
          		var scrollLeft = textarea.scrollLeft;

                  var sel = textarea.value.substring(start, end);
          	    //alert(sel);
          		var rep = tag1 + sel + tag2;
                  textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
          		
          		textarea.scrollTop = scrollTop;
          		textarea.scrollLeft = scrollLeft;
          	}
          }

          function doList(tag1,tag2,obj){
          textarea = document.getElementById(obj);

          // Code for IE
          		if (document.selection) 
          			{
          				textarea.focus();
          				var sel = document.selection.createRange();
          				var list = sel.text.split("\n");

          				for(i=0;i<list.length;i++) 
          				{
          				list[i] = "[li]" + list[i] + "[/li]";
          				}
          				sel.text = tag1 + "\n" + list.join("\n") + "\n" + tag2;

          			} else
          			// Code for Firefox
          			{

          		var len = textarea.value.length;
          	  var start = textarea.selectionStart;
          		var end = textarea.selectionEnd;
          		var i;

          		var scrollTop = textarea.scrollTop;
          		var scrollLeft = textarea.scrollLeft;          

              var sel = textarea.value.substring(start, end);
      	      //alert(sel);

          		var list = sel.split("\n");

          		for(i=0;i<list.length;i++) 
          		{
          		list[i] = "[li]" + list[i] + "[/li]";
          		}

          		var rep = tag1 + "\n" + list.join("\n") + "\n" +tag2;
          		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);

          		textarea.scrollTop = scrollTop;
          		textarea.scrollLeft = scrollLeft;
           }
          }
          function save_addinfo(){
            var a=0;
            
          }
          </script></span>'.NL;
          
$issue_edit_head .= '<span>
        <script type="text/javascript">
             function tab_open(blink_id, cell_ID) 
              {   if (document.getElementById(blink_id).style.display == "block")
                  {   document.getElementById(blink_id).style.display = "none";
                      document.getElementById(cell_ID).style.backgroundPosition = "0px 0px";
                  }
                  else
                  {   document.getElementById(blink_id).style.display = "block";
                      document.getElementById(cell_ID).style.backgroundPosition = "0px -19px";
                  }
              } 
             function span_open(blink_id) 
              {   if (document.getElementById(blink_id).style.display == "block")
                  {   document.getElementById(blink_id).style.display = "none";
                  }
                  else
                  {   document.getElementById(blink_id).style.display = "block";
                  }
              } 
        </script></span>'.NL;
               
//--------------------------------------
// Tables for the Issue details view:
//--------------------------------------
$issue_edit_head .= '<script>
                      function updSeverity()
                      {   document.getElementById("severity").style.display="none";
                          document.getElementById("frm_severity").style.display="inline"; }
                      
                      function updStatus()
                      {   document.getElementById("status").style.display="none";
                          document.getElementById("frm_status").style.display="inline"; }
                    </script>';
                    
$issue_edit_head .= '<table class="itd__title" style="margin-bottom:0;">'.
                   '<tr>
                      <td colSpan="6" >
                      <p>
                        <font size="1"><i>&nbsp['.$issue[$issue_id]['id'].']&nbsp;&nbsp;</i></font>
                        <font size="3" color=#00008f>'.
                          '<b><i><h class="itd_formtitle">'.$issue[$issue_id]['title'].'</h></i></b>
                        </FONT>
                      </p>
                      </td>
                    </tr>'.
                                      
                   '<tbody class="itd__details">'.                    

                   '<tr class="itd_tr_standard">                      
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('lbl_reporter').'</td>'.NL;
                      
if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false) or ($this->getConf('registered_users')=== 0))
{$allowedUser = true;}

if($allowedUser == true)
                        {$issue_edit_head .= '<td class="itd__col3"><a href="mailto:'.$__reportedbyaddr.'">'.$__reportedby.'</a></td>'.NL;}
else{$issue_edit_head .= '<td class="itd__col3">'.$__reportedby.'</td>'.NL;}

$issue_edit_head .= '<td class="itd__col4"></td>                                                                  
                      <td class="itd__col5">'.$this->getLang('th_created').':</td>
                      <td class="itd__col6">'.date($this->getConf('d_format'),strtotime($issue[$issue_id]['created'])).'</td>
                    </tr>'.

                    '<tr class="itd_tr_standard">                      
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_assigned').':</td>'.NL;
if($allowedUser == true)
                        {$issue_edit_head .= '<td class="itd__col3"><a href="mailto:'.$__assigenedaddr.'">'.$__assigened.'</a></td>'.NL;}
else{$issue_edit_head .= '<td class="itd__col3">'.$__assigened.'</td>'.NL;}

$issue_edit_head .= '<td class="itd__col4"></td>                                                                  
                      <td class="itd__col5">'.$this->getLang('th_modified').':</td>
                      <td class="itd__col6">'.date($this->getConf('d_format'),strtotime($issue[$issue_id]['modified'])).'</td>
                    </tr>'.

                   '<tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_severity').':</td>
                      <td class="itd__col3"><span id="severity"';

// --- #132 Feature Request: severity should be modifiable by admin/assignee ---
                      // check if current user is admin/assignee
                      if(strpos($target2,$user_mail['userinfo']['mail']) != false) {
$issue_edit_head .= ' onClick="updSeverity()">'.$severity_img.$issue[$issue_id]['severity'].'</span>'.NL;
                        // Build string to load severity select
                        $severity = explode(',', $this->getConf('severity')) ;
                        foreach ($severity as $x_severity) 
                        {   $x_severity = trim($x_severity);
                            if(stripos($issue[$issue_id]['severity'],$x_severity) !==false) $x_severity_select = $x_severity_select . '<option selected="selected" style="color: blue;" value="'.$x_severity.'">'.$x_severity.'</option>'; 
                            else $x_severity_select = $x_severity_select . '<option value="'.$x_severity.'">'.$x_severity.'</option>'; }

                      // built hidden form with select box of configured severity values
$issue_edit_head .= '<div class="frm_severity" id="frm_severity" style="display:none !important;">
                      <form method="post" accept-charset="'.$lang['encoding'].'>'.
                     '<input type="hidden" name="project" value="'.$project.'" />        
                      <input type="hidden" name="issue_ID" value="'.$issue[$issue_id]['id'].'" />
                      <input type="hidden" name="mod_severity" value="1"/>
                      <input type="hidden" name="ausr" value="'.$user_mail['userinfo']['name'].'"/>
                      <select name="new_severity">'.$x_severity_select.'</select> '.NL;
                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_edit_head .= '<span>'.$helper->getHTML().'</span>'; }
                      }                   
$issue_edit_head .= '<input type="submit" class="button" id="btnmod_severity" name="btnmod_severity" value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.NL.
                      formSecurityToken(false).'</form></div>'.NL;
                      }
                      else $issue_edit_head .= '">'.$severity_img.$issue[$issue_id]['severity'].'</span>'.NL;
                       
$issue_edit_head .= ' </td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('lbl_project').'</td>
                      <td class="itd__col6">'.$project.'</td>
                    </tr>';

                   
$issue_edit_head .= '<tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_status').':</td>
                      <td class="itd__col3"><span id="status"';

// --- #132 Feature Request: severity should be modifiable by admin/assignee ---
                      // check if current user is admin/assignee
                      if(strpos($target2,$user_mail['userinfo']['mail']) != false) {
$issue_edit_head .= ' onClick="updStatus()">'.$status_img.$issue[$issue_id]['status'].'</span>'.NL;
                        // Build string to load severity select
                        $status = explode(',', $this->getConf('status')) ;
                        foreach ($status as $x_status)
                        {   $x_status = trim($x_status);
                            if(stripos($issue[$issue_id]['status'],$x_status) !==false) $x_status_select = $x_status_select . '<option selected="selected" style="color: blue;" value="'.$x_status.'">'.$x_status.'</option>'; 
                            else $x_status_select = $x_status_select . '<option value="'.$x_status.'">'.$x_status.'</option>'; }
                      // built hidden form with select box of configured status values
$issue_edit_head .= '<div class="frm_status" id="frm_status" style="display:none !important;">
                      <form method="post" accept-charset="'.$lang['encoding'].'>'.
                     '<input type="hidden" name="project" value="'.$project.'" />        
                      <input type="hidden" name="issue_ID" value="'.$issue[$issue_id]['id'].'" />
                      <input type="hidden" name="mod_status" value="1"/>
                      <input type="hidden" name="busr" value="'.$user_mail['userinfo']['name'].'"/>
                      <select name="new_status">'.$x_status_select.'</select> '.NL;
                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_edit_head .= '<span>'.$helper->getHTML().'</span>'; }
                      }                   
$issue_edit_head .= '<input type="submit" class="button" id="btnmod_status" name="btnmod_status" value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.NL.
                      formSecurityToken(false).'</form></div>'.NL;
                      }
                      else $issue_edit_head .= '">'.$status_img.$issue[$issue_id]['status'].'</span>'.NL;
                       
$issue_edit_head .= ' </td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5"></td>
                      <td class="itd__col6"></td>
                    </tr>
                    <tr class="itd_tr_standard">
                      <tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2"></td>
                      <td class="itd__col3"></td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5"></td>
                      <td class="itd__col6"></td>
                    </tr>
                    </tbody></table>'.NL;

if(($mail_allowed === true) || ($this->getConf('registered_users')=== 0)) {                    
      $issue_edit_head .= '<form name="form77" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
      $issue_edit_head .= formSecurityToken(false).
                           '<input type="hidden" name="project" value="'.$project.'" />        
                            <input type="hidden" name="issue_ID" value="'.$issue[$issue_id]['id'].'" />
                            <input type="hidden" name="mod_add_data" value="1" />';

      if($issue[$issue_id]['dev_start']   !="") $dev_start = date('d.M.Y',strtotime($issue[$issue_id]['dev_start']));
      if($issue[$issue_id]['dev_deadline']!="") $deadline  = date('d.M.Y',strtotime($issue[$issue_id]['dev_deadline']));
                                                
      $issue_edit_head .= '<table class="itd__title" style="margin:0;padding-top: 1px;"><tbody class="itd__details">
                          <tr class="itd_tr_standard">
                            <td class="it__left_indent" style="margin-top: 3px;"></td>
                            <td class="itd__col2" style="padding-top: 3px;">'.$this->getLang('th_product').':</td>
                            <td class="itd__col3" style="padding-top: 3px;">'.$issue[$issue_id]['product'].'</td>
                            <td class="itd__col4" style="padding-top: 3px;"></td>                   
                            <td class="itd__col5" style="padding-top: 3px;">'.$this->getLang('th_begin').':</td>
                            <td class="itd__col6" style="padding-top: 3px;"><input type="text" class="itdtls_txt" name="dev_start" id="dev_start" value="'.$dev_start.'" /></td>
                          </tr>';
      
      $issue_edit_head .= '<tr class="itd_tr_standard">                      
                            <td class="it__left_indent"></td>
                            <td class="itd__col2">'.$this->getLang('th_version').':</td>
                            <td class="itd__col3">'.$issue[$issue_id]['version'].'</td>
                            <td class="itd__col4"></td>                                                                  
                            <td class="itd__col5">'.$this->getLang('th_deadline').':</td>
                            <td class="itd__col6"><input type="text" class="itdtls_txt" name="dev_deadline" id="dev_deadline" value="'.$deadline.'" /></td>
                          </tr>';
      
      $issue_edit_head .= '<tr class="itd_tr_standard">
                            <td class="it__left_indent"></td>'.NL;
      
                  /*--------------------------------------------------------------------*/
                  // load set of components defined by admin
                  /*--------------------------------------------------------------------*/
                  $STR_COMPONENTS = "";
                  $components = explode(',', $this->getConf('components')) ;
                  foreach ($components as $_component)
                  {
                      $_component = trim($_component);
                      if($issue[$issue_id]['component'] !== $_component) {
                          $STR_COMPONENTS .= '<option value="'.$_component.'" >'.$_component."</option>".NL;
                      }
                      else {
                          $STR_COMPONENTS .= '<option value="'.$_component.'" selected="selected" >'.$_component."</option>".NL;
                      }
                  }
                  $STR_COMPONENTS = '<option value=""></option>' .NL. $STR_COMPONENTS;
                  
      $issue_edit_head .= '                   
                            <td class="itd__col2">Component:</td>
                            <td class="itd__col3"><select name="new_component" id="new_component" class="itdtls_select" style="width:17em;font-size:95%">'.$STR_COMPONENTS.'</select></td>
                            <td class="itd__col4"></td>
                            <td class="itd__col5">'.$this->getLang('th_progress').':</td>
                            <td class="itd__col6"><input type="text" class="itdtls_txt" name="dev_progress" id="dev_progress" value="'.$issue[$issue_id]['dev_progress'].'" /></td>
                          </tr>'.NL;
      
                          if($issue[$issue_id]['tblock']==false) { $cbx_tblock = ""; }
                          else $cbx_tblock = "checked='true'";
      
      $issue_edit_head .= '<tr class="itd_tr_standard">
                            <td class="it__left_indent"></td>
                            <td class="itd__col2">'.$this->getLang('th_tversion').':</td>
                            <td class="itd__col3"><input type="text" class="itdtls_txt" name="trgt_version" id="trgt_version" value="'.$issue[$issue_id]['trgt_version'].'"  /></td>
                            <td class="itd__col4"></td>                   
                            <td class="itd__col5"></td>
                            <td class="itd__col6"></td>
                           </tr>'.NL;
      $issue_edit_head .= '<tr class="itd_tr_standard">
                            <td class="it__left_indent"></td>
                            <td class="itd__col2">'.$this->getLang('th_tblock').':</td>
                            <td class="itd__col3"><input type="checkbox" name="itl__block_filter" id="itl__block_filter" '.$cbx_tblock.' />&nbsp</td>
                            <td class="itd__col4"></td>                   
                            <td class="itd__col5">
                                <input class ="button" 
                                 style ="font-size:8pt;" 
                                 id    ="btn_upd_addinfo" 
                                 name  ="btn_upd_addinfo"
                                 type  ="submit" 
                                 value ="'.$this->getLang('btn_upd_addinfo').'" 
                                 title ="'.$this->getLang('btn_upd_addinfo').'" />
                            </td>
                            <td class="itd__col6"></td>
                           </tr>
                           </table>'.NL;                      
      $issue_edit_head .= '
                           </form>'.NL;
}
else {  // user not allowed to modify the planning data
      if($issue[$issue_id]['dev_start']   !="") $dev_start = date('d.M.Y',strtotime($issue[$issue_id]['dev_start']));
      if($issue[$issue_id]['dev_deadline']!="") $deadline  = date('d.M.Y',strtotime($issue[$issue_id]['dev_deadline']));

        $issue_edit_head .= '<table class="itd__title" style="margin:0;padding-top: 1px;"><tbody class="itd__details">
                            <tr class="itd_tr_standard">
                              <td class="it__left_indent" style="margin-top: 3px;"></td>
                              <td class="itd__col2" style="padding-top: 3px;">'.$this->getLang('th_product').':</td>
                              <td class="itd__col3" style="padding-top: 3px;">'.$issue[$issue_id]['product'].'</td>
                              <td class="itd__col4" style="padding-top: 3px;"></td>                   
                              <td class="itd__col5" style="padding-top: 3px;">'.$this->getLang('th_begin').':</td>
                              <td class="itd__col6" style="padding-top: 3px;">'.$dev_start.'</td>
                            </tr>';
        
        $issue_edit_head .= '<tr class="itd_tr_standard">                      
                              <td class="it__left_indent"></td>
                              <td class="itd__col2">'.$this->getLang('th_version').':</td>
                              <td class="itd__col3">'.$issue[$issue_id]['version'].'</td>
                              <td class="itd__col4"></td>                                                                  
                              <td class="itd__col5">'.$this->getLang('th_deadline').':</td>
                              <td class="itd__col6">'.$issue[$issue_id]['dev_deadline'].'</td>
                            </tr>';
        
        $issue_edit_head .= '<tr class="itd_tr_standard">
                              <td class="it__left_indent"></td>
                              <td class="itd__col2">'.$this->getLang('th_components').':</td>
                              <td class="itd__col3">'.$deadline.'</td>
                              <td class="itd__col4"></td>
                              <td class="itd__col5">'.$this->getLang('th_progress').':</td>
                              <td class="itd__col6">'.$issue[$issue_id]['dev_progress'].'</td>
                            </tr>'.NL;
        
                            if($issue[$issue_id]['tblock']!==false) { $cbx_tblock = $this->getLang('yes'); }
                            else $cbx_tblock = false;
        
        $issue_edit_head .= '<tr class="itd_tr_standard">
                              <td class="it__left_indent"></td>
                              <td class="itd__col2">'.$this->getLang('th_tversion').':</td>
                              <td class="itd__col3">'.$issue[$issue_id]['trgt_version'].'</td>
                              <td class="itd__col4"></td>                   
                              <td class="itd__col5"></td>
                              <td class="itd__col6"></td>
                             </tr>'.NL;
        $issue_edit_head .= '<tr class="itd_tr_standard">
                              <td class="it__left_indent"></td>
                              <td class="itd__col2">'.$this->getLang('th_tblock').':</td>
                              <td class="itd__col3">'.$cbx_tblock.'</td>
                              <td class="itd__col4"></td>                   
                              <td class="itd__col5"></td>
                              <td class="itd__col6"></td>
                             </tr>
                             </table>'.NL;                      
}
                   
/*------------------------------------------------------------------------------
  #60: to view mod-log
------------------------------------------------------------------------------*/
            if($this->getConf('it_data')==false) $modfile = DOKU_CONF."../data/meta/".$project.'_'.$issue[$issue_id]['id'].'.mod-log';
            else $modfile = DOKU_CONF."../". $this->getConf('it_data').$project.'_'.$issue[$issue_id]['id'].'.mod-log';
            if (@file_exists($modfile)) {  
              $pstring = sprintf("showid=%s&amp;project=%s", urlencode($issue[$issue_id]['id']), urlencode($project));
              $modlog_link = '<a style="font-size:8pt;" href="doku.php?id='.$ID.'&do=showmodlog&'.$pstring.'" title="'.$this->getLang('th_showmodlog').'">'.$this->getLang('th_showmodlog').'</a>';
              $issue_edit_head .= '<table class="itd__title modlog_link"><tr><td class="itd__modlog_link" style="width:100%;font-size:10pt !important;align:right;">['.$modlog_link.']</td></tr>'.NL;                    
              $issue_edit_head .= '</table>'.NL;
            }
/*----------------------------------------------------------------------------*/

                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
                  $cell_ID = 'img_tab_open_reporterdtls_'.$blink_id;                              
$issue_client_details = '<table class="itd__tables" id="tbl_'.$anker_id.'"><tbody>
                        <tr>
                           <td class="itd_tables_tdh" colSpan="3">'.$this->getLang('lbl_reporterdtls').'</td>
                        </tr>
                        <tbody style="display : none;" id="'.$blink_id.'"><tr class="itd__tables_tr" style="width:100%;">
                           <td class="it__left_indent"></td>
                           <td class="itd_tables_tdc2">'.$this->getLang('lbl_reportername').'</td>
                           <td class="itd_tables_tdc3">'.$issue[$issue_id]['user_name'].'</td>
                        </tr>';

                        //--------------------------------------------------------------------------------------------------------------
                        // do not show personal details if issue details diplayed by neigther admin/assignee nor the original user itself
                        //--------------------------------------------------------------------------------------------------------------
/*                        echo "current user = ".$user_mail['userinfo']['mail']."<br />".
                               "Reporting user = ".$issue[$issue_id]['user_mail']."<br />";
                          if($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) {echo "current user = Reporting user <br /><br />";}
                             else {echo "current user != Reporting user <br /><br />";}
                          if(strpos($target2,$user_mail['userinfo']['mail']) != false) {echo "current user is a member of assignees <br /><br />";}
                             else {echo "current user is not a member of assignees <br /><br />";}
*/
//--------------------------------------------------------------------------------------------------------------
//create output for followers on registered users only
if(($user_mail['userinfo']['mail'] !== false))
{         $blink2_id = 'statanker2_'.$alink_id;
          $anker2_id = 'anker2_'.$alink_id;
          $tmp = explode(',', $issue[$issue_id]['add_user_mail']);
          $follower = 0;
$issue_addcontacts .='      <td class="itd_tables_tdc3">'.NL;
                            foreach($tmp as $email) {
                              //show only own mail address
                              if((strlen($email)>2) && (stripos($user_mail['userinfo']['mail'],$email)!==false)) {
                                  $issue_addcontacts .='<a href="mailto:'.$email.'">'.$email.'</a>, '.NL;
                                  $ademail .= $email;
                                  $follower++;
                              }
                              //admin/assignee to see all followers
                              elseif((strlen($email)>2) && (strpos($target2,$user_mail['userinfo']['mail']) != false)) {
                                  $issue_addcontacts .='<a href="mailto:'.$email.'">'.$email.'</a>, '.NL;
                                  $ademail .= $email;
                                  $follower++;
                              }
                              // to count follower
                              elseif(strlen($email)>2) $follower++;
                            }
                            if($follower==0) $follower='0';
                            $ademail = str_replace(',,',',',$ademail);
$issue_addcontacts .='      <span style="display : none;" id="'.$blink2_id.'">
                                 <form name="add_contact" method="post" accept-charset="'.$lang['encoding'].'">'
                                  .formSecurityToken(false).'
                                  <input type="hidden" name="project" value="'.$project.'" />        
                                  <input type="hidden" name="issue_ID" value="'.$issue[$issue_id]['id'].'" />
                                  <input type="hidden" name="mod_contacts" value="1"/>
                                  <input type="text" style="width:95%; font-size: 9pt;" name="add_contact" value="'.$user_mail['userinfo']['mail'].'" /><br />';
                                  if ($this->getConf('use_captcha')==1) 
                                    {   $helper = null;
                            		        if(@is_dir(DOKU_PLUGIN.'captcha'))
                            			         $helper = plugin_load('helper','captcha');
                            			         
                            		        if(!is_null($helper) && $helper->isEnabled())
                            			      {  $issue_addcontacts .= '<span>'.$helper->getHTML().'</span>'; }
                                    }
$issue_addcontacts .='            <input  type="submit" class="button" style="font-size:8pt;" id="btn_add_contact" name="btn_add_contact" value="'.$this->getLang('btn_add').'" title="'.$this->getLang('btn_add').'");/>
                                </form>
                              </span>
                            </td>'.NL;
$issue_addimg = '<img class="cmt_list_plus_img" alt="add" src="'.DOKU_BASE.'lib/plugins/issuetracker/images/blank.gif" id="'.$anker2_id.'" onClick="span_open(\''.$blink2_id.'\')" />
                 <p style="margin-top:-4px;"><span style="font-size:7pt;">'.sprintf($this->getLang('itd_follower'),$follower).'</span></p>';
}
//--------------------------------------------------------------------------------------------------------------
                               
                        if($allowedUser == true) 
                        {  
$issue_client_details .= '<tr class="itd__tables_tr">
                            <td class="it__left_indent"></td>
                            <td class="itd_tables_tdc2">'.$this->getLang('lbl_reportermail').'</td>
                            <td class="itd_tables_tdc3"><a href="mailto:'.$issue[$issue_id]['user_mail'].'">'.$issue[$issue_id]['user_mail'].'</a></td>
                          </tr>
                          <tr class="itd__tables_tr">
                            <td class="it__left_indent"></td>
                            <td class="itd_tables_tdc2">'.$this->getLang('lbl_reporterphone').'</td>
                            <td class="itd_tables_tdc3">'.$issue[$issue_id]['user_phone'].'</td>
                          </tr>';
                        }
$issue_client_details .=    '<tr class="itd__tables_tr">
                                <td class="it__left_indent"></td>
                                <td class="itd_tables_tdc2">'.$this->getLang('lbl_reporteradcontact').NL.
                                $issue_addimg.NL.
                                '</td>'.$issue_addcontacts.'
                             </tr>'; 

$issue_client_details .= '</tbody><tr>'.NL.'
                            <td colspan="3" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                                <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                  <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('gen_tab_open').'</a>
                                </div>'.NL.'
                            </td>
                            </tr>'.NL.'</tbody></table>';


                        $x_comment = $this->convertlabel($issue[$issue_id]['description']);

/*------------------------------------------------------------------------------
 * Issue: 39, reported by lukas
 * hook-in to provide possibility of modifing the initial description
------------------------------------------------------------------------------*/
        // retrive some basic information
        $cur_date = date($this->getConf('d_format'));
        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');

$issue_initial_description = '<table class="itd__tables"><tbody>
                                <tr>
                                  <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_initdescr').'</td>
                                </tr>
                                <tr class="itd__tables_tr">
                                  <td class="itd_comment_tr" colSpan="2" style="padding-left:0.45em;">'.$this->xs_format($x_comment).'</td>
                                </tr>';
                                                           
/* mod for edit description by ticket owner and admin/assignee ---------------*/
// check if current user is author of the comment and offer an edit button
            if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false)) 
            {     // add hidden edit toolbar and textarea
                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
        
            $issue_initial_description .= '   <tr class="itd_edit_tr">
                                                 <td class="itd_edit_tr" colSpan="2" style="display : none;" id="'.$blink_id.'">';
                    
            if($this->getConf('wysiwyg')==true) {
                $issue_initial_description .= $this->it_wysiwyg_edit_toolbar($x_comment);
                $_textarea = '<textarea class="itd_textarea" id="description_mod" name="description_mod" style="display: none;"> </textarea><br />'.NL;
            }
            else {
                $issue_initial_description .= $this->it_xs_edit_toolbar('description_mod_'.$alink_id);
                $_textarea = '<textarea class="itd_textarea" id="description_mod_'.$alink_id.'" name="description_mod" type="text" cols="106" rows="7" value="">'.strip_tags($x_comment).'</textarea><br />'.NL;
            }        
            $issue_initial_description .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
            $issue_initial_description .= formSecurityToken(false). 
                                         '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                                         '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                                         '<input type="hidden" name="author"value="'.$u_mail_check.'" />'.NL.        
                                         '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.
                                         '<input type="hidden" name="mod_description" value="1"/>'.NL.
                                         $_textarea . '
                                          <span class="reply_close_link">
                                            <a href="javascript:resizeBoxId(\'description_mod_'.$alink_id.'\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                                            <a href="javascript:resizeBoxId(\'description_mod_'.$alink_id.'\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                                         </span>'.NL;
        
                                         
                                if ($this->getConf('use_captcha')==1) 
                                {   $helper = null;
                        		        if(@is_dir(DOKU_PLUGIN.'captcha'))
                        			         $helper = plugin_load('helper','captcha');
                        			         
                        		        if(!is_null($helper) && $helper->isEnabled())
                        			      {  $issue_initial_description .= '<p>'.$helper->getHTML().'</p>'; }
                                }
                                $cell_ID = 'img_tab_open_comment'.$blink_id;
                                $cntrl_id  = 'ctrl_'.$alink_id;
$issue_initial_description .=  '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
                               '<fieldset class="minor_mod">'.
                               '<span style="width: 30em; text-align: left; float: left;" title="'.$this->getLang('minor_mod_cbx_title').'"><input type="checkbox" name="minor_mod" id="'.$cntrl_id.'" value="true" style="float: left;" /><label for="'.$cntrl_id.'">&nbsp;&nbsp;'.$this->getLang('minor_mod').'</label>'.
                               '<input  style="margin-left: 2em;" type="submit" class="button" id="btnmod_description" name="btnmod_description" value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.'</span>'.
                               '</fieldset>'.
                               '</form>'.NL.'</td>'.NL.'</tr>'.NL.
                               '<tr>'.NL.'
                                   <td colspan="2" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                                       <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                         <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('descr_tab_mod').'</a>
                                       </div>'.NL.'
                                   </td>'.NL.'
                                </tr>'.NL;
                }
$issue_initial_description .= '</tbody></table>';
/* END mod for edit description by ticket owner ----------------------------------*/
if((strpos($this->getConf('ltdReport'),'Symptom link 1')===false) || ($this->getConf('upload')>0)) {
  $issue_attachments = '<table class="itd__tables"><tbody>
                        <tr>
                          <td colspan="2" class="itd_tables_tdh">'.$this->getLang('lbl_symptlinks').'</td>
                        </tr>
                        <tr  class="itd__tables_tr">
                          <td class="itd_attachments_tr" colspan="2" style="padding-left:0.45em;">1. <a href="'.$issue[$issue_id]['attachment1'].'"><img border="0" alt="symptoms 1" style="margin-right:0.5em" vspace="1" align="middle" src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment1'].'" href="'.$issue[$issue_id]['attachment1'].'">'.$issue[$issue_id]['attachment1'].'</a></td>
                        </tr>'.NL;
  if(strpos($this->getConf('ltdReport'),'Symptom link 2')===false){
    $issue_attachments .= '<tr  class="itd__tables_tr">
                            <td class="itd_attachments_tr" colspan="2" style="padding-left:0.45em;">2. <a href="'.$issue[$issue_id]['attachment2'].'"><img border="0" alt="symptoms 2" style="margin-right:0.5em" vspace=1em align=absMiddle src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment2'].'" href="'.$issue[$issue_id]['attachment2'].'">'.$issue[$issue_id]['attachment2'].'</a></td>
                          </tr>'.NL;
  }
  if(strpos($this->getConf('ltdReport'),'Symptom link 3')===false){
    $issue_attachments .= '<tr  class="itd__tables_tr">
                            <td class="itd_attachments_tr" colspan="2" style="padding-left:0.45em;">3. <a href="'.$issue[$issue_id]['attachment3'].'"><img border="0" alt="symptoms 3" style="margin-right:0.5em" vspace="1" align="middle" src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment3'].'" href="'.$issue[$issue_id]['attachment3'].'">'.$issue[$issue_id]['attachment3'].'</a></td>
                          </tr>'.NL;
  }
  /* mod for edit symptom links by ticket owner and admin/assignee ---------------*/
  // check if current user is author of the comment and offer an edit button
              if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false)) {
                    // add hidden edit toolbar and textarea
                    $alink_id++;
                    $blink_id = 'statanker_'.$alink_id;
                    $anker_id = 'anker_'.$alink_id;
                    $cell_ID = 'img_tab_open_reporterdtls'.$blink_id;                              
  $issue_attachments .= '<tbody style="display : none;" id="'.$blink_id.'">
                          <tr><td colspan=2>'.NL.
                          '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'" enctype="multipart/form-data">'.NL;
  $issue_attachments .= formSecurityToken(false). 
                       '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                       '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.        
                       '<input type="hidden" name="mod_symptomlinks" value="1"/>'.NL;        
                                           
                                  if ($this->getConf('use_captcha')==1) 
                                  {   $helper = null;
                          		        if(@is_dir(DOKU_PLUGIN.'captcha'))
                          			         $helper = plugin_load('helper','captcha');
                          			         
                          		        if(!is_null($helper) && $helper->isEnabled())
                          			      {  $issue_attachments .= '<p>'.$helper->getHTML().'</p>'; }
                                  }                   
                  //Check config if hidden
                  if($this->getConf('upload')>0) {
                    if(strpos($this->getConf('ltdReport'),'Symptom link 1')!==false){
                        $issue_attachments .= ' <input type="hidden" class="it__cir_linput" name="attachment1" value="'.$issue[$issue_id]['attachment1'].'"/>'.NL;
                    } 
                    else {
                        $issue_attachments .= '<span><input type="file" class="it__cir_linput" name="attachment1" value="'.$issue[$issue_id]['attachment1'].'"/></span><br />'.NL;
                    }             
                    if(strpos($this->getConf('ltdReport'),'Symptom link 2')!==false){
                        $issue_attachments .= ' <input type="hidden" class="it__cir_linput" name="attachment2" value="'.$issue[$issue_id]['attachment2'].'"/>'.NL;
                    } 
                    else {
                        $issue_attachments .= '<span><input type="file" class="it__cir_linput" name="attachment2" value="'.$issue[$issue_id]['attachment2'].'"/></span><br />'.NL;
                    }             
                    if(strpos($this->getConf('ltdReport'),'Symptom link 3')!==false){
                        $issue_attachments .= ' <input type="hidden" class="it__cir_linput" name="attachment3" value="'.$issue[$issue_id]['attachment3'].'"/>'.NL;
                    } 
                    else {
                        $issue_attachments .= '<span><input type="file" class="it__cir_linput" name="attachment3" value="'.$issue[$issue_id]['attachment3'].'"/></span><br/>'.NL;
                    }
                  } 
  $issue_attachments .= '<input type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
                                 '<input  type="submit" class="button" id="btnmod_description" name="btnmod_description" style="float:right;" value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.
                                 '</form>'.NL.'</td></tr></tbody><tr>'.NL.'
                              <td colspan="3" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                                  <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                    <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('descr_tab_mod').'</a>
                                  </div>'.NL.'
                              </td>
                              </tr>'.NL.'</table>';
              }
  $issue_attachments .='</tbody></table>'.NL;
}
/* END mod for edit description by ticket owner ----------------------------------*/  

/* Workaround description --------------------------------------------------------*/
$issue_workaround ='<table class="itd__tables"><tbody>
                      <tr>
                        <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_workaround').'</td>
                      </tr>'.NL;
$issue_workaround .= '<tr  class="itd__tables_tr">
                          <td class="itd_comment_tr">'.NL;
                      // output workaround content
                      $x_id = "WA";
                      $x_workaround = $comments[0]['wround_mod'];
                      $x_workaround = $this->convertlabel($x_workaround);
$issue_workaround .= $this->xs_format($x_workaround).NL.'</td></tr>'.NL;

//create output for followers on registered users only
if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false)) 
{                   // add hidden edit toolbar and textarea
                      $alink_id++;
                      $blink_id = 'statanker_'.$alink_id;
                      $anker_id = 'anker_'.$alink_id;
        
                    $issue_workaround .= '   <tr class="itd_edit_tr">
                                                 <td class="itd_edit_tr" colSpan="2" style="display : none;" id="'.$blink_id.'">';
                    
                    $issue_workaround .= $this->it_xs_edit_toolbar('wround_mod_'.$alink_id);
                    
                    $issue_workaround .= '<form name="wround_form" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                                          
                    $issue_workaround .= formSecurityToken(false). 
                                         '<input type="hidden" name="mod_wround" value="1" />'.NL.        
                                         '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                                         '<input type="hidden" name="comment_file" value="'.$cfile.'" />'.NL.
                                         '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                                         '<input type="hidden" name="author" value="'.$user_mail['userinfo']['mail'].'" />'.NL.        
                                         '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.
                                         '<textarea class="itd_textarea" id="wround_mod_'.$alink_id.'" name="wround_mod" type="text" cols="106" rows="7" value="">'.strip_tags($x_workaround).'</textarea><br />
                                          <span class="reply_close_link">
                                            <a href="javascript:resizeBoxId(\'wround_mod_'.$alink_id.'\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                                            <a href="javascript:resizeBoxId(\'wround_mod_'.$alink_id.'\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                                          </span>'.NL;
        
                    if ($this->getConf('use_captcha')==1) 
                    {   $helper = null;
            		        if(@is_dir(DOKU_PLUGIN.'captcha'))
            			         $helper = plugin_load('helper','captcha');
            			         
            		        if(!is_null($helper) && $helper->isEnabled())
            			      {  $issue_workaround .= '<p>'.$helper->getHTML().'</p>'; }
                    }
                    $cell_ID = 'img_tab_open_comment'.$blink_id;
                    $cntrl_id  = 'ctrl_'.$alink_id;
                    
$issue_workaround .= '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
                       '<fieldset class="minor_mod">'.
                               '<span style="width: 30em; text-align: left; float: left;"  title="'.$this->getLang('minor_mod_cbx_title').'"><input type="checkbox" name="minor_mod" value="true" id="'.$cntrl_id.'" value="true" style="float: left;" /><label for="'.$cntrl_id.'">&nbsp;&nbsp;'.$this->getLang('minor_mod').'</label>'.
                               '<input style="margin-left: 2em;" type="submit" class="button" id="store_workaround" name="store_workaround"  value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.'</span>'.
                       '</fieldset>'.
                       '</form>'.NL.'</td>'.NL.'</tr>'.NL.
                       '<tr>'.NL.'
                           <td colspan="2" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                               <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                 <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('descr_tab_mod').'</a>
                               </div>'.NL.'
                           </td>'.NL.'
                        </tr>'.NL;
}
$issue_workaround .='</tbody></table>'.NL; 
                             
/* END of Workaround description --------------------------------------------------------*/

$issue_comments_log ='<table class="itd__tables"><tbody>
                      <tr>
                        <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_cmts_wlog').'</td>
                      </tr>';
              // loop through the comments
              if ($comments!=false) {              
                  foreach ($comments as $a_comment)
                  {     if($this->_get_one_value($a_comment,'id') === 'WA') continue;
                        $x_id = $this->_get_one_value($a_comment,'id');
                        $x_comment = $this->_get_one_value($a_comment,'comment');
                        $x_comment = $this->convertlabel($x_comment);
                        
                        //----------------------------------------------------------------------------------------------------------------
                        // do not show personal details if issue details diplayed by neigther admin/assignee nor the original user itself
                        //----------------------------------------------------------------------------------------------------------------
                        if((($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) 
                            or (strpos($target2,$user_mail['userinfo']['mail']) != false) 
                            or ($user_mail['userinfo']['mail'] === $this->_get_one_value($a_comment,'author')))
                            && ($this->getConf('shw_mail_addr')===1) && ($this->getConf('auth_ad_overflow') == false))
                        {   $x_mail = '<a href="mailto:'.$this->_get_one_value($a_comment,'author').'">'.$this->_get_one_value($a_comment,'author').'</a>'; }
                        // show mailto with name instead user mail address
                        elseif((($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) 
                            or (strpos($target2,$user_mail['userinfo']['mail']) != false) 
                            or ($user_mail['userinfo']['mail'] === $this->_get_one_value($a_comment,'author')))
                            && ($this->getConf('shw_mail_addr')===0) && ($this->getConf('auth_ad_overflow') == false)) 
                            {
                              $compare = $this->_get_one_value($a_comment,'author');
                              $dw_users = $auth->retrieveUsers();
                              foreach($dw_users as $mail_adr)
                              { if($mail_adr['mail']==$compare)
                                {   $tmp_name = $mail_adr['name'];
                                    break;
                                }
                              }
                              if($tmp_name==false) $tmp_name = $compare;
                              $x_mail= '<a href="mailto:'.$compare.'">'.$tmp_name.'</a>';
                            }
                        elseif($this->getConf('auth_ad_overflow') == true) {
                            $x_mail = '<a href="mailto:'.$this->_get_one_value($a_comment,'author').'">'.$this->_get_one_value($a_comment,'author').'</a>'; }
                        else {   
                            // here it is necessary for others to know if issue reporter, follower or assignee is commenting
                            // implement the function to differenciate between these roles
                            // => check mail address with reporter, follower and assignee/admin group else it is a foreigner
                            $role       = $this->_get_one_value($a_comment,'author');
                            if ($issue[$issue_id]['user_mail'] === $role)
                                $x_mail = '<i> ('.$this->getLang('dtls_reporter_hidden').') </i>';  
                            elseif (stripos($issue['add_user_mail'],$role) != false)
                                $x_mail = '<i> ('.$this->getLang('dtls_follower_hidden').') </i>';  
                            elseif (stripos($target2,$role) != false)
                                $x_mail = '<i> ('.$this->getLang('dtls_assignee_hidden').') </i>';  
                            else
                                $x_mail = '<i> ('.$this->getLang('dtls_foreigner_hidden').') </i>';  
                        }

                        if($this->_get_one_value($a_comment,'mod_timestamp')) { $insert_lbl = '<label class="cmt_mod_exclamation">!</label>';}
                        else $insert_lbl ='';

                        $issue_comments_log .= '<tr  class="itd__tables_tr">
                                                  <td class="itd_comment_trh"><label name="a'.$x_id.'" id="a'.$x_id.'">['.$this->_get_one_value($a_comment,'id').'] </label>&nbsp;&nbsp;&nbsp;
                                                                            <label>'.date($this->getConf('d_format'),strtotime($this->_get_one_value($a_comment,'timestamp'))).' </label>&nbsp;&nbsp;&nbsp;'.NL.'
                                                                            <label>'.$x_mail.'</label>'.NL;
                        if($this->_get_one_value($a_comment,'mod_timestamp')) {
                        $issue_comments_log .= '                            <label class="cmt_mod_label" >&nbsp;&nbsp;[modified: '.date($this->getConf('d_format'),strtotime($this->_get_one_value($a_comment,'mod_timestamp'))).']&nbsp;'.$insert_lbl.'&nbsp;</label>&nbsp;&nbsp;&nbsp;'.NL;
                        }
                        
                        $issue_comments_log .= '  </td>'.NL;
                        $issue_comments_log .= '</tr>
                                                <tr  class="itd__tables_tr">
                                                  <td class="itd_comment_tr">';
                        
                        // delete button for comments
                        if(($user_mail['userinfo']['mail'] === $this->_get_one_value($a_comment,'author')) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
                        {   $issue_comments_log .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                            $issue_comments_log .= formSecurityToken(false). 
                                                 '<input type="hidden" name="project" value="'.$project.'"/>'.NL.
                                                 '<input type="hidden" name="comment_file" value="'.$cfile.'"/>'.NL.
                                                 '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'"/>'.NL.
                                                 '<input type="hidden" name="author" value="'.$u_mail_check.'"/>'.NL.        
                                                 '<input type="hidden" name="del_cmnt" value="TRUE"/>'.NL.
                                                 '<input type="hidden" name="comment_id" value="'.$this->_get_one_value($a_comment,'id').'"/>'.NL.        
                                                 '<input class="cmt_del_img" type="image" src="'.DOKU_BASE.'lib/plugins/issuetracker/images/dot.gif" alt="Del" title="'.$this->getLang('del_title').'" />'.        
                                                 '</form>'.NL; 
                       }
                       // output comment content
                       $issue_comments_log .= $this->xs_format($x_comment).NL.'</td></tr>'.NL;
        //--------------------------------------------------------------------------------------------------------------
        // only admin/assignees and reporter are allowed to add comments if only user edit option is set
        //--------------------------------------------------------------------------------------------------------------
        // retrive some basic information
        $cur_date = date($this->getConf('d_format'));
        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        elseif($this->getConf('shw_mail_addr')===0) {$u_mail_check =$user_mail['userinfo']['name'];}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');
        
/*------------------------------------------------------------------------------
 *   Modify comment                                                           */
                // check if current user is author of the comment and offer an edit button
                if(($allowedUser == true) or ($this->getConf('auth_ad_overflow') == true))
                {     // add hidden edit toolbar and textarea
                      $alink_id++;
                      $blink_id = 'statanker_'.$alink_id;
                      $anker_id = 'anker_'.$alink_id;
        
                    $issue_comments_log .= '   <tr class="itd_edit_tr">
                                                 <td class="itd_edit_tr" colSpan="2" style="display : none;" id="'.$blink_id.'">';
                    
                    $issue_comments_log .= $this->it_xs_edit_toolbar('comment_mod_'.$alink_id);
                    
                    $issue_comments_log .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                                          
                    $issue_comments_log .= formSecurityToken(false). 
                                         '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                                         '<input type="hidden" name="comment_file" value="'.$cfile.'" />'.NL.
                                         '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                                         '<input type="hidden" name="author"value="'.$user_mail['userinfo']['mail'].'" />'.NL.        
                                         '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.
                                         '<input type="hidden" name="comment_id" value="'.$this->_get_one_value($a_comment,'id').'" />'.NL.        
                                         '<textarea class="itd_textarea" id="comment_mod_'.$alink_id.'" name="comment_mod" type="text" cols="106" rows="7" value="">'.strip_tags($x_comment).'</textarea><br />
                                          <span class="reply_close_link">
                                            <a href="javascript:resizeBoxId(\'comment_mod_'.$alink_id.'\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                                            <a href="javascript:resizeBoxId(\'comment_mod_'.$alink_id.'\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                                          </span>'.NL;
        
                    if ($this->getConf('use_captcha')==1) 
                    {   $helper = null;
            		        if(@is_dir(DOKU_PLUGIN.'captcha'))
            			         $helper = plugin_load('helper','captcha');
            			         
            		        if(!is_null($helper) && $helper->isEnabled())
            			      {  $issue_comments_log .= '<p>'.$helper->getHTML().'</p>'; }
                    }
                    $cell_ID = 'img_tab_open_comment'.$blink_id;
                    $cntrl_id  = 'ctrl_'.$alink_id;
                    // check if only registered users are allowed to modify comments
                    // Ã‚Â¦ perm Ã¢â‚¬â€ the user's permissions related to the current page ($ID)
$issue_comments_log .= '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
                       '<fieldset class="minor_mod">'.
                               '<span style="width: 30em; text-align: left; float: left;"  title="'.$this->getLang('minor_mod_cbx_title').'"><input type="checkbox" name="minor_mod" value="true" id="'.$cntrl_id.'" value="true" style="float: left;" /><label for="'.$cntrl_id.'">&nbsp;&nbsp;'.$this->getLang('minor_mod').'</label>'.
                               '<input style="margin-left: 2em;"  type="submit" class="button" id="btnmod_description" name="btnmod_description"  value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.'</span>'.
                       '</fieldset>'.
                       '</form>'.NL.'</td>'.NL.'</tr>'.NL.
                       '<tr>'.NL.'
                           <td colspan="2" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                               <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                 <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('cmt_tab_mod').'</a>
                               </div>'.NL.'
                           </td>'.NL.'
                        </tr>'.NL;
                }
            }
        }
        $issue_comments_log .='</tbody></table>'; 

/*   end Modify comment
------------------------------------------------------------------------------*/

        //--------------------------------------------------------------------------------------------------------------
        // only admin/assignees and reporter are allowed to add comments if only user edit option is set
        //--------------------------------------------------------------------------------------------------------------
        // retrive some basic information
        $cur_date = date($this->getConf('d_format'));
        if(strlen($user_mail['userinfo']['mail']) == 0) {$u_mail_check ='unknown';}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');
        $u_name = $user_mail['userinfo']['name'];
        //2011-12-02: bwenz code proposal (Issue 11)
        $x_resolution = $this->convertlabel($issue[$issue_id]['resolution']);
//        if(!$x_resolution) { $x_resolution = "&nbsp;"; }
                        
        $_cFlag = false;             
        if($user_check == 0)  
            { $_cFlag = true;  } 
        elseif(($user_check == 1) && ($user_mail['perm'] > 1)) 
            { $_cFlag = true;  } 

        if($_cFlag === true) {

                      
// mod for editor ---------------------------------------------------------------------
                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
$issue_add_comment .='<table class="itd__tables">'.
                      '<tr>'.
                        '<td class="itd_tables_tdh cmts_adcmt" colSpan="2" >'.$this->getLang('lbl_cmts_adcmt').'</td>
                      </tr><tr class="itd_edit_tr"><td class="itd_edit_tr" colSpan="2" style="display : none;" id="'.$blink_id.'">';
$issue_add_comment .= $this->it_xs_edit_toolbar('comment_'.$alink_id);                     
// mod for editor ---------------------------------------------------------------------

$issue_add_comment .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                      
$issue_add_comment .= formSecurityToken(false). 
                     '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                     '<input type="hidden" name="comment_file" value="'.$cfile.'" />'.NL.
                     '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                     '<input type="hidden" name="author" value="'.$u_mail_check.'" />'.NL.        
                     '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.        
                     '<textarea class="itd_textarea" id="comment_'.$alink_id.'" name="comment" type="text" cols="106" rows="7" value=""></textarea><br>
                      <span class="reply_close_link">
                        <a href="javascript:resizeBoxId(\'comment_'.$alink_id.'\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                        <a href="javascript:resizeBoxId(\'comment_'.$alink_id.'\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                      </span>'.NL;
        

                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_add_comment .= '<p>'.$helper->getHTML().'</p>'; }
                      }
                      $cell_ID = 'img_tab_open_comment'.$blink_id;
                      // check if only registered users are allowed to add comments
                      // Ã‚Â¦ perm Ã¢â‚¬â€ the user's permissions related to the current page ($ID)
                      $issue_add_comment .= '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.NL.
                                            '<input class="button" id="showcase" type="submit" name="showcase" value="'.$this->getLang('btn_add').'" title="'.$this->getLang('btn_add_title').'");/>'.NL.
                                            '</form>'.NL.'</td>'.NL.'</tr>'.NL.
                                            '<tr>'.NL.'
                                                <td colspan="2" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                                                    <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                                      <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('cmt_tab_open').'</a>
                                                    </div>'.NL.'
                                                </td>'.NL.'
                                             </tr></table>'.NL;

                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;

$issue_edit_resolution ='<table class="itd__tables">
                         <tr>
                            <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('th_resolution').'</td>
                        </tr>';
$issue_edit_resolution .= '<tr class="itd__tables_tr">
                            <td class="itd_comment_tr" colSpan="2" style="padding-left:0.45em;">'.$this->xs_format($x_resolution).'</td>
                          </tr>
                          <tr class="itd_edit_tr"><td class="itd_edit_tr" colSpan="2" style="display : none;" id="'.$blink_id.'">';

/*------------------------------------------------------------------------------
 * extension based on Issue: 39, reported by lukas
 * hook-in to provide possibility of modifing the last comment
------------------------------------------------------------------------------*/
/*    - if user = commentor of last comment then provide edit text area 
        pre-loaded with button and last comment content for mofification
 *    - upon diff of former comment and textarea store it as comment
 *    - highlight current comment as modified (optional)
------------------------------------------------------------------------------*/

// mod for editor ---------------------------------------------------------------------
$issue_edit_resolution .= $this->it_xs_edit_toolbar('x_resolution');                      
// mod for editor ---------------------------------------------------------------------

$issue_edit_resolution .= '<form name="edit_resolution" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'">'.NL;                                            
$issue_edit_resolution .= formSecurityToken(false).
                          '<input type="hidden" name="project"value="'.$project.'"/>'.NL.
                          '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'"/>'.NL.
                          '<input type="hidden" name="usr" value="'.$u_name.'"/>'.NL.
                          '<input type="hidden" name="add_resolution" value="1"/>'.NL;        
    
$issue_edit_resolution .= "<textarea class='itd_textarea' id='x_resolution' name='x_resolution' type='text' cols='106' rows='7' value=''>".strip_tags($x_resolution)."</textarea><br>".
                          '<span class="reply_close_link">
                            <a href="javascript:resizeBoxId(\'x_resolution\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                            <a href="javascript:resizeBoxId(\'x_resolution\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                          </span>'.NL;

                              
                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_edit_resolution .= '<p>'.$helper->getHTML().'</p>'; }
                      }
                      
                      $cell_ID = 'img_tab_open_comment'.$blink_id;

$issue_edit_resolution .= '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
                      '<input class="button" id="store_resolution" type="submit" name="store_resolution" value="'.$this->getLang('btn_add').'" title="'.$this->getLang('btn_add_title').'");/>'.
                      '</form>'.NL.'</td>'.NL.'</tr>'.NL.
                      '<tr>'.NL.'
                          <td colspan="2" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                              <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                <a id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('rsl_tab_open').'</a>
                              </div>'.NL.'
                          </td>'.NL.'
                       </tr></table>'.NL;
        }
        // the user maybe registered within group "all" but the registered flag is turned on
        // eigther the user has to be moved into group "user" or the flag to be switched off
        elseif(($user_mail['perm'] < 2) && (strlen($user_mail['userinfo']['mail'])>1)) {
            $issue_edit_resolution ='<table class="itd__tables">
                                     <tr>
                                        <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('th_resolution').'</td>
                                    </tr>';
            $issue_edit_resolution .= '<tr class="itd__tables_tr">
                                        <td class="itd_resolution_tr" colSpan="2" style="padding-left:0.45em;">'.$this->xs_format($x_resolution).'</td>
                                      </tr></table>'.NL;

            $wmsg = $this->getLang('lbl_lessPermission'); 
            $issue_edit_resolution .= '<div class="it__standard_feedback">'.$wmsg.'</div>';                      
        }
        else {
            $issue_edit_resolution ='<table class="itd__tables">
                                     <tr>
                                        <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('th_resolution').'</td>
                                    </tr>';
            $issue_edit_resolution .= '<tr>
                                        <td class="itd_comment_tr">'.$this->xs_format($x_resolution).'</td>
                                      </tr></table>'.NL;

            $wmsg = $this->getLang('lbl_please').'<a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">'.$this->getLang('lbl_signin'); 
            $issue_edit_resolution .= '<div class="it__standard_feedback">'.$wmsg.'</div>';                      
        }

        
        //2011-12-02: bwenz code proposal (Issue 11)                                   
//        $ret = $issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment;
        $usr = '<span style="display:none;" id="currentuser">'.$user_grp['userinfo']['name'].'</span>' ;  //to log issue mods
        $ret = $usr.$issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_workaround . $issue_comments_log . $issue_add_comment . $issue_edit_resolution;

        return $ret;
    }

/******************************************************************************/
/******************************************************************************/
/* send an e-mail to user due to issue resolution
*/                            
    function _emailForRes($project,$issue)
    {       if($this->getConf('userinfo_email') ===0) return;

            if ($this->getConf('mail_templates')==1) {
                // load user html mail template
                $sFilename = DOKU_PLUGIN.'issuetracker/mailtemplate/issue_resolved_mail.html';
                $bodyhtml = file_get_contents($sFilename);
            }

            $subject = sprintf($this->getLang('issue_resolved_subject'),$issue['id'], $project);
            $subject = mb_encode_mimeheader($subject, "UTF-8", "Q" );            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issue_resolved_intro').chr(10).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).chr(10).
                    $this->getLang('issue_resolved_text').$this->xs_format($issue['resolution']).chr(10).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end');

            $body = html_entity_decode($body);

            if ($this->getConf('mail_templates')==1) $bodyhtml = $this->replace_bodyhtml($bodyhtml, $pstring, $project, $issue, NULL);
            
            $from=$this->getConf('email_address') ;

            $to=$issue['user_mail'];
            if($to==='') $to=$this->getConf('email_address');

            $cc=$issue['add_user_mail'].', '.$issue['assigned'];
            if(stripos($to.$cc,$issue['assigned'])==false) $cc .=', '.$issue['assigned'];

            if ($this->getConf('mail_templates')==1) { 
              $headers = "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
              $this->mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
            }
    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                                          
    function _emailForIssueMod($project, $issue, $old_value, $column, $new_value)
    {     
//        if ($conf['plugin']['issuetracker']['userinfo_email']==1)
        {   global $ID;
            global $lang;
            global $conf;
            if($new_value == '') $new_value = $this->getLang('it__none');
            if($old_value == '') $old_value = $this->getLang('it__none');
                        
            if ($this->getConf('mail_templates')==1) {
                // load user html mail template
                $sFilename            = DOKU_PLUGIN.'issuetracker/mailtemplate/edit_issuemod_mail.html';
                $bodyhtml             = file_get_contents($sFilename);
                $comment              = array();
                $comment["field"]     = $column;
                $comment["old_value"] = $old_value;
                $comment["new_value"] = $new_value;
                $comment["timestamp"] = date($this->getConf('d_format'));
                $user_mail            = pageinfo();
                $comment["author"]    = $user_mail['userinfo']['mail'];
            }
            //issuemod_subject = 'Issue #%s on %s: %s';
            $subject = sprintf($this->getLang('issuemod_subject'), $issue['id'], $project, $this->getLang('th_'.$column));
            $subject = mb_encode_mimeheader($subject, "UTF-8", "Q" );
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            
            //issuemod_changes = The issue changed on %s from %s to %s.
            $changes = sprintf($this->getLang('issuemod_changes'),$this->getLang('th_'.$column), $old_value, $new_value);

            $body = chr(10).$this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issuemod_intro').chr(10).
                    $changes.chr(10).chr(10).
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).
                    $this->getLang('issuemod_assignee').$issue['assigned'].chr(10).                    
                    $this->getLang('issuenew_descr').$issue['description'].chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$this->getLang('issuemod_end');
            $body = html_entity_decode($body);
            if ($this->getConf('mail_templates')==1) $bodyhtml = $this->replace_bodyhtml($bodyhtml, $pstring, $project, $issue, $comment);
            
            $from=$this->getConf('email_address'). "\r\n";
            
            $user_mail = pageinfo();
            if($user_mail['userinfo']['mail']===$issue['user_mail']) $to=$issue['assigned'];
            elseif($user_mail['userinfo']['mail']===$issue['assigned']) $to=$issue['user_mail'];
            else $to=$issue['user_mail'].', '.$issue['assigned'];
            if($to==='') $to=$this->getConf('email_address');
            
            $cc=$issue['add_user_mail'];
            if ($this->getConf('mail_templates')==1) { 
              $this->mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
            }
        }
    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                            
    function _emailForMod($project,$issue,$comment,$reason)
    {       if($this->getConf('userinfo_email') ===0) return;
            global $ID;
            
            if ($this->getConf('mail_templates')==1) {
                // load user html mail template
                $sFilename = DOKU_PLUGIN.'issuetracker/mailtemplate/cmnt_mod_mail.html';
                $bodyhtml = file_get_contents($sFilename);
            }
            if($reason     =='new')        { $subject = sprintf($this->getLang('cmnt_new_subject'),$issue['id'], $project). "\r\n"; }
            elseif($reason =='delete')     { $subject = sprintf($this->getLang('cmnt_del_subject'),$issue['id'], $project). "\r\n"; }
            elseif($reason =='workaround') { $subject = sprintf($this->getLang('cmnt_wa_subject') ,$issue['id'], $project). "\r\n"; }
            else {                           $subject = sprintf($this->getLang('cmnt_mod_subject'),$issue['id'], $project). "\r\n"; }            
            
            $subject = mb_encode_mimeheader($subject, "UTF-8", "Q" );
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            
            if($reason =='delete') {
              $body2     = $this->getLang('cmt_del_intro').chr(10).chr(13);
              $bodyhtml = str_ireplace("%%bodyhtml2%%",$this->getLang('cmt_del_intro'),$bodyhtml);
            }
            elseif($reason =='workaround') {
              $body2 = $this->getLang('issuemod_intro').chr(10).chr(13);
              $bodyhtml = str_ireplace("%%bodyhtml2%%",$this->getLang('issuemod_intro'),$bodyhtml); 
              $body3 = $this->getLang('issuemod_cmntauthor').$comment['author'].chr(10).
                       $this->getLang('issuemod_date').date($this->getConf('d_format'),strtotime($comment['timestamp'])).chr(10).
                       $this->getLang('issuemod_cmnt').chr(10).$this->xs_format($comment['wround_mod']).chr(10).chr(10); 
            }else {
              $body2 = $this->getLang('issuemod_intro').chr(10).chr(13);
              $bodyhtml = str_ireplace("%%bodyhtml2%%",$this->getLang('issuemod_intro'),$bodyhtml); 
              $body3 = $this->getLang('issuemod_cmntauthor').$comment['author'].chr(10).
                       $this->getLang('issuemod_date').date($this->getConf('d_format'),strtotime($comment['timestamp'])).chr(10).
                       $this->getLang('issuemod_cmnt').chr(10).$this->xs_format($comment['comment']).chr(10).chr(10); 
            }
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $body2.
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).
                    $this->getLang('issuemod_assignee').$issue['assigned'].chr(10).                    
                    $body3.
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end'). "\r\n";

            $body = html_entity_decode($body);

            if ($this->getConf('mail_templates')==1) $bodyhtml = $this->replace_bodyhtml($bodyhtml, $pstring, $project, $issue, $comment);
                        
            $from=$this->getConf('email_address'). "\r\n";
            
            $user_mail = pageinfo();
            if($user_mail['userinfo']['mail']===$issue['user_mail']) $to=$issue['assigned'];
            elseif($user_mail['userinfo']['mail']===$issue['assigned']) $to=$issue['user_mail'];
            else $to=$issue['user_mail'].', '.$issue['assigned'];
            if(strlen($to)<3) $to=$this->getConf('email_address');

            $cc=$issue['add_user_mail'];
            if ($this->getConf('mail_templates')==1) { 
              $this->mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
            }
    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion on Descriptions
*/                            
    function _emailForDscr($project,$issue)
    {       if($this->getConf('userinfo_email') ===0) return;
            global $ID;

            if ($this->getConf('mail_templates')==1) {
                // load user html mail template
                $sFilename = DOKU_PLUGIN.'issuetracker/mailtemplate/issue_descr_mail.html';
                $bodyhtml = file_get_contents($sFilename);
            }
            $subject = sprintf($this->getLang('issuedescrmod_subject'),$issue['id'], $project). "\r\n";
            $subject = mb_encode_mimeheader($subject, "UTF-8", "Q" );            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issuemod_intro').chr(10).chr(13).
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).
                    $this->getLang('issuemod_assignee').$issue['assigned'].chr(10).chr(10).
                    $this->getLang('issuemod_date').date($this->getConf('d_format'),strtotime($comment['timestamp'])).chr(10).chr(10).
                    $this->getLang('th_description').chr(10).$issue['description'].chr(10).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end'). "\r\n";
            
            if ($this->getConf('mail_templates')==1) $bodyhtml = $this->replace_bodyhtml($bodyhtml, $pstring, $project, $issue, $comment);
            
            $body = html_entity_decode($body);
            $from=$this->getConf('email_address'). "\r\n";
            
            $user_mail = pageinfo();
            if($user_mail['userinfo']['mail']===$issue['user_mail']) $to=$issue['assigned']. "\r\n";
            elseif($user_mail['userinfo']['mail']===$issue['assigned']) $to=$issue['user_mail']. "\r\n";
            else $to=$issue['user_mail'].', '.$issue['assigned']. "\r\n";

            $cc=$issue['add_user_mail']. "\r\n";
            if ($this->getConf('mail_templates')==1) { 
              $this->mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
            }
    }
/******************************************************************************/
    /***********************************
     * HTML Mail functions
     *
     * Sends HTML-formatted mail
     * By Lin Junjie (mail [dot] junjie [at] gmail [dot] com)
     *
     ***********************************/
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
    function replace_bodyhtml($bodyhtml, $pstring, $project, $issue, $comment) {
        global $ID;
//        echo "ID = ". $ID . "<br />";
        $bodyhtml = str_ireplace("%%_SEE%%",DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_head%%",$this->getLang('issuemod_head'),$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_intro%%",$this->getLang('issuemod_intro'),$bodyhtml);

        $bodyhtml = str_ireplace("%%issuemod_issueid%%",$this->getLang('issuemod_issueid'),$bodyhtml);
        $bodyhtml = str_ireplace("%%ID%%",$issue['id'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_title%%",$this->getLang('issuemod_title'),$bodyhtml);
        $bodyhtml = str_ireplace("%%TITEL%%",$issue['title'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_status%%",$this->getLang('issuemod_status'),$bodyhtml);
        $bodyhtml = str_ireplace("%%STATUS%%",$issue['status'],$bodyhtml);
        $bodyhtml = str_ireplace("%%th_project%%",$this->getLang('th_project'),$bodyhtml);
        $bodyhtml = str_ireplace("%%PROJECT%%",$project,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_product%%",$this->getLang('issuemod_product'),$bodyhtml);
        $bodyhtml = str_ireplace("%%PRODUCT%%",$issue['product'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_version%%",$this->getLang('issuemod_version'),$bodyhtml);
        $bodyhtml = str_ireplace("%%VERSION%%",$issue['version'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_severity%%",$this->getLang('issuemod_severity'),$bodyhtml);
        $bodyhtml = str_ireplace("%%SEVERITY%%",$issue['severity'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_creator%%",$this->getLang('issuemod_creator'),$bodyhtml);
        $bodyhtml = str_ireplace("%%CREATOR%%",$issue['user_name'],$bodyhtml);
        $bodyhtml = str_ireplace("%%CREATOR_MAIL%%",$issue['user_mail'],$bodyhtml);
        $bodyhtml = str_ireplace("%%th_assigned%%",$this->getLang('th_assigned'),$bodyhtml);
        $bodyhtml = str_ireplace("%%ASSIGNED%%",$issue['assigned'],$bodyhtml);
        $bodyhtml = str_ireplace("%%th_created%%",$this->getLang('th_created'),$bodyhtml);
        $bodyhtml = str_ireplace("%%CREATED%%",$issue['created'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issueassigned_head%%",$lang['issueassigned_head'],$bodyhtml);
        $bodyhtml = str_ireplace("%%issueassigned_intro%%",$lang['issueassigned_intro'],$bodyhtml);

        $bodyhtml = str_ireplace("%%issue_resolved_intro%%",$this->getLang('issue_resolved_intro'),$bodyhtml);
        $bodyhtml = str_ireplace("%%issue_resolved_text%%",$this->getLang('issue_resolved_text'),$bodyhtml);
        $frmt_res = str_ireplace(chr(10),"<br />",$issue['resolution']);
        $bodyhtml = str_ireplace("%%RESOLUTION%%",$this->xs_format($frmt_res),$bodyhtml);
        $bodyhtml = str_ireplace("%%TIMESTAMP%%",date($this->getConf('d_format')),$bodyhtml);
        
        $user_grp = pageinfo();        
        $usr      = $user_grp['userinfo']['name'] ; 
        $bodyhtml = str_ireplace("%%RESOLVER%%",$usr,$bodyhtml);
        $bodyhtml = str_ireplace("%%MOD_BY%%",$usr,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuedescrmod_subject%%",sprintf($this->getLang('issuedescrmod_subject'),$issue['id'], $project),$bodyhtml);
        $bodyhtml = str_ireplace("%%th_description%%",$this->getLang('th_description'),$bodyhtml);
        $frmt_descr = str_ireplace(chr(10),"<br />",$issue['description']);
        $bodyhtml = str_ireplace("%%DESCRIPTION%%",$this->xs_format($frmt_descr),$bodyhtml);
        
                        
//        if($comment) {
            $bodyhtml = str_ireplace("%%lbl_cmts_wlog%%",$this->getLang('lbl_cmts_wlog'),$bodyhtml);
            $bodyhtml = str_ireplace("%%CMNT_ID%%",$comment['id'],$bodyhtml);
            $bodyhtml = str_ireplace("%%EDIT_AUTHOR%%",$comment['author'],$bodyhtml);
            $bodyhtml = str_ireplace("%%CMNT_AUTHOR%%",$comment['author'],$bodyhtml);
            $bodyhtml = str_ireplace("%%CMNT_TIMESTAMP%%",date($this->getConf('d_format'),strtotime($comment['timestamp'])),$bodyhtml);
            $frmt_cmnt = str_ireplace(chr(10),"<br />",$comment['comment']);
            $bodyhtml = str_ireplace("%%COMMENT%%",$this->xs_format($frmt_cmnt),$bodyhtml);
            $bodyhtml = str_ireplace("%%FIELD%%",str_ireplace(chr(10),"<br />",$comment["field"]),$bodyhtml);
            $bodyhtml = str_ireplace("%%OLD_VALUE%%",$this->xs_format(str_ireplace(chr(10),"<br />",$comment["old_value"])),$bodyhtml);
            $bodyhtml = str_ireplace("%%NEW_VALUE%%",$this->xs_format(str_ireplace(chr(10),"<br />",$comment["new_value"])),$bodyhtml);
//        }
        $bodyhtml = str_ireplace("%%issuemod_br%%",$this->getLang('issuemod_br'),$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_end%%",$this->getLang('issuemod_end'),$bodyhtml);
        
//        echo $bodyhtml;
        
        return $bodyhtml;
    }
/******************************************************************************/
/* pic-up a single value
*/
    function _get_one_value($issue, $key) {
//        echo $key. " => " . $issue . "<br />";
        if (@array_key_exists($key,$issue)) return $issue[$key];
        else return '';
    }
/******************************************************************************/
/* elaborate the display string of assignee (login, name or mail)
*/
    function _get_assignee($issue, $key) {
        if (array_key_exists($key,$issue)) {
            global $auth;
            global $conf;

            $filter['grps']  = $this->getConf('assign');
            $usr_array       = $auth->retrieveUsers(0,0,$filter);
            $shw_assignee_as = trim($this->getConf('shw_assignee_as'));

            if(stripos("login, mail, name",$shw_assignee_as) === false) $shw_assignee_as = "login";
            foreach ($usr_array as $u_key => $usr)
            {     if($usr['mail']==$issue[$key]) 
                  {   if    ($shw_assignee_as == 'login') { return $u_key;       }
                      elseif($shw_assignee_as == 'mail')  { return $usr['mail']; }
                      else                                { return $usr['name']; }
                  }
            } 
        }
        if(stripos("mail",$shw_assignee_as) !== false) { return $issue[$key]; }
        else 
        {   $b_display = explode("@",$issue[$key]);
            return $b_display[0];
        }
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
/* Create count output
*/
    function _count_render($issues,$start,$step,$next_start,$filter,$project)
    {   global $ID;
        $count = array();
        foreach ($issues as $issue)
        {
            if(($issue['project'] !== $project) && ($this->getConf('multi_projects')==0)) {
              continue;
            }
            elseif ((strcasecmp($filter['product'],'ALL')===0) || (stristr($filter['product'],$this->_get_one_value($issue,'product'))!= false))
            {   $status = trim($this->_get_one_value($issue,'status'));
                $a_count = $a_count + 1;
                if (($status != '') && (stripos($this->getConf('status_special'),$status)===false))
                {    if ($this->_get_one_value($count,strtoupper($status))=='')
                        {$count[strtoupper($status)] = array(1,$status);}
                    else
                        {$count[strtoupper($status)][0] += 1;}                           
                }
            }                                
        }
        $rendered_count = '<div class="itl__count_div">'.'<table class="itl__count_tbl">';
        foreach ($count as $value)
        {
            //http://www.fristercons.de/fcon/doku.php?id=issuetracker:issuelist&do=showcaselink&showid=19&project=fcon_project
            // $ID.'&do=issuelist_filter&itl_sev_filter='.$value[1]
            $rendered_count .= '<tr><td><a href="'.DOKU_URL.'doku.php?id='.$ID.'&do=issuelist_filterlink'.'&itl_start='.$start.'&itl_step='.$step.'&itl_next='.$next_start.'&itl_stat_filter='.$value[1].'&itl_sev_filter='.$filter['severity'].'&itl__prod_filter='.$filter['product'].'&itl_project='.$project.'" >'.$value[1].'</a>&nbsp;</td><td>&nbsp;'.$value[0].'</td></tr>';
        }
        $rendered_count .= '</table></div>';
        $ret_array = array($a_count,$rendered_count);
        return $ret_array;
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

        $x_comment = preg_replace('/\[code\]/i', '<div class="it_code"><code>', $x_comment);
        $x_comment = preg_replace('/\[\/code\]/i', '</code></div>', $x_comment);    

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
/* return html-code for edit toolbar
*/
    function it_wysiwyg_edit_toolbar($xa_comment) {
        $sFilename    = DOKU_BASE."lib/plugins/issuetracker/wysiwyg_editor.js";
        $it_edit_tb  .= '<script type="text/javascript" src="'.$sFilename.'"></script>';
        $sFilename    = DOKU_PLUGIN.'issuetracker/wysiwyg_editor.html';
        $it_edit_tb  .= file_get_contents($sFilename);
        $it_edit_tb   = str_ireplace("%%DOKU_BASE%%",DOKU_BASE,$it_edit_tb);
        $trans=get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
        $trans=array_flip($trans);
        $x_comment=strtr($xa_comment, $trans);

        
        $it_edit_tb   = str_ireplace('<p>&nbsp;</p>',$x_comment,$it_edit_tb);                  
        return $it_edit_tb;
    }

    function it_xs_edit_toolbar($type) {
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $it_edit_tb  = '<div class="it_edittoolbar">'.NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."bold.png\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('[b]','[/b]','$type')\">".NL;
        $it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."italic.png\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('[i]','[/i]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."underline.png\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('[u]','[/u]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."strikethrough.png\" name=\"btnStrike\" title=\"Strike through\" onClick=\"doAddTags('[s]','[/s]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."subscript.png\" name=\"btnSubscript\" title=\"Subscript\" onClick=\"doAddTags('[sub]','[/sub]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."superscript.png\" name=\"btnSuperscript\" title=\"Superscript\" onClick=\"doAddTags('[sup]','[/sup]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."hr.png\" name=\"btnLine\" title=\"hLine\" onClick=\"doHLine('[hr]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."ordered.png\" name=\"btn_o_List\" title=\"Ordered List\" onClick=\"doList('[ol]','[/ol]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."unordered.png\" name=\"btn_u_List\" title=\"Unordered List\" onClick=\"doList('[ul]','[/ul]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."quote.png\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('[blockquote]','[/blockquote]','$type')\">".NL; 
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."code.png\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('[code]','[/code]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."pen_red.png\" name=\"btnRed\" title=\"Red\" onClick=\"doAddTags('[red]','[/red]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."pen_green.png\" name=\"btnGreen\" title=\"Green\" onClick=\"doAddTags('[grn]','[/grn]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."pen_blue.png\" name=\"btnBlue\" title=\"Blue\" onClick=\"doAddTags('[blu]','[/blu]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."bg_yellow.png\" name=\"btn_bgYellow\" title=\"bgYellow\" onClick=\"doAddTags('[bgy]','[/bgy]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."link.png\" name=\"btn_link\" title=\"Link\" onClick=\"doAddTags('[link]','[/link]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."img.png\" name=\"btn_img\" title=\"Image - max width 850px\" onClick=\"doAddTags('[img]','[/img]','$type')\">".NL;
      	$it_edit_tb .= "<a href=\"http://www.imageshack.us/\" target=\"_blank\"><<img class=\"xseditor_button\" src=\"".$imgBASE."imageshack.png\" name=\"btn_ishack\" title=\"ImageShack upload (ext TaC !)\">></a>".NL;
        $it_edit_tb .= "<br></div>".NL; 
        return $it_edit_tb;                     
    }
/******************************************************************************/
    function get_issues_file_contents($project, $issue_id) {
        if($this->getConf('it_data')==false) $pfile = DOKU_CONF."../data/meta/".$project.'.issues';
        else $pfile = DOKU_CONF."../". $this->getConf('it_data').$project.'.issues';
        if ((@file_exists($pfile)) && (strlen($project)>1))
        	{  $issue  = unserialize(@file_get_contents($pfile));
             // check if ID exist
             $cFlag = false;
             foreach ($issue as $issue_item)  {
                if ($issue_item['id'] == $issue_id) {
                    $cFlag = true;
                    return $issue;
                    break;
                }
             }
             if ($cFlag === false) {
             // promt error message that issue with this ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_issuemissing').$issue_id.'.</div><br />';
//              echo $Generated_Header;
              return;
             }
          }
        else
        	{   // promt error message that issue with ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">'.sprintf($this->getLang('msg_pfilemissing'),$pfile).'</div><br />';
//              echo $Generated_Header;
              return;
          }
    }
/******************************************************************************/
/* sort the issues array according the selected key                           */                                          
    function _issues_globalsort($issues, $sort_key) {
/*        $tmp = Array(); 
        foreach($issues as &$ma)  $tmp[] = &$ma[$sort_key]; 
        array_multisort($tmp, $issues);                          */
        foreach ($issues as $key => $row) {
            $down[$key]    = $row['id'];
            $up[$key]      = $row[$sort_key];
        }
        if($up) { @array_multisort($up, constant($this->getConf('global_sort')), $down, SORT_ASC, $issues); }
        return $issues;
    }
/******************************************************************************/
/* log issue modificaions
 * who changed what and when per issue
*/                                          
    function _log_mods($project, $issue, $usr, $column, $old_value, $new_value)
    {     global $conf;
          // get mod-log file contents
          $issue_id = $issue['id'];
          if($this->getConf('it_data')==false) $modfile = DOKU_CONF."../data/meta/".$project.'_'.$issue_id.'.mod-log';
          else $modfile = DOKU_CONF."../". $this->getConf('it_data').$project.'_'.$issue_id.'.mod-log';
          if (@file_exists($modfile))
              {$mods  = unserialize(@file_get_contents($modfile));}
          else 
              {$mods = array();}
          $cur_date = date($this->getConf('d_format'));
          $mod_id = count($mods);
          if($new_value=='') $new_value = $this->getLang('mod_valempty');
          $mods[$mod_id]['timestamp'] = $cur_date;
          $mods[$mod_id]['user']      = $usr;
          $mods[$mod_id]['field']     = $column;
          $mods[$mod_id]['old_value'] = $old_value;
          $mods[$mod_id]['new_value'] = $new_value;
          // Save issues file contents
          $fh = fopen($modfile, 'w');
          fwrite($fh, serialize($mods));
          fclose($fh);
    }
/******************************************************************************/
/* replace special characters in file names like German "Umlaute"
*/
  function img_name_encode($f_name) {
      $umlaute = explode(',',$this->getLang('umlaute'));
      $replace = explode(',',$this->getLang('conv_umlaute'));
      if((count($umlaute)>1) && (count($replace)>1)) $f_name = str_replace($umlaute, $replace, $f_name);
      //preg_replace($umlaute, $replace, $f_name);

      $f_name = strtolower($f_name);
      return $f_name;
  }
/******************************************************************************/

/*******************************************************************************
* The eventhandlers to catch profile updates (e-mail address)
*******************************************************************************/
/* -------------------------------------------------------------------------- */
  function handle_usermod_before(&$event, $param)
	{   if($this->getConf('profile_updt')==false) return;
      if($this->getConf('it_data')==false) $filename = DOKU_CONF."../data/meta/it_eventcheck.txt";
      else $filename = DOKU_CONF."../". $this->getConf('it_data').'it_eventcheck.txt';

      if (!$handle = fopen($filename, 'w')) {
          msg("IssueTracker: Failed to create the eventcheck file.",-1);
          return;    
      }
      // search dokuwiki user-db for client and e-mail address before change
      // store it for AFTER event comparison
      global $auth;
      $user = array();
      $user['microtime_start'] = microtime(true);
      $user['client'] = $event->data['params'][0];
      $usr_info = $auth->getUserData($user['client']);
      $user['name'] = $usr_info['name'];
      $user['mail'] = $usr_info['mail'];

      fwrite($handle, serialize($user));
//      fwrite($handle, $eve);
      
      fclose($handle);
 }
/* -------------------------------------------------------------------------- */
  function handle_usermod_after(&$event, $param)
	{   //$filename = DOKU_INC . 'it_eventcheck.txt';
      if($this->getConf('profile_updt')==false) return;
      if($this->getConf('it_data')==false) $filename = DOKU_CONF."../data/meta/it_eventcheck.txt";
      else $filename = DOKU_CONF."../". $this->getConf('it_data').'it_eventcheck.txt';
      
      global $auth;
      $result .= "* function handle_usermod_after".chr(10);
      $user = array();      
      $user['client'] = $event->data['params'][0];
      $usr_info       = $auth->getUserData($user['client']);
      $user['name']   = $usr_info['name'];
      $user['mail']   = $usr_info['mail'];

      $user_before    = unserialize(@file_get_contents($filename));
      $result        .= ' --- BEFORE --- | --- AFTER ---'.chr(10);
      $result        .= $user_before['client'].' <= ? => '. $user['client'].chr(10);
      $result        .= $user_before['name']  .' <= ? => '. $user['name'].chr(10);
      $result        .= $user_before['mail']  .' <= ? => '. $user['mail'].chr(10);

      if(($user_before['mail'] !== false) && ($user_before['mail'] !== $user['mail'])) {
          $result  .= 'DIFF in mail => update issues, comments and modlog'.chr(10);
          $this->_update_it_files($user_before, $user);
          // finally the eventcheck will contain the values and resulting action info
          if (!$handle = fopen($filename, 'a')) {
              msg("IssueTracker: Failed to write into eventcheck file.",-1);
              return;    
          }
          $perf = 'IssueTracker updates took '.microtime(true) - $user['microtime_start'].' seconds';
          $result .= chr(10).chr(10).$perf.chr(10);
          fwrite($handle, $result);
          fclose($handle);
          if($perf>0) msg($perf,0);
      }
      else $result .= chr(10).chr(10).'NO diff in mail => do nothing';
      
      $handle = fopen($filename, 'a');
      fwrite($handle, $result);
      fclose($handle);

 }
/* -------------------------------------------------------------------------- */
// replace user mail address after modification of user profile
  function _update_it_files($user_before, $user) {
      global $conf;
      
      if($this->getConf('profile_updt')==false) return;
      //$filename = DOKU_INC . 'it_eventcheck.txt';
      if($this->getConf('it_data')==false) $filename = DOKU_CONF."../data/meta/it_eventcheck.txt";
      else $filename = DOKU_CONF."../". $this->getConf('it_data').'it_eventcheck.txt';
      // create array of related files
      if($this->getConf('it_data')==false) $path = DOKU_CONF."../data/meta/";
      else $path = DOKU_CONF."../".$this->getConf('it_data');
      $file_array = $this->_file_list($path, '.issues.cmnts.mod-log');

      $result .= "* function _update_it_files".chr(10);

      if((strlen($user_before['mail'])<1) ) {
          $result  .= "Can't update IssueTracker records due to missing old mail value. \nThis may lead into troubles for the just updated user. \nBetter to turn back the changes same way (except on passwords) and use the Update Profile action of your template.";
      }
      elseif(strlen($user['mail'])<1) {
          $result  .= "Can't update IssueTracker records due to missing new user mail value. \nThis may lead into troubles for the just updated user. \nBetter to turn back the changes same way (except on passwords or user deletion) and use the Update Profile action of your template.";
      }
      else {
          $result = chr(10).chr(10)."Loop thorugh IssueTracker data files.".chr(10);
          // loop through all files
          foreach($file_array as $file) {
              $parts = explode(".", $file);
              $extension = end($parts);
              $pfile = $path.'/'.$file;
    
              if (@file_exists($pfile)) {
                  switch ($extension) {
                    case "issues":
                        // get issues file contents
                        $issues  = unserialize(@file_get_contents($pfile));
                        foreach($issues as &$issue) {
                            // search for old_mail and replace by new_mail
                            if($issue['user_mail'] == $user_before['mail']) {
                                $issue['user_name'] = $user['name'];
                                $issue['user_mail'] = $user['mail'];
                                $result .= 'Issue ID '.$issue['id'].': author field successfully updated'.chr(10);
                                $upd_issues++;
                            }
                            // delete mail address from followers
                            if((stripos($issue['add_user_mail'],$user_before['mail']) !== false)) {
                                      $tmp = explode(',', $issues[$issue_id]['add_user_mail']);
                                      foreach($tmp as $email) {
                                          if (stripos($email,$user_before['mail']) === false) $ret_mails .= $email.',';
                                          else {
                                              $ret_mails .= $user['mail'].',';
                                              $result .= 'Issue ID '.$issue['id'].': follower field successfully updated.'.chr(10);
                                              $upd_folllowers++;
                                          }
                                      } 
                            }
                            if($issue['assigned']==$user_before['mail']) { 
                                $issue['assigned'] = $user['mail'];
                                $result .= 'Issue ID '.$issue['id'].': assignee field successfully updated.'.chr(10); 
                                $upd_assignments++;
                            }
                        }
                        // store issue file
                        $xvalue = io_saveFile($pfile,serialize($issues));                   
                        
                    case "cmnts":
                        // get comments file contents
                        $comments  = unserialize(@file_get_contents($pfile));
                        foreach($comments as &$comment) {
                            if($comment['author'] == $user_before['mail'])  {
                                $comment['author'] = $user['mail']; // search for old_mail and replace by new_mail
                                $result .= '('.$file.') Comment #'.$comment['id'].': author field successfully updated.'.chr(10);
                                $upd_comments++;
                            }
                        }
                        $xvalue = io_saveFile($pfile,serialize($comments));
                      
                    case "mod-log":
                        // loop through all mod-log files
                        $mods  = unserialize(@file_get_contents($pfile));
                        foreach($mods as &$mod) {
                            if($mod['new_value'] == $user_before['mail']) {
                                $mod['new_value'] = $user['mail']; // search for old_mail and replace by new_mail
                                $result .= '('.$file.') modification logfile successfully updated.'.chr(10);
                                $upd_modlog_entries++;
                            }
                        }
                        $xvalue = io_saveFile($pfile,serialize($mods));
                  }
              }
              else msg('File: '.$pfile.'does not exist',-1);
          }
      }

      // provide user-feedback & log ---------------------------------
      if($this->getConf('it_data')==false) $filename = DOKU_CONF."../data/meta/it_eventcheck.txt";
      else $filename = DOKU_CONF."../". $this->getConf('it_data').'it_eventcheck.txt';

      if (!$handle = fopen($filename, 'a')) {
          msg("IssueTracker: Failed to write into eventcheck file.",-1);
          return;    
      }
      
      if(($upd_issues>0) && ($conf['allowdebug']!= false)) {
          msg('IssueTracker: '.$upd_issues." issue creator entries updated",0);
      }
      $result  .= chr(10).'IssueTracker: '.intval($upd_issues)." issue creator entries updated".chr(10);
      
      if(($upd_folllowers>0) && ($conf['allowdebug']!= false)) {
          msg('IssueTracker: '.$upd_folllowers." follower entries updated",0);
      }
      $result .= 'IssueTracker: '.intval($upd_folllowers)." follower entries updated".chr(10);
      
      if(($upd_assignments>0) && ($conf['allowdebug']!= false))  {
          msg('IssueTracker: '.$upd_assignments." assignments updated",0);
      }
      $result .= 'IssueTracker: '.intval($upd_assignments)." assignments updated".chr(10);
      
      if(($upd_comments>0) && ($conf['allowdebug']!= false))    {
          msg('IssueTracker: '.$upd_comments." comment author entries updated",0);
      }
      $result .= 'IssueTracker: '.intval($upd_comments)." comment author entries updated".chr(10);
      
      if(($upd_modlog_entries>0) && ($conf['allowdebug']!= false))  {
          msg('IssueTracker: '.$upd_modlog_entries." mod-log entries updated",0);
      }
      $result .= 'IssueTracker: '.intval($upd_modlog_entries)." mod-log entries updated".chr(10);
      $result  .= chr(10).chr(10);
      if($conf['allowdebug']!= false)   $result  .= 'allowdebug = true'.chr(10);
      else $result  .= 'allowdebug = false'.chr(10);
      
      $handle = fopen($filename, 'a');
      fwrite($handle, $result);
      fclose($handle);

      return;  
  }
/* -------------------------------------------------------------------------- */
  // list all files with defined file-extension within a directory 
  // does not read sub-directories
  function _file_list($dir, $type) {
      $dh = opendir($dir); 
      $files = array(); 
      while (($file = readdir($dh)) !== false) { 
          $flag = false; 
          if($file !== '.' && $file !== '..') { 
          // --- get the current file extension ---------------------
              $parts = explode(".", $file);
              if (is_array($parts) && count($parts) > 1) {
                  $extension = end($parts);
                  if (stripos($type,$extension)!==false) {
                      $files[] = $file; 
                      $a = $file.chr(10);
                  }
              }
          //---------------------------------------------------------
          } 
      }
      return $files;
  }
/******************************************************************************/ 
/* upload a file if valid on mime type and file extension
*/
  function _symptom_file_upload(&$issues, $issue_id, $attachment_id) {
      global $conf;
      if($this->getConf('it_data')==false) $target_path = "data/meta/";
      else $target_path = $this->getConf('it_data');
      $ip_block_path = $target_path."ipblock";
      $target_path .= 'symptoms/';
      if(!is_dir(DOKU_CONF."../".$target_path)) { mkdir(DOKU_CONF."../".$target_path, 0777); }                                                                                

      $valid_file_extensions = array();
      $valid_mimetypes = array(); 
      $mimetypes = getMimeTypes();

      foreach($mimetypes as $key => $value) {
          $valid_file_extensions[] = $key;
          $valid_mimetypes[] = $value;
      }
      
      if($this->getConf('ip_blocked') == 1){
          $ip_blocked_sec = $this->getConf('ip_blockd_time')*60;
           
          // search folder ipblock
          if(is_dir(DOKU_INC.$ip_block_path)) { 
              $path = openDir(DOKU_INC.$ip_block_path); 
              while(false !== ($filename = readdir($path))){ 
                  if($filename != "." && $filename != ".."){
                      // delete aged ipblocks
                      if(file_exists(DOKU_INC.$ip_block_path.'/'.$filename)) {
                          $t_check = filemtime(DOKU_INC.$ip_block_path.'/'.$filename)+$ip_blocked_sec;
                          if($t_check <= time()) { @unlink(DOKU_INC.$ip_block_path.'/'.$filename); }
                      }
                  }
              }
              closedir($path); 
          }
          else {
              mkdir(DOKU_INC.$ip_block_path.'/', 0777); 
          }         
          
          $ip_addr = $_SERVER['REMOTE_ADDR'];
          if($ip_addr == "") {
            if(getenv(HTTP_X_FORWARDED_FOR)) { $ip_addr = getenv('HTTP_X_FORWARD_FOR'); }
            else { $ip_addr = getenv('REMOTE_ADDR'); }
          }
      
          if($ip_addr != ""){
              // check if ip already known
              if(file_exists(DOKU_INC.$ip_block_path.'/'.$ip_addr)) {
                  // check upload attampts (to be larger than 3)
                  $iplog = fopen(DOKU_INC.$ip_block_path.'/'.$ip_addr, "r");
                  $attachments_left=fread($iplog, filesize(DOKU_INC.$ip_block_path.'/'.$ip_addr)); 
                  fclose($iplog);
                  if($attachments_left<1) {
                    $error_code = 1;
                    $t_check = intval((filemtime(DOKU_INC.$ip_block_path.'/'.$filename)+$ip_blocked_sec-time())/60); 
                    msg(sprintf($this->getLang('wmsg9'), $t_check),-1);
                  }
              }
              else $attachments_left = 3;
          } 
      }

      if(isset($error_code)){ 
        $t_check = intval((filemtime(DOKU_INC.$ip_block_path.'/'.$filename)+$ip_blocked_sec-time())/60);
        $Generated_Header = '<div class="it__negative_feedback">'.sprintf($this->getLang('wmsg9'), $t_check).'</div>';
        $renderer->doc .= $Generated_Header;
        return;
      }      

      // get file extension 
      $mime_type = $_FILES[$attachment_id]['type'];    
      $file_extension = strrchr($_FILES[$attachment_id]['name'],'.'); // last occurance of dot to detect extension
      $file_dot_extension = strtolower($file_extension);   
      $file_extension = str_replace(".", "", strtolower($file_dot_extension));  
      $error_flag = 0;
                     
      // check validity of file extension
      if(!in_array($file_extension, $valid_file_extensions)) {
        $error_flag = 1;
        $Generated_Header .= '<span>'.$this->getLang('wmsg7').' (File: <b>'.$_FILES[$attachment_id]['name'].'</b>)</span><br>'; 
      }
      // check mime type
      if((!in_array($mime_type, $valid_mimetypes)) && (!in_array("!".$mime_type, $valid_mimetypes)) ) {
        $error_flag = 1;
        $Generated_Header .= '<span>'.$this->getLang('wmsg8').' (File: <b>'.$_FILES[$attachment_id]['name'].', Mime-Type: '.$mime_type.'</b>)</span><br>';
      }
      // check file-size
      if($_FILES[$attachment_id]['size'] > ($this->getConf('max_fsize'))){
          $error_flag = 1;
          $Generated_Header .= '<span>'.sprintf($this->getLang('wmsg6'), $this->getConf('max_fsize')).' (File: <b>'.$_FILES[$attachment_id]['name'].'</b>)</span><br>';
      }                
// -----------------------------------------------------------------------------
    if($error_flag > 0) { 
      echo $Generated_Header = '<div class="it__negative_feedback">'.$Generated_Header.'</div>';
    }                  
    else {
      //$safe_filename = preg_replace(array("/\s+/", "/[^-\.\w]+/"),array("_", ""),trim(basename( $_FILES[$attachment_id]['name'])));
      // delete all other characters beside the following defined
      $safe_filename = preg_replace('#[^A-Za-z0-9_.-]#', '',trim(basename( $_FILES[$attachment_id]['name']))); 
      $target_path = $target_path . $issue_id . '_sympt_' . $safe_filename; 
      if(move_uploaded_file($_FILES[$attachment_id]['tmp_name'], DOKU_INC.$target_path)) {
          $attachments_left = $attachments_left-1;
          $issues[$issue_id][$attachment_id] = DOKU_URL.$target_path;
//          msg("The file ".$safe_filename." has been successfully uploaded to ".DOKU_URL.$target_path,1);
          msg("The file ".$safe_filename." has been successfully uploaded.",1);
      } else{
//          msg("There was an error uploading the file to ".DOKU_URL.$target_path." \n, please try again!",-1);
          msg("There was an error uploading the file, please try again!",-1);
      }
// -----------------------------------------------------------------------------
      // block ip
      if($this->getConf('ip_blocked') == 1) {
              $ip_addr=$_SERVER['REMOTE_ADDR']; 
              if($ip_addr==""){
                  if(getenv(HTTP_X_FORWARDED_FOR)) { $ip_addr = getenv('HTTP_X_FORWARD_FOR'); }
                  else { $ip_addr = getenv('REMOTE_ADDR'); }
              }
              if(!is_dir(DOKU_INC.$ip_block_path) && ($ip_addr != "")) { 
                  @mkdir(DOKU_INC.$ip_block_path.'/', 0777); 
                  $iplog = fopen(DOKU_INC.$ip_block_path.'/'.$ip_addr, "w+");
                  fwrite($iplog, $attachments_left); 
                  fclose($iplog); 
                  }
              elseif($ip_addr != ""){ 
                  $iplog = fopen(DOKU_INC.$ip_block_path.'/'.$ip_addr, "w+");
                  fwrite($iplog, $attachments_left); 
                  fclose($iplog); 
              }
          }            
    }
// -----------------------------------------------------------------------------
    return $Generated_Header;
  }
/******************************************************************************/
/* 
 * Check for MyIssues
 *  
 * Check if the issue is related to the current user
 * the user maybe the issue reporter, assignee or registered as follower      
 * it will return true/false
 *  
 * @author   Taggic <taggic@t-online.de>
 * @param    array $issue the single issue
 * @param    array $user the current user info  
 * @return   bool (true / false)
 *
 */
 
  function _find_myissues($issue, $user) {
      // current user is issue reporter
      if($user['userinfo']['mail'] === $issue['user_mail']) return true;
      
      // current user is assigned to this issue
      if($user['userinfo']['mail'] === $issue['assigned']) return true;
      
      // current user is registered as follower within the comments log of actual issue
      if(stristr($issue['add_user_mail'],$user['userinfo']['mail']) !== false) return true;
      
      // else return false
       return false;      
  }
/******************************************************************************/
  function __find_projects($path) { 
    if(!is_dir($path))  return false;  // prevent the php-warning
    if ($handle=opendir($path)) { 
      while (false!==($file=readdir($handle))) { 
        if ($file<>"." AND $file<>"..") { 
          if (is_file($path.'/'.$file)) { 
            $ext = explode('.',$file);
            $last = count($ext) - 1;
	          if ($ext[$last] == 'issues') {
              $projects .= ','.substr($file,0,strlen($file)-strlen('.issues'));
            }
          } 
        } 
      } 
    }
    return $projects; 
  }    
/******************************************************************************/
/* 
 * Load all issues into an array
 *  
 * Check if multi_project is set to true
 * Load current $project or all projects of it_data_store     
 * Return the issues array
 *  
 * @author   Taggic <taggic@t-online.de>
 * @param    string $project  delivers the project name; used if $all = false
 * @param    bool   $all      determines if all projects to be retrieved   
 * @return   array  $issues   result: is an array of issues
 *
 */
 
  function _get_issues($project, $all = false) {
    // detect the IssueTracker data store (path)
    if($this->getConf('it_data')==false) $it_datastore = DOKU_CONF."../data/meta/";
    else $it_datastore = DOKU_CONF."../". $this->getConf('it_data');
    
    // check if last sign is a slash
    $i = strrchr ($it_datastore, chr(47));     // chr(47) = "/"
    $j = strrchr ($it_datastore, chr(92));     // chr(92) = "\"
    if(($i !== strlen($it_datastore)) && ($i !== strlen($it_datastore))) { $it_datastore .= chr(47); }
    
    if(($this->getConf('multi_projects')!==false) && ($all !== false)) {
        // loop through it_datastore and list all .issues files
        $xprojects = $this->__find_projects($it_datastore);
        $x_projects = explode(',',$xprojects);
        $issues = array();
        $tmp    = array();
        
        foreach ($x_projects as $project)
        {   $project = trim($project);
            if(is_file($it_datastore.$project.'.issues') == true) {
                $tmp = unserialize(@file_get_contents($it_datastore.$project.'.issues'));
                
                // loop through the field and add project to each row
                foreach($tmp as &$tmps)
                {   $tmps['project'] = $project; }
                
                $issues = array_merge($issues, $tmp);
                $tmp = array();
            }
        }
    }
    else {
        // get issues from single project file
        if($this->getConf('it_data')==false) $pfile = $it_datastore.$project.'.issues';
        else $pfile = $it_datastore.$project.'.issues';
    
        if (@file_exists($pfile))
        	{$issues  = unserialize(@file_get_contents($pfile));}
        else
        	{$issues = array();
            msg("project =  $pfile  not found",-1);
          }
        }
//    $arr1 = $this->array_msort($issues, array('project'=>SORT_DESC, 'id'=>SORT_ASC));
    $arr1 = $this->array_msort($issues, array('project'=>SORT_DESC));
    return $arr1;
  }
/******************************************************************************/
  function array_msort($array, $cols)
   {
       $colarr = array();
       foreach ($cols as $col => $order) {
           $colarr[$col] = array();
           foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
       }
       $eval = 'array_multisort(';
       foreach ($cols as $col => $order) {
           $eval .= '$colarr[\''.$col.'\'],'.$order.',';
       }
       $eval = substr($eval,0,-1).');';
       eval($eval);
       $ret = array();
       foreach ($colarr as $col => $arr) {
           foreach ($arr as $k => $v) {
               $k = substr($k,1);
               if (!isset($ret[$k])) $ret[$k] = $array[$k];
               $ret[$k][$col] = $array[$k][$col];
           }
       }
       return $ret;   
  }
/******************************************************************************/
}