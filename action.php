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
         'date'   => '2012-02-22',
         'name'   => 'Issue comments (action plugin component)',
         'desc'   => 'to display comments of a dedicated issue.',
         'url'    => 'http://www.dokuwiki.org/plugin:issuetracker',
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
         if (($event->data === 'showcase') || ($event->data === 'store_resolution')){
             $this->parameter = $_POST['showid'];
             $this->project   = $_POST['project'];         
         }
         elseif ($event->data === 'showcaselink') {
            $this->parameter  = $_GET['showid'];
            $this->project    = $_GET['project'];
         }
         elseif($event->data === 'btn_add_contact') {
             $this->project     = $_POST['project'];
             $this->issue_ID    = $_POST['issue_ID'];
             $this->add_contact = $_POST['add_contact'];
         }
         elseif ($event->data === 'it_search') {
            $this->parameter  = $_POST['it_str_search'];
            $this->project    = $_POST['project'];
         }
         elseif ($event->data === 'issuelist_next') {
            $this->itl_start  = $_POST['itl_start'];
            $this->itl_step   = $_POST['itl_step'];
            $this->itl_next   = $_POST['itl_next'];
            $this->itl_pjct   = $_POST['itl_project'];
            $this->itl_stat   = $_POST['itl_stat_filter'];
            $this->itl_sev    = $_POST['itl_sev_filter'];
            $this->itl_prod   = $_POST['itl_prod_filter'];
         }
         elseif ($event->data === 'issuelist_previous') {
            $this->itl_start  = $_POST['itl_start'];
            $this->itl_step   = $_POST['itl_step'];
            $this->itl_next   = $_POST['itl_next'];
            $this->itl_pjct   = $_POST['itl_project'];
            $this->itl_stat   = $_POST['itl_stat_filter'];
            $this->itl_sev    = $_POST['itl_sev_filter'];
            $this->itl_prod   = $_POST['itl_prod_filter'];
         }
         elseif ($event->data === 'issuelist_filter') {
            $this->itl_start = $_POST['itl_start'];
            $this->itl_step = $_POST['itl_step'];
            $this->itl_next = $_POST['itl_next'];
            $this->itl_pjct = $_POST['itl_project'];
            $this->itl_stat = $_POST['itl_stat_filter'];
            $this->itl_sev = $_POST['itl_sev_filter'];
            $this->itl_prod = $_POST['itl_prod_filter'];
         }
         elseif ($event->data === 'issuelist_filterlink') {
            $this->itl_start = $_GET['itl_start'];
            $this->itl_step = $_GET['itl_step'];
            $this->itl_next = $_GET['itl_next'];
            $this->itl_pjct = $_GET['itl_project'];
            $this->itl_stat = $_GET['itl_stat_filter'];
            $this->itl_sev = $_GET['itl_sev_filter'];
            $this->itl_prod = $_GET['itl_prod_filter'];
         }
         elseif ($event->data === 'showmodlog') {
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
        $tmp = explode(chr(10),$txt);
        foreach($tmp as $line) {
          if((stripos($line,'ul')===false) && (stripos($line,'ol')===false) && (stripos($line,'li')===false)) {
              $res .= $line."<br />";
          }
          else $res .= $line;
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

         if (($data->data == 'showcase') || ($data->data == 'showcaselink') || ($data->data == 'store_resolution')) {
             
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
                      echo '<div class="it__negative_feedback">'.sprintf($this->getLang('msg_pfilemissing'), $project) . '</div><br />';
                   }	                              
                 
                 $Generated_Header = '';
                 $Generated_Message = '';
                 //If comment to be deleted
                 if ($_REQUEST['del_cmnt']==='TRUE') {
                     // check if captcha is to be used by issue tracker in general
                     if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                     else { $captcha_ok = ($this->_captcha_ok());}
                     if ($captcha_ok)
                     {    if (checkSecurityToken())
                          {  // get comment file contents
                             $comments_file = metaFN($project."_".$_REQUEST['comment_issue_ID'], '.cmnts');
                             if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                             else  {  $txt='Comments file does not exist.';  }
                             // delete fieldset from $comments array
                             $comment_id = htmlspecialchars(stripslashes($_REQUEST['comment_id']));
                             //$comments[$comment_id]
                             unset($comments[$comment_id]);
                             // store comments to file
                             $xvalue = io_saveFile($comments_file,serialize($comments));
                             if($this->getConf('mail_modify_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id],'delete');
                             $Generated_Header = '<div class="it__positive_feedback">'.sprintf($this->getLang('msg_commentdeltrue'),$comment_id).'</div><br />';
                          }
                      }
                 }
                 //If comment to be added  or modified
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
                                   $comments_file = metaFN($project."_".$_REQUEST['comment_issue_ID'], '.cmnts');
            
                                   if (@file_exists($comments_file))  {  $comments  = unserialize(@file_get_contents($comments_file));  }
                                   else  {  $comments = array();  }
                                   $checkFlag=false;
                                   
                                   //Add new comment to the comment file
                                   $comment_id=count($comments);
                                   // check if comment content already exist
                                   foreach ($comments as $value)
                                       {  if ($value['id'] >= $comment_id) { $comment_id=$value['id'] + 1; } 
                                          if ($_REQUEST['comment'] === $value['comment']) 
                                          {   $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_commentfalse').'</div><br />';
                                              $checkFlag=true; 
                                          }
                                       }
                                   //If comment to be modified
                                   if (($checkFlag === false) && (isset($_REQUEST['comment_id'])))
                                   { $comment_id = htmlspecialchars(stripslashes($_REQUEST['comment_id']));
                                     if ($_REQUEST['comment_mod'] === $comments[$comment_id]['comment']) 
                                        {   $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_commentmodfalse').$comment_id.'</div><br />';
                                            $checkFlag=true; 
                                        }
                                     else
                                     {  $cur_date = date ($this->getConf('d_format'));
                                        $comments[$comment_id]['mod_timestamp'] = $cur_date;
                                        $comments[$comment_id]['comment'] = htmlspecialchars(stripslashes($_REQUEST['comment_mod']));
                                        $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commentmodtrue').$comment_id.'.</div><br />';
                                        //Create comments file
                                        $xvalue = io_saveFile($comments_file,serialize($comments));
                                        if($this->getConf('mail_modify_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'modify');
                                        $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commenttrue').$comment_id.'.</div><br />';
                                      }
                                   }
                                   //If comment to be added
                                   elseif ($checkFlag === false)
                                   {   $comment_id=$value['id']+1;
                                       $comments[$comment_id]['id'] = $comment_id;
                                       
                                       $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                                       $cur_date = date ($this->getConf('d_format'));
                                       $comments[$comment_id]['timestamp'] = $cur_date;
                                       $comments[$comment_id]['comment'] = htmlspecialchars(stripslashes($_REQUEST['comment']));
                                       //Create comments file
                                       $xvalue = io_saveFile($comments_file,serialize($comments)); 
                                       if($this->getConf('mail_add_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'new');
                                       $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commenttrue').$comment_id.'.</div><br />';
                                    }
                                    // update issues modification date
                                    if ($checkFlag === false)
                                    {   // inform user (or assignee) about update
                                        // update modified date
                                        $issues[$_REQUEST['comment_issue_ID']]['modified'] = date($this->getConf('d_format')); 
                                        $xvalue = io_saveFile($pfile,serialize($issues));
                                        // if($this->getConf('mail_modify_comment') ===1) $this->_emailForMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id], 'modify');
                                        $anker_id = 'resolved_'. uniqid((double)microtime()*1000000,1);                                   
                                    }
                                 }
                            }
                       }
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
                              {   $issues[$issue_id]['description'] = htmlspecialchars(stripslashes($_REQUEST['description_mod']));
                                  //save issue-file
                                  $xvalue = io_saveFile($pfile,serialize($issues));
                                  if($this->getConf('mail_modify__description') ===1) $this->_emailForDscr($_REQUEST['project'], $issues[$issue_id]);
                                  $Generated_Message = '<div class="it__positive_feedback">'.$this->getLang('msg_descrmodtrue').$issue_id.'</div>';
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

                              if ($cFlag === true)
                              {   $issues[$issue_id]['attachment1'] = htmlspecialchars(stripslashes($_REQUEST['attachment1']));
                                  $issues[$issue_id]['attachment2'] = htmlspecialchars(stripslashes($_REQUEST['attachment2']));
                                  $issues[$issue_id]['attachment3'] = htmlspecialchars(stripslashes($_REQUEST['attachment3']));
                                  //save issue-file
                                  $xvalue = io_saveFile($pfile,serialize($issues));
//                                  if($this->getConf('mail_modify__description') ===1) $this->_emailForDscr($_REQUEST['project'], $issues[$issue_id]);
                                  $Generated_Message = '<div class="it__positive_feedback">'.$this->getLang('msg_slinkmodtrue').$issue_id.'</div>';
                              }
                              else { msg("Issue with ID: $issue_id not found.",-1); }
                        }
                    }
                 }                 
                 elseif (isset($_REQUEST['add_resolution'])) 
                 {  $renderer->info['cache'] = false;     
                    // get issues file contents
//                    $pfile = metaFN($data['project'], '.issues'); 
          
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
                              {   $issues[$issue_id]['resolution'] = htmlspecialchars(stripslashes($_REQUEST['x_resolution']));
                                  
                                  $issues[$issue_id]['status'] = $this->getLang('issue_resolved_status');
                                  $xuser = $issues[$issue_id]['user_mail'];
                                  $xdescription = $issues[$issue_id]['description'];

                                  //save issue-file
                                  $xvalue = io_saveFile($pfile,serialize($issues));
                                  $anker_id = 'resolved_'. uniqid((double)microtime()*1000000,1);                                   
                                  if($this->getConf('mail_modify_resolution') ===1) $this->_emailForRes($_REQUEST['project'], $issues[$_REQUEST['comment_issue_ID']]);
                                  $Generated_Message = '<div class="it__positive_feedback"><a href="#'.$anker_id.'"></a>'.$this->getLang('msg_resolution_true').$issue_id.'</div>';
                                  msg($this->getLang('msg_resolution_true').$issue_id.'.',1);
                                  $usr = $_POST['usr'];                                                                    
                                  $this->_log_mods($project, $issues[$issue_id], $usr, 'resolution', $issues[$issue_id]['resolution']);
                              }
                              else { msg("Issue with ID: $issue_id not found.",-1); }
                                
                          }
                      }        
                 }
                 // Render 
                                                        // Array  , project name
                 $Generated_Table = $this->_details_render($issues, $project);                 
                 //$data->doc .= $Generated_Header.$Generated_Table.$Generated_feedback;

        }
        // scrolling next/previous issues 
        elseif (($data->data == 'issuelist_next') || ($data->data == 'issuelist_previous') || ($data->data == 'issuelist_filter') || ($data->data == 'issuelist_filterlink'))  {
                 $data->preventDefault();
                 $renderer->info['cache'] = false;         
                 $itl_start = $this->itl_start;
                 $step = $this->itl_step;
                 if ($step == '') {$step=10;}
                 $itl_next = $this->itl_next;
                 $a = $this->itl_pjct;
//                 echo 'Project: '.$a.'<br />';
                 
                                                   
                 $pfile = metaFN($a, '.issues');        
                if (@file_exists($pfile))
                	{$issues  = unserialize(@file_get_contents($pfile));}
/*                else
                	{   // prompt error message that issue with ID does not exist
                      echo '<div class="it__negative_feedback">'.printf($this->getLang('msg_pfilemissing'), $project).'</div><br />';
                      return;
                  } */           	          

                 if ($data->data == 'issuelist_next') {
                    $start = $itl_next;
                    if ($start<0) { $start='0'; }
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
                    if ($start<0) { $start='0'; }                    
                    $next_start = $start + $step;
                    if ($next_start>count($issues)) {
                      $next_start=count($issues);
                      $start = $next_start - $step;
                      if ($start<0) { $start='0'; }
                      }
//                    echo 'start = '.$start.';  step = '.$step.';  next_start = '.$next_start.'<br />';
                 }
                 elseif (($data->data == 'issuelist_filter')||($data->data == 'issuelist_filterlink')) {
                    $start = $itl_start;
                    $next_start = $start + $step;                    
                    if ($next_start>count($issues)) { $next_start=count($issues); }                 
                 }

                $stat_filter = $this->itl_stat;
                if ($stat_filter == '') {$stat_filter='ALL';}
                $sev_filter = $this->itl_sev;
                if ($sev_filter == '') {$sev_filter='ALL';}
                $productfilter = $this->itl_prod;
                if ($productfilter == '') {$productfilter='ALL';}
                $Generated_Header  = '';                       
                $Generated_Table   = $this->_table_render($a,$step,$start,$next_start,$stat_filter,$sev_filter,$productfilter); 
                $Generated_Scripts = $this->_scripts_render();
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
            $modfile = metaFN($project.'_'.$issue_id, '.mod-log');
            if (@file_exists($modfile))
                {$mods  = unserialize(@file_get_contents($modfile));}
            else 
                {msg('No Modification log file found for this issue',-1);
                 return;}

            $Generated_Table  .= '<h1>'.$this->getLang('h_modlog').$issue_id.'</h1>';
            $Generated_Table  .= '<div class="dokuwiki"><table class="inline tbl_showmodlog">'.NL;
            $Generated_Table  .= '<tr><th>Date</th><th>User</th><th>Field</th><th>new Value</th></tr>'.NL;

            foreach($mods as $mod) {          
                $Generated_Table  .= '<tr>'.NL;
                $Generated_Table  .= '  <td>'.date($this->getConf('d_format'),strtotime($this->_get_one_value($mod,'timestamp'))).'</td>'.NL;
                $Generated_Table  .= '  <td>'.$this->_get_one_value($mod,'user').'</td>'.NL;
                $Generated_Table  .= '  <td>'.$this->_get_one_value($mod,'field').'</td>'.NL;
                
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
            $itl_item_title = '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$this->getLang('back').'">'.$this->getLang('back').'</a>';
            $Generated_Table  .= $itl_item_title.NL;        
        }
        elseif ($data->data == 'it_search'){
            $data->preventDefault();
            include('itsearch.php');
        }
        else return;
        
        // Render            
        echo $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report.$Generated_Message;
    }
