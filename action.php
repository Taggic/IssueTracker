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
         'date'   => '2011-12-07',
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
             $this->project = $_POST['project'];         
         }
         elseif ($event->data === 'showcaselink') {
            $this->parameter = $_GET['showid'];
            $this->project = $_GET['project'];
         }
         elseif ($event->data === 'issuelist_next') {
            $this->itl_start = $_POST['itl_start'];
            $this->itl_step = $_POST['itl_step'];
            $this->itl_next = $_POST['itl_next'];
            $this->itl_pjct = $_POST['itl_project'];
            $this->itl_stat = $_POST['itl_stat_filter'];
            $this->itl_sev = $_POST['itl_sev_filter'];
            $this->itl_prod = $_POST['itl_prod_filter'];
         }
         elseif ($event->data === 'issuelist_previous') {
            $this->itl_start = $_POST['itl_start'];
            $this->itl_step = $_POST['itl_step'];
            $this->itl_next = $_POST['itl_next'];
            $this->itl_pjct = $_POST['itl_project'];
            $this->itl_stat = $_POST['itl_stat_filter'];
            $this->itl_sev = $_POST['itl_sev_filter'];
            $this->itl_prod = $_POST['itl_prod_filter'];
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
            if($ord === 10){ $res .= "<br />";  } 
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
                                              $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_commentfalse').'</div><br />';
                                              $checkFlag=true; 
                                              break;
                                          }
                                       }
                                   if ($checkFlag === false)
                                   {
                                       $comments[$comment_id]['id'] = $comment_id;    
                                       $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                                       $comments[$comment_id]['timestamp'] = htmlspecialchars(stripslashes($_REQUEST['timestamp']));
                                       $comments[$comment_id]['comment'] = htmlspecialchars(stripslashes($_REQUEST['comment']));    
                
                                       //Create comments file
                                       $xvalue = io_saveFile($comments_file,serialize($comments));
    
                                       // inform user (or assignee) about update
                                       $this->_emailForIssueMod($_REQUEST['project'],$issues[$_REQUEST['comment_issue_ID']], $comments[$comment_id]);                                 

                                       // update modified date
                                       $issues[$_REQUEST['comment_issue_ID']]['modified'] = date($this->getConf('d_format')); 
                                       $xvalue = io_saveFile($pfile,serialize($issues));
                                       $anker_id = 'resolved_'. uniqid((double)microtime()*1000000,1);                                   
                                       $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_commenttrue').$comment_id.'.</div><br />';
                                    } 
                                 }
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
                                  $Generated_Message = '<div class="it__positive_feedback"><a href="#'.$anker_id.'"></a>'.$this->getLang('msg_resolution_true').$issue_id.'</div>';
                                  msg($this->getLang('msg_resolution_true').$issue_id,1);
                                  $this->_emailForResolution($_REQUEST['project'], $issues[$_REQUEST['comment_issue_ID']]);
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
                $Generated_Header = '';                       
                $Generated_Table = $this->_table_render($a,$step,$start,$next_start,$stat_filter,$sev_filter,$productfilter); 
                $Generated_Scripts = $this->_scripts_render();
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
        
        // Build string to load versions select