/******************************************************************************/
/* Create table scripts
*/
    function _scripts_render()
    {
        // load status values from config into select control
        $s_counter = 0;
        $status = explode(',', $this->getConf('status')) ;
        foreach ($status as $x_status)
        {
            $s_counter = $s_counter + 1;
            $STR_STATUS = $STR_STATUS . "case '".$x_status."':  val = ".$s_counter."; break;";
            $pattern = $pattern . "|" .  $x_status;
            $x_status_select = $x_status_select . "['".$x_status."','".$x_status."'],";
        }
        
        // Build string to load products select
        $products = explode(',', $this->getConf('products')) ;
        foreach ($products as $x_products)
        {
            $x_products_select = $x_products_select . "['".$x_products."','".$x_products."'],";
        } 
        
        // Build string to load severity select
        $severity = explode(',', $this->getConf('severity')) ;
        foreach ($severity as $x_severity)
        {
            $x_severity_select = $x_severity_select . "['".$x_severity."','".$x_severity."'],";
        } 
        
        // see issue 37: AUTH:AD switch to provide text input instead 
        // select with retriveing all_users from AD
        if($this->getConf('auth_ad_overflow') == false) {
            global $auth;        
            $filter['grps'] = $this->getConf('assign');
            $target         = $auth->retrieveUsers(0,0,$filter); 
            $target2        = $this->array_implode($target);
            foreach ($target2 as $x_umail)
            {
                    if (strrpos($x_umail, "@") > 0)
                    {
                        $x_umail_select = $x_umail_select . "['".$x_umail."','".$x_umail."'],";
                    }
            }      
            $x_umail_select .= "['',''],";
            $authAD_selector = "TableKit.Editable.selectInput('assigned',{}, [".$x_umail_select."]);";
        }

        $BASE = DOKU_BASE."lib/plugins/issuetracker/";
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
    function _table_render($project,$step,$start,$next_start,$stat_filter,$sev_filter,$productfilter)
    {
        global $ID;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $style =' style="text-align:left; white-space:pre-wrap;">';
        $user_grp = pageinfo();
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
        $stat_filter=strtoupper($stat_filter);
        $sev_filter=strtoupper($sev_filter);
        $productfilter==strtoupper($productfilter);
        
        // get issues file contents
        $pfile = metaFN($project, '.issues'); 

        if (@file_exists($pfile))
        	{$issues  = unserialize(@file_get_contents($pfile));}
        else
        	{ msg("No [$pfile] found.",-1); return; }            	          

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
        
        // members of defined groups allowed$user_grps changing issue contents 
        if ($cFlag === true)       
        {   
            $head = "<div class='itl__table'><table id='".$project."' class='sortable editable resizable inline' width='100%'>".NL.
                    "<thead><tr><th class=\"sortfirstdesc\" id='id'>".$this->getLang('th_id')."</th>".NL.
                    "<th id='created'>".$this->getLang('th_created')."</th>".NL.
                    "<th id='product'>".$this->getLang('th_product')."</th>".NL.
                    "<th id='version'>".$this->getLang('th_version')."</th>".NL.
                    "<th id='severity'>".$this->getLang('th_severity')."</th>".NL.
                    "<th id='status'>".$this->getLang('th_status')."</th>".NL.
                    "<th id='user_name'>".$this->getLang('th_username')."</th>".NL.
                    "<th id='title'>".$this->getLang('th_title')."</th>".NL.
                    "<th id='assigned'>".$this->getLang('th_assigned')."</th>".NL. 
                    "<th id='resolution'>".$this->getLang('th_resolution')."</th>".NL.
                    "<th id='modified'>".$this->getLang('th_modified')."</th></tr></thead>".NL;        
            $body = '<tbody>'.NL;
        
            for ($i=$next_start-1;$i>=0;$i=$i-1)
            {   // check start and end of rows to be displayed
                    $issue = $issues[$i];                    
                    $a_status = strtoupper($this->_get_one_value($issue,'status'));
                    $a_severity = strtoupper($this->_get_one_value($issue,'severity'));
                    $a_product = strtoupper($this->_get_one_value($issue,'product'));
                    
                if ((($stat_filter=='ALL') || (stristr($stat_filter,$a_status)!= false)) && (($sev_filter=='ALL') || (stristr($sev_filter,$a_severity)!= false)) && (($productfilter=='ALL') || (stristr($productfilter,$a_product)!= false)))
                {   
                    if ($y>=$step) break;
                    if ((stripos($this->getConf('status_special'),$a_status) !== false) && (stripos($stat_filter,$this->getConf('status_special')) === false)) continue;                   
                    $y=$y+1;
                    // check if status image or text to be displayed
                    if ($noStatIMG === false) {                    
                        $status_img = $imgBASE . implode('', explode(' ',strtolower($a_status))).'.gif';
//                        if(!file_exists(str_replace("//", "/", DOKU_INC.$status_img)))  { $status_img = $imgBASE . 'status.gif' ;}
                        $status_img =' align="center"> <img border="0" alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16">';
                    }                    
                    else { $status_img = $style.$a_status; }
                    // check if severity image or text to be displayed                                            
                    if ($noSevIMG === false) {                    
                        $severity_img = $imgBASE . implode('', explode(' ',strtolower($a_severity))).'.gif';

//                        if(!file_exists(str_replace("//", "/", DOKU_INC.$severity_img)))  { $severity_img = $imgBASE . 'status.gif' ;}
                        $severity_img =' align="center"> <img border="0" alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16">';
                    }
                    else { $severity_img = $style.$a_severity; }
                    
                    // build parameter for $_GET method
                        $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($project));
                        $itl_item_title = '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$this->_get_one_value($issue,'title').'">'.$this->_get_one_value($issue,'title').'</a>';
                    
                                            
                    $body .= '<tr id = "'.$project.' '.$this->_get_one_value($issue,'id').'" onMouseover="this.bgColor=\'#DDDDDD\'" onMouseout="this.bgColor=\'#FFFFFF\'">'.                       
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'id').'</td>'.
                             '<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'created'))).'</td>'.
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'product').'</td>'.
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'version').'</td>'.
                             '<td'.$severity_img.'</td>'.
                             '<td'.$status_img.'</td>'.
                             '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'user_mail').'">'.$this->_get_one_value($issue,'user_name').'</a></td>'. 
                             '<td class="canbreak itl__td_standard">'.$itl_item_title.'</td>'.
                             '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'assigned').'">'.$this->_get_one_value($issue,'assigned').'</a></td>'. 
                             '<td class="canbreak itl__td_standard">'.$this->xs_format($this->_get_one_value($issue,'resolution')).'</td>'.
                             '<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'modified'))).'</td>'.
                             '</tr>';        
                }
            } 
            $body .= '</tbody></table></div>';          
        } 

        else       
        {   
            //Build table header according settings
            $configs = explode(',', $this->getConf('shwtbl_usr')) ;
            $reduced_header ='';
            $reduced_header = "<div class='itl__table'><table id='".$dynatable_id."' class='sortable resizable inline' width='100%'>".NL.
                    "<thead><tr>".NL."<th class='sortfirstdesc' id='id'>".$this->getLang('th_id')."</th>".NL;

            foreach ($configs as $config)
            {
                $reduced_header .= "<th id='".$config."'>".$this->getLang('th_'.$config)."</th>".NL;
            }

            $reduced_header .= "</tr></thead>".NL;

            //Build rows according settings
            $reduced_issues='';
            for ($i=$next_start-1;$i>=0;$i=$i-1)
            {   // check start and end of rows to be displayed
                    $issue = $issues[$i];                    
                    $a_status = strtoupper($this->_get_one_value($issue,'status'));
                    $a_severity = strtoupper($this->_get_one_value($issue,'severity'));

                if ((($stat_filter=='ALL') || (stristr($stat_filter,$a_status)!= false)) && (($sev_filter=='ALL') || (stristr($sev_filter,$a_severity)!= false)) && (($productfilter=='ALL') || (stristr($productfilter,$a_product)!= false)))
                {   
                    if ($y>=$step) break;
                    if ((stripos($this->getConf('status_special'),$a_status) !== false) && (stripos($stat_filter,$this->getConf('status_special')) === false)) continue;
                    $y=$y+1;
                    $reduced_issues = $reduced_issues.'<tr id = "'.$project.' '.$this->_get_one_value($issue,'id').'" onMouseover="this.bgColor=\'#DDDDDD\'" onMouseout="this.bgColor=\'#FFFFFF\'">'.
                                                      '<td'.$style.$this->_get_one_value($issue,'id').'</td>';
                    foreach ($configs as $config)
                    {
                        $isval = $this->_get_one_value($issue,strtolower($config));
                        // check if status image or text to be displayed
                        if ($config == 'status')
                        {
                            if ($noStatIMG === false) {                    
                                $status_img = $imgBASE . implode('', explode(' ',strtolower($isval))).'.gif';
                                $reduced_issues .='<td align="center"> <img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"></td>';
                            }
                            else { $reduced_issues .= '<td'.$style.$isval; }
                        }                                            
                        // check if severity image or text to be displayed
                        elseif ($config == 'severity')
                        {
                            if ($noSevIMG === false) {                    
                                $severity_img = $imgBASE . implode('', explode(' ',strtolower($isval))).'.gif';
                                $reduced_issues .='<td align="center"> <img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"></td>';
                            }
                            else { $reduced_issues .= '<td'.$style.$isval.'</td>'; }
                        }
                        elseif ($config == 'title')
                        {   // build parameter for $_GET method
                            $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($project));
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
            
            $head = NL.$reduced_header.NL;
            $body = '<tbody>'.$reduced_issues.'</tbody></table></div>';
        }
        
        if ($productfilter==="") {$productfilter='ALL';}
        //$a,,$productfilter
        $li_count = $this->_count_render($issues,$start,$step,$next_start,$stat_filter,$sev_filter,$productfilter,$project);
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
                      '<label class="it__cir_projectlabel">'.sprintf($this->getLang('lbl_issueqty'),$project).count($issues).'</label>'.NL.
                  '</td>'.NL.
                  '<td class="itl__showdtls" rowspan="2" width="35%">'.$li_count.'</td>'.NL.
               '</tr>'.NL.

               '<tr class="itd__tables_tr">'.NL.
               '   <td align ="left" valign="top" width="15%">'.NL.
               '     <p class="it__cir_projectlabel">'.$this->getLang('lbl_scroll').' <br />'.NL.
                                                      $this->getLang('lbl_filtersev').' <br />'.NL.
                                                      $this->getLang('lbl_filterstat').' </p>'.NL.
               '   </td>'.NL.
               '   <td align ="left" valign="top" width="20%">'.NL.
               '    <form name="myForm" action="" method="post">'.NL.
               '       <input type="hidden" name="itl_start" id="itl_start" value="'.$start.'"/>'.NL.
               '       <input type="hidden" name="itl_step" id="itl_step" value="'.$step.'"/>'.NL.
               '       <input type="hidden" name="itl_next" id="itl_next" value="'.$next_start.'"/>'.NL.
               '       <input type="hidden" name="itl_project" id="itl_project" value="'.$project.'"/>'.NL.
               '       <input type="hidden" class="itl__prod_filter" name="itl__prod_filter" id="itl__prod_filter" value="'.$productfilter.'"/>'.NL.
               '       <input class="itl__buttons" type="button" name="showprevious" value="'.$this->getLang('btn_previuos').'" title="'.$this->getLang('btn_previuos_title').'" onClick="changeAction(1)"/>'.NL.
               '       <input class="itl__step_input"      name="itl_step" id="itl_step" type="text" value="'.$step.'"/>'.NL.
               '       <input class="itl__buttons" type="button" name="shownext" value="'.$this->getLang('btn_next').'" title="'.$this->getLang('btn_next_title').'" onClick="changeAction(2)"/><br />'.NL.
               '       <input class="itl__sev_filter"      name="itl_sev_filter" id="itl_sev_filter" type="text" value="'.$sev_filter.'"/><br />'.NL.                         
               '       <input class="itl__stat_filter"     name="itl_stat_filter" id="itl_stat_filter" type="text" value="'.$stat_filter.'"/>'.NL.
               '       <input class="itl__buttons" type="button" name="go" value="'.$this->getLang('btn_go').'" title="'.$this->getLang('btn_go').'" onClick="changeAction(3)"/><br />'.NL.
               '    </form>'.NL.                      
               '   </td>'.NL.
               '   <td width="2%">&nbsp;</td>'.NL.
               '   <td class="itl__showdtls" align ="left" width="40%">'.NL.
               '    <form  method="post" action="doku.php?id=' . $ID . '&do=showcase">'.NL.
               '       <label class="it__searchlabel">'.$this->getLang('lbl_showid').'</label>'.NL.
               '       <input class="itl__sev_filter" name="showid" id="showid" type="text" value="0"/>'.NL.
               '       <input type="hidden" name="project" id="project" value="'.$project.'"/>'.NL.
               '       <input type="hidden" name="itl_sev_filter" id="itl_sev_filter" value="'.$sev_filter.'"/>'.NL.
               '       <input type="hidden" name="itl_stat_filter" id="itl_stat_filter" value="'.$stat_filter.'"/>'.NL.
               '       <input class="itl__showid_button" id="showcase" type="submit" name="showcase" value="'.$this->getLang('btn_showid').'" title="'.$this->getLang('btn_showid_title').'"/>'.NL.
               '    </form><br />'.NL.
               '    <form  method="post" action="doku.php?id=' . $ID . '&do=it_search">'.NL.
               '       <label class="it__searchlabel">'.$this->getLang('lbl_search').'</label>'.NL.
               '       <input class="itl__sev_filter" name="it_str_search" id="it_str_search" type="text" value="'.$_REQUEST['it_str_search'].'"/>'.NL.
               '       <input type="hidden" name="project" id="project" value="'.$project.'"/>'.NL.
               '       <input class="itl__search_button" id="searchcase" type="submit" name="searchcase" value="'.$this->getLang('btn_search').'" title="'.$this->getLang('btn_search_title').'"/>'.NL.
               '    </form>'.NL.
               '   </td>'.NL.
               '</tr>'.NL.'</tbody>'.NL.'</table>'.NL.'</div>'.NL;

         $usr  = '<span style="display:none;" id="currentuser">'.$user_grp['userinfo']['name'].'</span>' ; // to log issue mods
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
        $issue_id = $this->parameter;
        if (!$issue_id) { $issue_id = '0'; }
        
//        echo 'Project = '.$project.'<br />Issue ID = '. $issue_id.'<br />';
        
        if ($issue_id === false) return;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
//        $user_grp = pageinfo();
        
        // get issues file contents
        $issue = $this->get_issues_file_contents($project, $issue_id);
        if(!$issue) return false;	          
        
        // get detail information from issue comment file
        $cfile = metaFN($project."_".$issue_id, '.cmnts');
        if (@file_exists($cfile)) {$comments  = unserialize(@file_get_contents($cfile));}
        else {$comments = array();}

        $a_severity = $issue[$issue_id]['severity'];                  
        $severity_img = $imgBASE . implode('', explode(' ',strtolower($a_severity))).'.gif';
        $severity_img =' <img border="0" alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"> ';
        $a_status = $issue[$issue_id]['status'];
        $status_img = $imgBASE . implode('', explode(' ',strtolower($a_status))).'.gif';
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
                ($this->getConf('shw_mail_addr')===1))
            {   $__assigened  = $issue[$issue_id]['assigned'];
                $__assigenedaddr = $issue[$issue_id]['assigned'];
                $__reportedby = $issue[$issue_id]['user_mail'];
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
                $mail_allowed = true;
            }
            else 
            {   foreach($target as $_assignee)
                  { if($_assignee['mail'] === $issue[$issue_id]['assigned'])
                    {   $__assigened = $_assignee['name'];
                        $__assigenedaddr = $_assignee['mail'];
                        $mail_allowed = true;
                        break;
                    }
                  }
                $__reportedby = $issue[$issue_id]['user_name'];
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
            }
        }
        else {  // auth_ad_overflow = true
                $__reportedby = $issue[$issue_id]['user_name'];
                $__reportedbyaddr = $issue[$issue_id]['user_mail'];
                $mail_allowed = true;
        }
                   
// scripts for xsEditor -------------------------------------------------------
$issue_edit_head .= '<span>
         <script>
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
          </script></span>'.NL;
          
$issue_edit_head .= '<span>
         <script>
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
$issue_edit_head .= '<table class="itd__title">'.
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
                      <td class="itd__col2">'.$this->getLang('lbl_issueid').'</td>
                      <td class="itd__col3">'.$issue[$issue_id]['id'].'</td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('lbl_project').'</td>
                      <td class="itd__col6">'.$project.'</td>
                    </tr>';
                   
$issue_edit_head .= '<tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_severity').':</td>
                      <td class="itd__col3">'.$severity_img.$issue[$issue_id]['severity'].'</td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('th_product').':</td>
                      <td class="itd__col6">'.$issue[$issue_id]['product'].'</td>
                    </tr>';
                   
$issue_edit_head .= '<tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_status').':</td>
                      <td class="itd__col3">'.$status_img.$issue[$issue_id]['status'].'</td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('th_version').':</td>
                      <td class="itd__col6">'.$issue[$issue_id]['version'].'</td>
                    </tr>';

$issue_edit_head .= '<tr class="itd_tr_standard">                      
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('lbl_reporter').'</td>'.NL;
if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
                        {$issue_edit_head .= '<td class="itd__col3"><a href="mailto:'.$__reportedbyaddr.'">'.$__reportedby.'</a></td>'.NL;}
else{$issue_edit_head .= '<td class="itd__col3">'.$__reportedby.'</td>'.NL;}

$issue_edit_head .= ' <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('th_created').':</td>
                      <td class="itd__col6">'.date($this->getConf('d_format'),strtotime($issue[$issue_id]['created'])).'</td>
                    </tr>
                   
                    <tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_assigned').':</td>'.NL;
if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
                        {$issue_edit_head .= '<td class="itd__col3"><a href="mailto:'.$__assigenedaddr.'">'.$__assigened.'</a></td>'.NL;}
else{$issue_edit_head .= '<td class="itd__col3">'.$__assigened.'</td>'.NL;}

$issue_edit_head .= '<td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('th_modified').':</td>
                      <td class="itd__col6">'.date($this->getConf('d_format'),strtotime($issue[$issue_id]['modified'])).'</td>
                    </tr>'.NL;