/*        $versions = explode(',', $this->getConf('versions')) ;
        foreach ($versions as $x_versions)
        {
            $x_versions_select = $x_versions_select . "['".$x_versions."','".$x_versions."'],";
        }
*/         
        // Build string to load severity select
        $severity = explode(',', $this->getConf('severity')) ;
        foreach ($severity as $x_severity)
        {
            $x_severity_select = $x_severity_select . "['".$x_severity."','".$x_severity."'],";
        } 
        
        // Build string to load 'assign to' select from all user_mail of defined DW user groups
        global $auth;        
        $filter['grps']=$this->getConf('assign');
        $target = $auth->retrieveUsers(0,0,$filter); 
        $target2 = $this->array_implode($target);
        foreach ($target2 as $x_umail)
        {
                if (strrpos($x_umail, "@") > 0)
                {
                    $x_umail_select = $x_umail_select . "['".$x_umail."','".$x_umail."'],";
                }
        }      
        
        $BASE = DOKU_BASE."lib/plugins/issuetracker/";
        return    "<script type=\"text/javascript\" src=\"".$BASE."prototype.js\"></script><script type=\"text/javascript\" src=\"".$BASE."fabtabulous.js\"></script>
        <script type=\"text/javascript\" src=\"".$BASE."tablekit.js\"></script>
        <script type=\"text/javascript\">
            TableKit.options.editAjaxURI = '".$BASE."edit.php';
            TableKit.Editable.selectInput('status',{}, [".$x_status_select."]);
            TableKit.Editable.selectInput('product',{}, [".$x_products_select."]);
            TableKit.Editable.selectInput('severity',{}, [".$x_severity_select."]);
            TableKit.Editable.selectInput('assigned',{}, [".$x_umail_select."]);
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

        $ret = '<br /><br /><script type="text/javascript" src="include/selectupdate.js"></script>'.
               '<form class="issuetracker__form2" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'"><p>';
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
            $reduced_header = '';
            foreach ($configs as $config)
            {
                $reduced_header = $reduced_header."<th id='".$config."'>".strtoupper($config)."</th>";
            }
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
                        else 
                        {   $reduced_issues .= '<td'.$style.$isval.'</td>';
                        }
                    }
                        $reduced_issues .= '</tr>';
                }
            }
            
            $head = "<div class='issuetracker_div'><table id='".$project."' class='sortable resizable inline'>"."<thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th>".$reduced_header."</tr></thead>";
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
                      '<label class="it__cir_projectlabel">'.$this->getLang('lbl_issueqty').count($issues).'</label>'.NL.
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
               '   <td class="itl__showdtls" align ="left" width="30%">'.NL.
               '    <form  method="post" action="doku.php?id=' . $ID . '&do=showcase">'.NL.
               '       <label class="it__cir_projectlabel">'.$this->getLang('lbl_showid').'</label><br />'.NL.
               '       <input class="itl__showid_input" name="showid" id="showid" type="text" value="0"/>'.NL.
               '       <input type="hidden" name="project" id="project" value="'.$project.'"/>'.NL.
               '       <input type="hidden" name="itl_sev_filter" id="itl_sev_filter" value="'.$sev_filter.'"/>'.NL.
               '       <input type="hidden" name="itl_stat_filter" id="itl_stat_filter" value="'.$stat_filter.'"/>'.NL.
               '       <input class="itl__showid_button" id="showcase" type="submit" name="showcase" value="'.$this->getLang('btn_showid').'" title="'.$this->getLang('btn_showid_title').'"/>'.NL.
               '    </form>'.NL.
               '   </td>'.NL.
               '</tr>'.NL.'</tbody>'.NL.'</table>'.NL.'</div>'.NL;

         $ret = $ret.$head.$body;              
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
              $Generated_Header = '<div class="it__negative_feedback">'.$this->getLang('msg_issuemissing').$issue_id.'.</div><br />';
              echo $Generated_Header;
              return;
             }
          }
        else
        	{
              // promt error message that issue with ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">'.sprintf($this->getLang('msg_pfilemissing'),$pfile).'</div><br />';
              echo $Generated_Header;
              return;
          }	          
        
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
                      <td class="itd__col2">'.$this->getLang('lbl_reporter').'</td>
                      <td class="itd__col3"><a href="mailto:'.$__reportedby.'">'.$__reportedby.'</a></td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('th_created').':</td>
                      <td class="itd__col6">'.date($this->getConf('d_format'),strtotime($issue[$issue_id]['created'])).'</td>
                    </tr>
                   
                    <tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">'.$this->getLang('th_assigned').':</td>
                      <td class="itd__col3"><a href="mailto:'.$__assigened.'">'.$__assigened.'</a></td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">'.$this->getLang('th_modified').':</td>
                      <td class="itd__col6">'.date($this->getConf('d_format'),strtotime($issue[$issue_id]['modified'])).'</td>
                    </tr>
                    </tbody></table>';


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
                        if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
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
                            <td class="itd_tables_tdc2">'.$this->getLang('lbl_reporteradcontact').'</td>
                            <td class="itd_tables_tdc3"><a href="mailto:'.$issue[$issue_id]['add_user_mail'].'">'.$issue[$issue_id]['add_user_mail'].'</a></td>
                          </tr>'; 
                        }