/*------------------------------------------------------------------------------
  #60: to view mod-log
------------------------------------------------------------------------------*/
            $modfile = metaFN($project.'_'.$issue[$issue_id]['id'], '.mod-log');
            if (@file_exists($modfile)) {  
              $pstring = sprintf("showid=%s&amp;project=%s", urlencode($issue[$issue_id]['id']), urlencode($project));
              $modlog_link = '<a href="doku.php?id='.$ID.'&do=showmodlog&'.$pstring.'" title="'.$this->getLang('th_showmodlog').'">'.$this->getLang('th_showmodlog').'</a>';
              $issue_edit_head .= '<tr><td class="itd__modlog_link" colspan="6">['.$modlog_link.']</td></tr>'.NL;                    
              $issue_edit_head .= '</tbody></table>'.NL;
            }
/*----------------------------------------------------------------------------*/
                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
                  $cell_ID = 'img_tab_open_reporterdtls'.$blink_id;                              
$issue_client_details = '<table class="itd__tables" id="tbl_'.$anker_id.'"><tbody>
                        <tr>
                           <td class="itd_tables_tdh" colSpan="3">'.$this->getLang('lbl_reporterdtls').'</td>
                        </tr>
                        <tbody style="display : none;" id="'.$blink_id.'"><tr class="itd__tables_tr">
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
                            			      {  $issue_client_details .= '<p>'.$helper->getHTML().'</p>'; }
                                    }
$issue_addcontacts .='            <input  type="submit" class="button" style="font-size:8pt;" id="btn_add_contact" name="btn_add_contact" value="'.$this->getLang('btn_add').'" title="'.$this->getLang('btn_add').'");/>
                                </form>
                              </span>
                            </td>'.NL;
$issue_addimg = '<img class="cmt_list_plus_img" alt="add" src="'.DOKU_BASE.'lib/plugins/issuetracker/images/blank.gif" id="'.$anker2_id.'" onClick="span_open(\''.$blink2_id.'\')" />
                 <p style="margin-top:-6px;"><span style="font-size:7pt;">'.sprintf($this->getLang('itd_follower'),$follower).'</span></p>';
}
//--------------------------------------------------------------------------------------------------------------
                               
                        if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false)) 
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
                          </tr>
                          <tr class="itd__tables_tr">
                            <td class="it__left_indent"></td>
                            <td class="itd_tables_tdc2">'.$this->getLang('lbl_reporteradcontact').NL.
                            $issue_addimg;
$issue_client_details .=    '</td>'.$issue_addcontacts.'
                          </tr>'; 
                        }

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
        $cur_date = date ($this->getConf('d_format'));
        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');

$issue_initial_description = '<table class="itd__tables"><tbody>
                                <tr>
                                  <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_initdescr').'</td>
                                </tr>
                                <tr class="itd__tables_tr">
                                  <td width="1%"></td>
                                  <td>'.$this->xs_format($x_comment).'</td>
                                </tr>';
                             