$issue_client_details .= '</tbody><tr>'.NL.'
                            <td colspan="3" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                                <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                  <a href="#tbl_'.$anker_id.'" id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('gen_tab_open').'</a>
                                </div>'.NL.'
                            </td>
                            </tr>'.NL.'</tbody></table>';


                        $x_comment = $this->convertlabel($issue[$issue_id]['description']);
                        
$issue_initial_description = '<table class="itd__tables"><tbody>
                                <tr>
                                  <td class="itd_tables_tdh" colSpan="2" >'.$this->getLang('lbl_initdescr').'</td>
                                </tr>
                                <tr class="itd__tables_tr">
                                  <td width="1%"></td>
                                  <td>'.$this->xs_format($x_comment).'</td>
                                </tr>
                              </tbody></table>';

$issue_attachments = '<table class="itd__tables"><tbody>
                      <tr>
                        <td class="itd_tables_tdh">'.$this->getLang('lbl_symptlinks').'</td>
                      </tr>
                      <tr  class="itd__tables_tr">
                        <td style="padding-left:0.45em;">1. <a href="'.$issue[$issue_id]['attachment1'].'"><img border="0" alt="symptoms 1" style="margin-right:0.5em" vspace="1" align="middle" src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment1'].'" href="'.$issue[$issue_id]['attachment1'].'">'.$issue[$issue_id]['attachment1'].'</a></td>
                      </tr>'.
                     '<tr  class="itd__tables_tr">
                        <td style="padding-left:0.45em;">2. <a href="'.$issue[$issue_id]['attachment2'].'"><img border="0" alt="symptoms 2" style="margin-right:0.5em" vspace=1em align=absMiddle src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment2'].'" href="'.$issue[$issue_id]['attachment2'].'">'.$issue[$issue_id]['attachment2'].'</a></td>
                      </tr>'.
                     '<tr  class="itd__tables_tr">
                        <td style="padding-left:0.45em;">3. <a href="'.$issue[$issue_id]['attachment3'].'"><img border="0" alt="symptoms 3" style="margin-right:0.5em" vspace="1" align="middle" src="'.$imgBASE.'sympt.gif" width="16" height="16"></a><a title="'.$issue[$issue_id]['attachment3'].'" href="'.$issue[$issue_id]['attachment3'].'">'.$issue[$issue_id]['attachment3'].'</a></td>
                      </tr>'.
                     '</tbody></table>';              

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
                        if(($user_mail['userinfo']['mail'] === $issue[$issue_id]['user_mail']) or (strpos($target2,$user_mail['userinfo']['mail']) != false))
                        {   $x_mail = '<a href="mailto:'.$this->_get_one_value($a_comment,'author').'">'.$this->_get_one_value($a_comment,'author').'</a>'; }
                        else {   $x_mail = '<i> (user details hidden) </i>';  }

                        $issue_comments_log .= '<tr  class="itd__tables_tr">
                                                  <td class="itd_comment_trh"><label>['.$this->_get_one_value($a_comment,'id').'] </label>&nbsp;&nbsp;&nbsp;
                                                                            <label>'.date($this->getConf('d_format'),strtotime($this->_get_one_value($a_comment,'timestamp'))).' </label>&nbsp;&nbsp;&nbsp;
                                                                            <label>'.$x_mail.'</label></td>
                                                </tr>
                                                <tr  class="itd__tables_tr">
                                                  <td class="itd_comment_tr">'.$this->xs_format($x_comment).'</td>
                                                </tr>';
                  }
              }
              $issue_comments_log .='</tbody></table>'; 

                     
        //--------------------------------------------------------------------------------------------------------------
        // only admin/assignees and reporter are allowed to add comments if only user edit option is set
        //--------------------------------------------------------------------------------------------------------------
        // retrive some basic information
        $cur_date = date ($this->getConf('d_format'));
        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        else {$u_mail_check = $user_mail['userinfo']['mail'];}
        $user_check = $this->getConf('registered_users');
        
        //2011-12-02: bwenz code proposal (Issue 11)
        $x_resolution = $this->convertlabel($issue[$issue_id]['resolution']);
        if($x_resolution=="") { $x_resolution = "&nbsp;"; }
                        
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
// mod for editor ---------------------------------------------------------------------