/* mod for edit description by ticket owner and admin/assignee ---------------*/
// check if current user is author of the comment and offer an edit button
            if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false)) 
            {     // add hidden edit toolbar and textarea
                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
        
            $issue_initial_description .= '   <tr>
                                                 <td colSpan="2" style="display : none;" id="'.$blink_id.'">';
                    
            $issue_initial_description .= $this->it_edit_toolbar('description_mod');
                    
            $issue_initial_description .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                                          
            $issue_initial_description .= formSecurityToken(false). 
                                         '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                                         '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                                         '<input type="hidden" name="author"value="'.$u_mail_check.'" />'.NL.        
                                         '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.
                                         '<input type="hidden" name="mod_description" value="1"/>'.NL.
                                         '<textarea id="description_mod" name="description_mod" type="text" cols="106" rows="7" value="">'.strip_tags($x_comment).'</textarea>'.NL;        
                                         
                                if ($this->getConf('use_captcha')==1) 
                                {   $helper = null;
                        		        if(@is_dir(DOKU_PLUGIN.'captcha'))
                        			         $helper = plugin_load('helper','captcha');
                        			         
                        		        if(!is_null($helper) && $helper->isEnabled())
                        			      {  $issue_initial_description .= '<p>'.$helper->getHTML().'</p>'; }
                                }
                                $cell_ID = 'img_tab_open_comment'.$blink_id;

$issue_initial_description .=  '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
                               '<input  type="submit" class="button" id="btnmod_description" name="btnmod_description" value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.
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

$issue_attachments = '<table class="itd__tables"><tbody>
                      <tr>
                        <td colspan="2" class="itd_tables_tdh">'.$this->getLang('lbl_symptlinks').'</td>
                      </tr>
                      <tr  class="itd__tables_tr">
                        <td colspan="2" style="padding-left:0.45em;">1. <a href="'.$issue[$issue_id]['attachment1'].'"><img border="0" alt="symptoms 1" style="margin-right:0.5em" vspace="1" align="middle" src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment1'].'" href="'.$issue[$issue_id]['attachment1'].'">'.$issue[$issue_id]['attachment1'].'</a></td>
                      </tr>'.NL.
                     '<tr  class="itd__tables_tr">
                        <td colspan="2" style="padding-left:0.45em;">2. <a href="'.$issue[$issue_id]['attachment2'].'"><img border="0" alt="symptoms 2" style="margin-right:0.5em" vspace=1em align=absMiddle src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment2'].'" href="'.$issue[$issue_id]['attachment2'].'">'.$issue[$issue_id]['attachment2'].'</a></td>
                      </tr>'.NL.
                     '<tr  class="itd__tables_tr">
                        <td colspan="2" style="padding-left:0.45em;">3. <a href="'.$issue[$issue_id]['attachment3'].'"><img border="0" alt="symptoms 3" style="margin-right:0.5em" vspace="1" align="middle" src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment3'].'" href="'.$issue[$issue_id]['attachment3'].'">'.$issue[$issue_id]['attachment3'].'</a></td>
                      </tr>'.NL;
/* mod for edit symptom links by ticket owner and admin/assignee ---------------*/
// check if current user is author of the comment and offer an edit button
            if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false))
            {     // add hidden edit toolbar and textarea
                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
                  $cell_ID = 'img_tab_open_reporterdtls'.$blink_id;                              
$issue_attachments .= '<tbody style="display : none;" id="'.$blink_id.'">
                        <tr><td colspan=2>'.NL.
                        '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
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
                                }                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Symptom link 1')!==false){
                      $issue_attachments .= ' <input type="hidden" class="it__cir_linput" name="attachment1" value="'.$issue[$issue_id]['attachment1'].'"/>'.NL;
                  } 
                  else {
                      $issue_attachments .= '<span style="margin-left:4em; float:left;">1.</span>
                                   <span><input class="it__cir_linput" name="attachment1" value="'.$issue[$issue_id]['attachment1'].'"/></span><br />'.NL;
                  }             
                  if(strpos($this->getConf('ltdReport'),'Symptom link 2')!==false){
                      $issue_attachments .= ' <input type="hidden" class="it__cir_linput" name="attachment2" value="'.$issue[$issue_id]['attachment2'].'"/>'.NL;
                  } 
                  else {
                      $issue_attachments .= '<span style="margin-left:4em; float:left;">2.</span>
                                   <span><input class="it__cir_linput" name="attachment2" value="'.$issue[$issue_id]['attachment2'].'"/></span><br />'.NL;
                  }             
                  if(strpos($this->getConf('ltdReport'),'Symptom link 3')!==false){
                      $issue_attachments .= ' <input type="hidden" class="it__cir_linput" name="attachment3" value="'.$issue[$issue_id]['attachment3'].'"/>'.NL;
                  } 
                  else {
                      $issue_attachments .= '<span style="margin-left:4em; float:left;">3.</span>
                                   <span><input class="it__cir_linput" name="attachment3" value="'.$issue[$issue_id]['attachment3'].'"/></span><br/>'.NL;
                  } 
$issue_attachments .= '<input  type="hidden" class="showid__option" name="showid" id="showid" size="10" value="'.$this->parameter.'"/>'.
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
/* END mod for edit description by ticket owner ----------------------------------*/  
          
$issue_comments_log ='<table class="itd__tables"><tbody>
                      <tr>
                        <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_cmts_wlog').'</td>
                      </tr>';
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
                        else {   $x_mail = '<i> ('.$this->getLang('dtls_usr_hidden').') </i>';  }

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
        $cur_date = date ($this->getConf('d_format'));
        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        elseif($this->getConf('shw_mail_addr')===0) {$u_mail_check =$user_mail['userinfo']['name'];}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');
        
/*------------------------------------------------------------------------------
 *   Modify comment                                                           */
                // check if current user is author of the comment and offer an edit button
                if((($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) || (strpos($target2,$user_mail['userinfo']['mail']) != false)) or 
                (($this->getConf('auth_ad_overflow') == true)))
                {     // add hidden edit toolbar and textarea
                      $alink_id++;
                      $blink_id = 'statanker_'.$alink_id;
                      $anker_id = 'anker_'.$alink_id;
        
                    $issue_comments_log .= '   <tr>
                                                 <td colSpan="2" style="display : none;" id="'.$blink_id.'">';
                    
                    $issue_comments_log .= $this->it_edit_toolbar('comment_mod');
                    
                    $issue_comments_log .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                                          
                    $issue_comments_log .= formSecurityToken(false). 
                                         '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                                         '<input type="hidden" name="comment_file" value="'.$cfile.'" />'.NL.
                                         '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                                         '<input type="hidden" name="author"value="'.$user_mail['userinfo']['mail'].'" />'.NL.        
                                         '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.
                                         '<input type="hidden" name="comment_id" value="'.$this->_get_one_value($a_comment,'id').'" />'.NL.        
                                         '<textarea id="comment_mod" name="comment_mod" type="text" cols="106" rows="7" value="">'.strip_tags($x_comment).'</textarea>'.NL;        
                    if ($this->getConf('use_captcha')==1) 
                    {   $helper = null;
            		        if(@is_dir(DOKU_PLUGIN.'captcha'))
            			         $helper = plugin_load('helper','captcha');
            			         
            		        if(!is_null($helper) && $helper->isEnabled())
            			      {  $issue_comments_log .= '<p>'.$helper->getHTML().'</p>'; }
                    }
                    $cell_ID = 'img_tab_open_comment'.$blink_id;
                    // check if only registered users are allowed to modify comments
                    // ¦ perm — the user's permissions related to the current page ($ID)
$issue_comments_log .= '<input  type="hidden" class="showid__option" name="showid" id="showid" type="text" size="10" value="'.$this->parameter.'"/>'.
                       '<input  type="submit" class="button" id="btnmod_description" name="btnmod_description"  value="'.$this->getLang('btn_mod').'" title="'.$this->getLang('btn_mod_title').'");/>'.
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
        $cur_date = date ($this->getConf('d_format'));
        if(strlen($user_mail['userinfo']['mail']) == 0) {$u_mail_check ='unknown';}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');
        $u_name = $user_mail['userinfo']['name'];
        //2011-12-02: bwenz code proposal (Issue 11)
        $x_resolution = $this->convertlabel($issue[$issue_id]['resolution']);
//        if(!$x_resolution) { $x_resolution = "&nbsp;"; }
                        
        $_cFlag = false;             
        if($user_check == false)
            { $_cFlag = true; } 
            
        elseif ($user_check == true) {
            if ($user_mail['perm'] > 1) 
            { $_cFlag = true; } }

        if($_cFlag === true) {

                      
// mod for editor ---------------------------------------------------------------------
                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;
$issue_add_comment .='<table class="itd__tables">'.
                      '<tr>'.
                        '<td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_cmts_adcmt').'</td>
                      </tr><tr><td colSpan="2" style="display : none;" id="'.$blink_id.'">';
$issue_add_comment .= $this->it_edit_toolbar('comment');                     
// mod for editor ---------------------------------------------------------------------

$issue_add_comment .= '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                      
$issue_add_comment .= formSecurityToken(false). 
                     '<input type="hidden" name="project" value="'.$project.'" />'.NL.
                     '<input type="hidden" name="comment_file" value="'.$cfile.'" />'.NL.
                     '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'" />'.NL.
                     '<input type="hidden" name="author" value="'.$u_mail_check.'" />'.NL.        
                     '<input type="hidden" name="timestamp" value="'.$cur_date.'" />'.NL.        
                     '<textarea id="comment" name="comment" type="text" cols="106" rows="7" value=""></textarea>'.NL;        

                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_add_comment .= '<p>'.$helper->getHTML().'</p>'; }
                      }
                      $cell_ID = 'img_tab_open_comment'.$blink_id;
                      // check if only registered users are allowed to add comments
                      // ¦ perm — the user's permissions related to the current page ($ID)
                      $issue_add_comment .= '<input  type="hidden" class="showid__option" name="showid" id="showid" type="text" size="10" value="'.$this->parameter.'"/>'.NL.
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
                            <td width="1%"></td>
                            <td>'.$this->xs_format($x_resolution).'</td>
                          </tr>
                          <tr><td colSpan="2" style="display : none;" id="'.$blink_id.'">';

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
$issue_edit_resolution .= $this->it_edit_toolbar('x_resolution');                      
// mod for editor ---------------------------------------------------------------------

$issue_edit_resolution .= '<form name="edit_resolution" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'">'.NL;                                            
$issue_edit_resolution .= formSecurityToken(false).
                          '<input type="hidden" name="project"value="'.$project.'"/>'.NL.
                          '<input type="hidden" name="comment_issue_ID" value="'.$issue[$issue_id]['id'].'"/>'.NL.
                          '<input type="hidden" name="usr" value="'.$u_name.'"/>'.NL.
                          '<input type="hidden" name="add_resolution" value="1"/>'.NL;        
    
$issue_edit_resolution .= "<textarea id='x_resolution' name='x_resolution' type='text' cols='106' rows='7' value=''>".strip_tags($x_resolution)."</textarea>";
                              
                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_edit_resolution .= '<p>'.$helper->getHTML().'</p>'; }
                      }
                      
                      $cell_ID = 'img_tab_open_comment'.$blink_id;

$issue_edit_resolution .= '<input  type="hidden" class="showid__option" name="showid" id="showid" type="text" size="10" value="'.$this->parameter.'"/>'.
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
                                        <td width="1%"></td>
                                        <td>'.$this->xs_format($x_resolution).'</td>
                                      </tr></table>'.NL;

            $wmsg = $this->getLang('lbl_lessPermission'); 
            $issue_edit_resolution .= '<div class="it__standard_feedback">'.$wmsg.'</div>';                      
        }
        else {
            $issue_edit_resolution ='<table class="itd__tables">
                                     <tr>
                                        <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('th_resolution').'</td>
                                    </tr>';
            $issue_edit_resolution .= '<tr class="itd__tables_tr">
                                        <td width="1%"></td>
                                        <td>'.$this->xs_format($x_resolution).'</td>
                                      </tr></table>'.NL;

            $wmsg = $this->getLang('lbl_please').'<a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">'.$this->getLang('lbl_signin'); 
            $issue_edit_resolution .= '<div class="it__standard_feedback">'.$wmsg.'</div>';                      
        }

        
        //2011-12-02: bwenz code proposal (Issue 11)                                   
//        $ret = $issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment;
        $usr = '<span style="display:none;" id="currentuser">'.$user_grp['userinfo']['name'].'</span>' ;  //to log issue mods
        $ret = $usr.$issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment . $issue_edit_resolution;

        return $ret;
    }