$issue_add_comment .= '<div class="it_edittoolbar">'.NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/bold.png\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('[b]','[/b]','comment')\">".NL;
  $issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/italic.png\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('[i]','[/i]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/underline.png\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('[u]','[/u]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/strikethrough.png\" name=\"btnStrike\" title=\"Strike through\" onClick=\"doAddTags('[s]','[/s]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/subscript.png\" name=\"btnSubscript\" title=\"Subscript\" onClick=\"doAddTags('[sub]','[/sub]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/superscript.png\" name=\"btnSuperscript\" title=\"Superscript\" onClick=\"doAddTags('[sup]','[/sup]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/hr.png\" name=\"btnLine\" title=\"hLine\" onClick=\"doHLine('[hr]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/ordered.png\" name=\"btnList\" title=\"Ordered List\" onClick=\"doList('[ol]','[/ol]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/unordered.png\" name=\"btnList\" title=\"Unordered List\" onClick=\"doList('[ul]','[/ul]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/quote.png\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('[blockquote]','[/blockquote]','comment')\">".NL; 
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/code.png\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('[code]','[/code]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/pen_red.png\" name=\"btnRed\" title=\"Red\" onClick=\"doAddTags('[red]','[/red]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/pen_green.png\" name=\"btnGreen\" title=\"Green\" onClick=\"doAddTags('[grn]','[/grn]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/pen_blue.png\" name=\"btnBlue\" title=\"Blue\" onClick=\"doAddTags('[blu]','[/blu]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/bg_yellow.png\" name=\"btn_bgYellow\" title=\"bgYellow\" onClick=\"doAddTags('[bgy]','[/bgy]','comment')\">".NL;
	$issue_add_comment .= "<img class=\"button\" src=\"".$imgBASE."/link.png\" name=\"btn_link\" title=\"Link\" onClick=\"doAddTags('[link]','[/link]','comment')\">".NL;
  $issue_add_comment .= "<br></div>".NL;                      
// mod for editor ---------------------------------------------------------------------

$issue_add_comment .= '<script type="text/javascript" src="include/selectupdate.js"></script>'.NL.
                      '<form name="form1" method="post" accept-charset="'.$lang['encoding'].'">'.NL;
                      
$issue_add_comment .= formSecurityToken(false). 
                     '<input type="hidden" name="project" type="text" value="'.$project.'"/>'.NL.
                     '<input type="hidden" name="comment_file" type="text" value="'.$cfile.'"/>'.NL.
                     '<input type="hidden" id="comment_issue_ID" name="comment_issue_ID" type="text" value="'.$issue[$issue_id]['id'].'"/>'.NL.
                     '<input type="hidden" name="author" type="text" value="'.$u_mail_check.'"/>'.NL.        
                     '<input type="hidden" name="timestamp" type="text" value="'.$cur_date.'"/>'.NL.        
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
                                                      <a href="#'.$anker_id.'" id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('cmt_tab_open').'</a>
                                                    </div>'.NL.'
                                                </td>'.NL.'
                                             </tr></table>'.NL;

                  $alink_id++;
                  $blink_id = 'statanker_'.$alink_id;
                  $anker_id = 'anker_'.$alink_id;

$issue_edit_resolution ='<table class="itd__tables">
                         <tr>
                            <td class="itd_tables_tdh" colSpan="2" >Resolution</td>
                        </tr>';
$issue_edit_resolution .= '<tr class="itd__tables_tr">
                            <td width="1%"></td>
                            <td>'.$this->xs_format($x_resolution).'</td>
                          </tr>
                          <tr><td colSpan="2" style="display : none;" id="'.$blink_id.'">';

// mod for editor ---------------------------------------------------------------------
$issue_edit_resolution .= '<div class="it_edittoolbar">'.NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/bold.png\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('[b]','[/b]','x_resolution')\">".NL;
  $issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/italic.png\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('[i]','[/i]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/underline.png\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('[u]','[/u]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/strikethrough.png\" name=\"btnStrike\" title=\"Strike through\" onClick=\"doAddTags('[s]','[/s]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/subscript.png\" name=\"btnSubscript\" title=\"Subscript\" onClick=\"doAddTags('[sub]','[/sub]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/superscript.png\" name=\"btnSuperscript\" title=\"Superscript\" onClick=\"doAddTags('[sup]','[/sup]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/hr.png\" name=\"btnLine\" title=\"hLine\" onClick=\"doHLine('[hr]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/ordered.png\" name=\"btnList\" title=\"Ordered List\" onClick=\"doList('[ol]','[/ol]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/unordered.png\" name=\"btnList\" title=\"Unordered List\" onClick=\"doList('[ul]','[/ul]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/quote.png\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('[blockquote]','[/blockquote]','x_resolution')\">".NL; 
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/code.png\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('[code]','[/code]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/pen_red.png\" name=\"btnRed\" title=\"Red\" onClick=\"doAddTags('[red]','[/red]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/pen_green.png\" name=\"btnGreen\" title=\"Green\" onClick=\"doAddTags('[grn]','[/grn]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/pen_blue.png\" name=\"btnBlue\" title=\"Blue\" onClick=\"doAddTags('[blu]','[/blu]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/bg_yellow.png\" name=\"btn_bgYellow\" title=\"bgYellow\" onClick=\"doAddTags('[bgy]','[/bgy]','x_resolution')\">".NL;
	$issue_edit_resolution .= "<img class=\"button\" src=\"".$imgBASE."/link.png\" name=\"btn_link\" title=\"Link\" onClick=\"doAddTags('[link]','[/link]','x_resolution')\">".NL;
  $issue_edit_resolution .= "<br></div>".NL;                      
// mod for editor ---------------------------------------------------------------------

$issue_edit_resolution .= '<form name="edit_resolution" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'">'.NL;                                            
$issue_edit_resolution .= formSecurityToken(false).
                          '<input type="hidden" name="project" type="text" value="'.$project.'"/>'.NL.
                          '<input type="hidden" name="comment_issue_ID" type="text" value="'.$issue[$issue_id]['id'].'"/>'.NL.
                          '<input type="hidden" id="add_resolution" name="add_resolution" type="text" value="1"/>'.NL;        
    
$issue_edit_resolution .= "<textarea id='x_resolution' name='x_resolution' type='text' cols='106' rows='7' value=''>$x_resolution</textarea>";
                              
                      if ($this->getConf('use_captcha')==1) 
                      {   $helper = null;
              		        if(@is_dir(DOKU_PLUGIN.'captcha'))
              			         $helper = plugin_load('helper','captcha');
              			         
              		        if(!is_null($helper) && $helper->isEnabled())
              			      {  $issue_edit_resolution .= '<p>'.$helper->getHTML().'</p>'; }
                      }
                      
                      $cell_ID = 'img_tab_open_comment'.$blink_id;
                      // check if only registered users are allowed to add comments
                      // ¦ perm — the user's permissions related to the current page ($ID)
$issue_edit_resolution .= '<input  type="hidden" class="showid__option" name="showid" id="showid" type="text" size="10" value="'.$this->parameter.'"/>'.
                      '<input class="button" id="store_resolution" type="submit" name="store_resolution" value="'.$this->getLang('btn_add').'" title="'.$this->getLang('btn_add_title').'");/>'.
                      '</form>'.NL.'</td>'.NL.'</tr>'.NL.
                      '<tr>'.NL.'
                          <td colspan="2" class="img_tab_open_comment" id="'.$cell_ID.'">'.NL.'
                              <div class="lnk_tab_open_comment" id="'.$cell_ID.'">
                                <a href="#'.$anker_id.'" id="'.$anker_id.'" onClick="tab_open(\''.$blink_id.'\',\''.$cell_ID.'\')">'.$this->getLang('rsl_tab_open').'</a>
                              </div>'.NL.'
                          </td>'.NL.'
                       </tr></table>'.NL;
        }
        else {
            $issue_edit_resolution ='<table class="itd__tables">
                                     <tr>
                                        <td class="itd_tables_tdh" colSpan="2" >Resolution</td>
                                    </tr>';
            $issue_edit_resolution .= '<tr class="itd__tables_tr">
                                        <td width="1%"></td>
                                        <td>'.$this->xs_format($x_resolution).'</td>
                                      </tr></table>'.NL;

            $wmsg = 'Please <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">'.$this->getLang('lbl_signin'); 
            $issue_edit_resolution .= '<div class="it__standard_feedback">'.$wmsg.'</div>';                      
        }


        
        //2011-12-02: bwenz code proposal (Issue 11)                                   
//        $ret = $issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment;
        $ret = $issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment . $issue_edit_resolution;

        return $ret;
    }

/******************************************************************************/
/* send an e-mail to user due to issue resolution
*/                            
    function _emailForResolution($project,$issue)
    {  if ($this->getConf('userinfo_email')==1)
        {   $subject = sprintf($this->getLang('issue_resolved_subject'),$issue['id'], $project);            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).$this->getLang('issue_resolved_intro').chr(10).chr(13).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issue_resolved_text').$this->xs_format($issue['resolution']).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end');

            $from=$this->getConf('email_address') ;
            $to=$issue['user_mail'];
            $cc=$issue['add_user_mail'];
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
        }
    }
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/                            
    function _emailForIssueMod($project,$issue,$comment)
    {
        if ($this->getConf('userinfo_email')==1)
        {
            $subject = sprintf($this->getLang('issuemod_subject'),$issue['id'], $project);            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).$this->getLang('issuemod_intro').chr(10).chr(13).
            $this->getLang('issuemod_issueid').$issue['id'].chr(10).
            $this->getLang('issuemod_status').$issue['status'].chr(10).
            $this->getLang('issuemod_product').$issue['product'].chr(10).
            $this->getLang('issuemod_version').$issue['version'].chr(10).
            $this->getLang('issuemod_severity').$issue['severity'].chr(10).
            $this->getLang('issuemod_creator').$issue['user_name'].chr(10).
            $this->getLang('issuemod_title').$issue['title'].chr(10).
            $this->getLang('issuemod_cmntauthor').$comment['author'].chr(10).
            $this->getLang('issuemod_date').$comment['timestamp'].chr(10).
            $this->getLang('issuemod_cmnt').$this->xs_format($comment['comment']).chr(10).
            $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
            $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end');

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
/* Create count output
*/
    function _count_render($issues,$start,$step,$next_start,$stat_filter,$sev_filter,$productfilter,$project)
    {   global $ID;
        $count = array();
        foreach ($issues as $issue)
        {
            if (($productfilter=='ALL') || (stristr($productfilter,$this->_get_one_value($issue,'product'))!= false))
            {
                $status = $this->_get_one_value($issue,'status');
                if ($status != '')
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
        $x_comment = preg_replace("/\[link\](.*?)\[\/link\]/si", "<a target=\"_blank\" href=\"\\1\">\\1</a>", $x_comment);

      return $x_comment;
    }
/******************************************************************************/
}