/******************************************************************************/
/* send an e-mail to user due to issue resolution
*/                            
    function _emailForRes($project,$issue)
    {       if($this->getConf('userinfo_email') ===0) return;
            $subject = sprintf($this->getLang('issue_resolved_subject'),$issue['id'], $project);            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issue_resolved_intro').chr(10).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).chr(10).
                    $this->getLang('issue_resolved_text').$this->xs_format($issue['resolution']).chr(10).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end');

//            $body = utf8_encode($body);
            $from=$this->getConf('email_address') ;

            $user_mail = pageinfo();
            if($user_mail['userinfo']['mail']===$issue['user_mail']) $to=$issue['assigned'];
            elseif($user_mail['userinfo']['mail']===$issue['assigned']) $to=$issue['user_mail'];
            else $to=$issue['user_mail'].', '.$issue['assigned'];
            
            $cc=$issue['add_user_mail'];
            $headers = "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers, $params=null);

    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                            
    function _emailForMod($project,$issue,$comment,$reason)
    {       if($this->getConf('userinfo_email') ===0) return;
            if($reason ==='new') { $subject = sprintf($this->getLang('cmnt_new_subject'),$issue['id'], $project). "\r\n"; }
            elseif($reason =='delete') { $subject = sprintf($this->getLang('cmnt_del_subject'),$issue['id'], $project). "\r\n"; }
            else {$subject = sprintf($this->getLang('cmnt_mod_subject'),$issue['id'], $project). "\r\n";}            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            
            if($reason =='delete') {
              $body2 = $this->getLang('cmt_del_intro').chr(10).chr(13);
            }
            else {
              $body2 = $this->getLang('issuemod_intro').chr(10).chr(13);
              $body3 = $this->getLang('issuemod_cmntauthor').$comment['author'].chr(10).
                       $this->getLang('issuemod_date').$comment['timestamp'].chr(10).
                       $this->getLang('issuemod_cmnt').$this->xs_format($comment['comment']).chr(10).chr(10); 
            }
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $body2.
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $body3.
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end'). "\r\n";

//            $body = utf8_encode($body);
            
            $from=$this->getConf('email_address'). "\r\n";
            
            $user_mail = pageinfo();
            if($user_mail['userinfo']['mail']===$issue['user_mail']) $to=$issue['assigned'];
            elseif($user_mail['userinfo']['mail']===$issue['assigned']) $to=$issue['user_mail'];
            else $to=$issue['user_mail'].', '.$issue['assigned'];
            
            $cc=$issue['add_user_mail'];
            $headers = "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers, $params=null);

    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                            
    function _emailForDscr($project,$issue)
    {       if($this->getConf('userinfo_email') ===0) return;
            $subject = sprintf($this->getLang('issuedescrmod_subject'),$issue['id'], $project). "\r\n";            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issuemod_intro').chr(10).chr(13).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).chr(10).
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $this->getLang('issuemod_date').$comment['timestamp'].chr(10).chr(10).
                    $this->getLang('th_description').chr(10).$issue['description'].chr(10).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end'). "\r\n";

//            $body = utf8_encode($body);
            $from=$this->getConf('email_address'). "\r\n";
            
            $user_mail = pageinfo();
            if($user_mail['userinfo']['mail']===$issue['user_mail']) $to=$issue['assigned']. "\r\n";
            elseif($user_mail['userinfo']['mail']===$issue['assigned']) $to=$issue['user_mail']. "\r\n";
            else $to=$issue['user_mail'].', '.$issue['assigned']. "\r\n";
            
            $cc=$issue['add_user_mail']. "\r\n";
            $headers = "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers, $params=null);
            

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
/* Create count output
*/
    function _count_render($issues,$start,$step,$next_start,$stat_filter,$sev_filter,$productfilter,$project)
    {   global $ID;
        $count = array();
        foreach ($issues as $issue)
        {
            if (($productfilter=='ALL') || (stristr($productfilter,$this->_get_one_value($issue,'product'))!= false))
            {
                $status = trim($this->_get_one_value($issue,'status'));
                if (($status != '') && (stripos($this->getConf('status_special'),$status)===false))
                    if ($this->_get_one_value($count,$status)=='')
                        {$count[$status] = array(1,$status);}
                    else
                        {$count[$status][0] += 1;}
            }                                
        }
        $rendered_count = '<div class="itl__count_div">'.'<table class="itl__count_tbl">';
        foreach ($count as $value)
        {
            //http://www.fristercons.de/fcon/doku.php?id=issuetracker:issuelist&do=showcaselink&showid=19&project=fcon_project
            // $ID.'&do=issuelist_filter&itl_sev_filter='.$value[1]
            $rendered_count .= '<tr><td><a href="'.DOKU_URL.'doku.php?id='.$ID.'&do=issuelist_filterlink'.'&itl_start='.$start.'&itl_step='.$step.'&itl_next='.$next_start.'&itl_stat_filter='.$value[1].'&itl_sev_filter='.$sev_filter.'&itl_prod_filter='.$productfilter.'&itl_project='.$project.'" >'.$value[1].'</a>&nbsp;</td><td>&nbsp;'.$value[0].'</td></tr>';
        }
        $rendered_count .= '</table></div>';
        return $rendered_count;
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

        $x_comment = preg_replace("/\[img\](http.*?)\[\/img\]/si", "<img src=\"\\1\"title=\"\\1\" alt=\"\\1\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(http.*?)\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\2\" alt=\"\\1\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img\](file.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\1\" alt=\"\\1\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(file.*?)\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\2\" alt=\"\\1\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img\](\:.*?)\[\/img\]/si", "<img src=\"". DOKU_URL . "lib/exe/fetch.php?media=\\1\" title=\"\\1\" alt=\"\\1\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(\:.*?)\](.*?)\[\/img\]/si", "<img src=\"". DOKU_URL . "lib/exe/fetch.php?media=\\1\" title=\"\\2\" alt=\"\\1\" />", $x_comment);
        $x_comment = preg_replace("/\[img\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\1\" \/>", $x_comment);
        $x_comment = preg_replace("/\[img=(.*?)\](.*?)\[\/img\]/si", "<img src=\"\\1\" title=\"\\2\" \/>", $x_comment);


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
    function it_edit_toolbar($type) {
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $it_edit_tb  = '<div class="it_edittoolbar">'.NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/bold.png\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('[b]','[/b]','$type')\">".NL;
        $it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/italic.png\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('[i]','[/i]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/underline.png\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('[u]','[/u]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/strikethrough.png\" name=\"btnStrike\" title=\"Strike through\" onClick=\"doAddTags('[s]','[/s]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/subscript.png\" name=\"btnSubscript\" title=\"Subscript\" onClick=\"doAddTags('[sub]','[/sub]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/superscript.png\" name=\"btnSuperscript\" title=\"Superscript\" onClick=\"doAddTags('[sup]','[/sup]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/hr.png\" name=\"btnLine\" title=\"hLine\" onClick=\"doHLine('[hr]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/ordered.png\" name=\"btnList\" title=\"Ordered List\" onClick=\"doList('[ol]','[/ol]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/unordered.png\" name=\"btnList\" title=\"Unordered List\" onClick=\"doList('[ul]','[/ul]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/quote.png\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('[blockquote]','[/blockquote]','$type')\">".NL; 
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/code.png\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('[code]','[/code]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/pen_red.png\" name=\"btnRed\" title=\"Red\" onClick=\"doAddTags('[red]','[/red]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/pen_green.png\" name=\"btnGreen\" title=\"Green\" onClick=\"doAddTags('[grn]','[/grn]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/pen_blue.png\" name=\"btnBlue\" title=\"Blue\" onClick=\"doAddTags('[blu]','[/blu]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/bg_yellow.png\" name=\"btn_bgYellow\" title=\"bgYellow\" onClick=\"doAddTags('[bgy]','[/bgy]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/link.png\" name=\"btn_link\" title=\"Link\" onClick=\"doAddTags('[link]','[/link]','$type')\">".NL;
      	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/img.png\" name=\"btn_img\" title=\"Image\" onClick=\"doAddTags('[img]','[/img]','$type')\">".NL;
      	$it_edit_tb .= "<a href=\"http://www.imageshack.us/\" target=\"_blank\"><<img class=\"xseditor_button\" src=\"".$imgBASE."/imageshack.png\" name=\"btn_ishack\" title=\"ImageShack upload (ext TaC !)\">></a>".NL;
        $it_edit_tb .= "<br></div>".NL; 
        return $it_edit_tb;                     
    }
/******************************************************************************/
    function get_issues_file_contents($project, $issue_id) {
        $pfile = metaFN($project, '.issues');   
        if (@file_exists($pfile))
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
              echo $Generated_Header;
              return;
             }
          }
        else
        	{   // promt error message that issue with ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">'.sprintf($this->getLang('msg_pfilemissing'),$pfile).'</div><br />';
              echo $Generated_Header;
              return;
          }
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
          if($new_value=='') $new_value = $this->getLang('mod_valempty');
          $mods[$mod_id]['timestamp'] = $issue['modified'];
          $mods[$mod_id]['user'] = $usr;
          $mods[$mod_id]['field'] = $column;
          $mods[$mod_id]['new_value'] = $new_value;
          
          // Save issues file contents
          $fh = fopen($modfile, 'w');
          fwrite($fh, serialize($mods));
          fclose($fh);
    }
/******************************************************************************/
}