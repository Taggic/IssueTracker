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
         'date'   => '2011-09-29',
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
         if ($event->data === 'showcase') {
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
         }
         elseif ($event->data === 'issuelist_previous') {
            $this->itl_start = $_POST['itl_start'];
            $this->itl_step = $_POST['itl_step'];
            $this->itl_next = $_POST['itl_next'];
            $this->itl_pjct = $_POST['itl_project'];
            $this->itl_stat = $_POST['itl_stat_filter'];
            $this->itl_sev = $_POST['itl_sev_filter'];
         }
         elseif ($event->data === 'issuelist_filter') {
            $this->itl_start = $_POST['itl_start'];
            $this->itl_step = $_POST['itl_step'];
            $this->itl_next = $_POST['itl_next'];
            $this->itl_pjct = $_POST['itl_project'];
            $this->itl_stat = $_POST['itl_stat_filter'];
            $this->itl_sev = $_POST['itl_sev_filter'];
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

         if (($data->data == 'showcase') || ($data->data == 'showcaselink')) {
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
                      echo '<div class="it__negative_feedback">Project file does not exist: ' . $project . '.issues .</div><br />';
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
                                              $Generated_Header = '<div class="it__negative_feedback">This comment does already exist and was not added again.</div><br />';
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
                                       $Generated_Header = '<div class="it__positive_feedback">Your comment has been successfully stored with ID #'.$comment_id.'.</div><br />';
                                       
                                    }
                                 }
                            }
                       }
                 }
                 // Render 
                                                        // Array  , project name
                 $Generated_Table = $this->_details_render($issues, $project);                 
                 //$data->doc .= $Generated_Header.$Generated_Table.$Generated_feedback;

        }
        
        // scrolling next/previous issues 
        elseif (($data->data == 'issuelist_next') || ($data->data == 'issuelist_previous') || ($data->data == 'issuelist_filter'))  {
                 $data->preventDefault();
                 $renderer->info['cache'] = false;         
                 $itl_start = $this->itl_start;
                 $step = $this->itl_step;
                 if ($step == '') {$step=10;}
                 $itl_next = $this->itl_next;
                 $a = $this->itl_pjct;
                                                   
                 $pfile = metaFN($a, '.issues');        
                if (@file_exists($pfile))
                	{$issues  = unserialize(@file_get_contents($pfile));}
                else
                	{   // prompt error message that issue with ID does not exist
                      echo '<div class="it__negative_feedback">Project file does not exist: ' . $project . '.issues .</div><br />';
                      return;
                  }            	          

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
                 elseif ($data->data == 'issuelist_filter') {
                    $start = $itl_start;
                    $next_start = $start + $step;                    
                    if ($next_start>count($issues)) { $next_start=count($issues); }                 
                 }
                                 

                
                $stat_filter = $this->itl_stat;
                if ($stat_filter == '') {$stat_filter='ALL';}
                $sev_filter = $this->itl_sev;
                if ($sev_filter == '') {$sev_filter='ALL';}
                $Generated_Header = '';                        
                $Generated_Table = $this->_table_render($a,$step,$start,$next_start,$stat_filter,$sev_filter); 
                $Generated_Scripts = $this->_scripts_render();
        }
        else return;
        
        // Render            
        echo $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report;

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
        return    "<script type='text/javascript' src=\"".$BASE."prototype.js\"></script><script type='text/javascript' src=\"".$BASE."fabtabulous.js\"></script>
        <script type='text/javascript' src=\"".$BASE."tablekit.js\"></script>
        <script type='text/javascript'>
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
    function _table_render($project,$step,$start,$next_start,$stat_filter,$sev_filter)
    {
        global $ID;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $style =' style="text-align:left; white-space:pre-wrap;">';
        $user_grp = pageinfo();
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
        $stat_filter=strtoupper($stat_filter);
        $sev_filter=strtoupper($sev_filter);
        
        // get issues file contents
        $pfile = metaFN($project, '.issues'); 

        if (@file_exists($pfile))
        	{$issues  = unserialize(@file_get_contents($pfile));}
        else
        	{$issues = array();}            	          

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

            $head = "<div class='itl__table'><table id='".$project."' class='sortable editable resizable inline'>".
                    "<thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th>".
                    "<th id='created'>Created</th>".
                    "<th id='product'>Product</th>".
                    "<th id='version'>Version</th>".
                    "<th id='severity'>Severity</th>".
                    "<th id='status'>Status</th>".
                    "<th id='user_name'>User name</th>".
                    "<th id='title'>Title</th>".
                    "<th id='assigned'>assigned</th>". 
                    "<th id='resolution'>Resolution</th>".
                    "<th id='modified'>Modified</th></tr></thead>";        
            $body = '<tbody>';
        
            for ($i=$next_start-1;$i>=0;$i=$i-1)
            {   // check start and end of rows to be displayed
                    $issue = $issues[$i];                    
                    $a_status = strtoupper($this->_get_one_value($issue,'status'));
                    $a_severity = strtoupper($this->_get_one_value($issue,'severity'));
                    
                if ((($stat_filter=='ALL') || (stristr($stat_filter,$a_status)!= false)) && (($sev_filter=='ALL') || (stristr($sev_filter,$a_severity)!= false)))
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
                    
                                            
                    $body .= '<tr id = "'.$project.'_'.$this->_get_one_value($issue,'id').'">'.                       
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'id').'</td>'.
                             '<td class="itl__td_date">'.$this->_get_one_value($issue,'created').'</td>'.
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'product').'</td>'.
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'version').'</td>'.
                             '<td'.$severity_img.'</td>'.
                             '<td'.$status_img.'</td>'.
                             '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'user_mail').'">'.$this->_get_one_value($issue,'user_name').'</a></td>'. 
                             '<td class="canbreak itl__td_standard">'.$itl_item_title.'</td>'.
                             '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'assigned').'">'.$this->_get_one_value($issue,'assigned').'</a></td>'. 
                             '<td class="canbreak itl__td_standard">'.$this->_get_one_value($issue,'resolution').'</td>'.
                             '<td class="itl__td_date">'.$this->_get_one_value($issue,'modified').'</td>'.
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

                if ((($stat_filter=='ALL') || (stristr($stat_filter,$a_status)!= false)) && (($sev_filter=='ALL') || (stristr($sev_filter,$a_severity)!= false)))
                {   
                    if ($y>=$step) break;
                    $y=$y+1;
                    
                    $reduced_issues = $reduced_issues.'<tr id = "'.$project.'_'.$this->_get_one_value($issue,'id').'">'.
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
                        else 
                        {
                            $reduced_issues .= '<td'.$style.$isval.'</td>';
                        }
                    }
                        $reduced_issues .= '</tr>';
                }
            }
            
            $head = "<div class='issuetracker_div'><table id='".$project."' class='sortable resizable inline'>"."<thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th>".$reduced_header."</tr></thead>";
            $body = '<tbody>'.$reduced_issues.'</tbody></table></div>';
        }

        $ret = '<div>'.
               '<script  type="text/javascript"> 
                       function changeAction(where) { 
                          if(where==1) { 
                             document.forms["myForm"].action = "doku.php?id=' . $ID . '&do=issuelist_previous"; 
                          } 
                          else if(where==2){ 
                             document.forms["myForm"].action = "doku.php?id=' . $ID . '&do=issuelist_next"; 
                          } 
                          else if(where==3){ 
                             document.forms["myForm"].action = "doku.php?id=' . $ID . '&do=issuelist_filter"; 
                          } 
                          document.forms["myForm"].submit(); 
                       } 
                    </script>'.                    
               '<table class="itl__t1"><tbody>'.
               '<tr class="itd__tables_tr">'.
                  '<td colspan="5" align="left"   valign="middle" height="40">'.
                      '<label class="it__cir_projectlabel">Quantity of Issues:&nbsp;'.count($issues).'</label>'.
                  '</td>'.
               '</tr>'.
               '<tr>'.
                    '<form name="myForm" action="" method="post"> 
                      <td align ="left" valign="top" width="20%">
                         <label class="it__cir_projectlabel">Scroll issue List &nbsp;&nbsp;&nbsp;</label>
                         <input type="hidden" name="itl_start" id="itl_start" value="'.$start.'"/>
                         <input type="hidden" name="itl_step" id="itl_step" value="'.$step.'"/>
                         <input type="hidden" name="itl_next" id="itl_next" value="'.$next_start.'"/>
                         <input type="hidden" name="itl_project" id="itl_project" value="'.$project.'"/>
                         <label class="it__cir_projectlabel">Filter Severity: </label>
                         <label class="it__cir_projectlabel">Filter Status: </label>
                      </td>
                      <td align ="left" width="20%">
                         <input type="button" name="showprevious" value="<<<" title="previous Issues" onClick="changeAction(1)"/>
                         <input class="itl__step_input" name="itl_step" id="itl_step" type="text" value="'.$step.'"/>
                         <input type="button" name="shownext" value=">>>" title="next Issues" onClick="changeAction(2)"/><br />
                         <input class="itl__sev_filter" name="itl_sev_filter" id="itl_sev_filter" type="text" value="'.$sev_filter.'"/><br />
                         <input class="itl__stat_filter" name="itl_stat_filter" id="itl_stat_filter" type="text" value="'.$stat_filter.'"/>
                         <input type="button" name="go" value="Go" title="Go" onClick="changeAction(3)"/><br />
                      </td>
                    </form>'.
                 '<td width="10%">&nbsp;</td>'.
                 '<td align ="left" width="30%">'.
                     '<form  method="post" action="doku.php?id=' . $ID . '&do=showcase"><label class="it__cir_projectlabel"> Show details of Issue:</label>'.
                         '<input class="itl__showid_input" name="showid" id="showid" type="text" value="0"/>'.
                         '<input type="hidden" name="project" id="project" value="'.$project.'"/>'.
                         '<input type="hidden" name="itl_sev_filter" id="itl_sev_filter" value="'.$sev_filter.'"/>'.
                         '<input type="hidden" name="itl_stat_filter" id="itl_stat_filter" value="'.$stat_filter.'"/>'.
                         '<input class="itl__showid_button" id="showcase" type="submit" name="showcase" value="Go" title="Go"/>'.
                     '</form>'.
                 '</td>'.
                 '<td width="20%"></td>'.
               '</tr></tbody></table></div>';

                            
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
              $Generated_Header = '<div class="it__negative_feedback">There does no Issue exist with ID '.$issue_id.'.</div><br />';
              echo $Generated_Header;
              return;
             }
          }
        else
        	{
              // promt error message that issue with ID does not exist
              $Generated_Header = '<div class="it__negative_feedback">Project file does not exist: '.$pfile.'</div><br />';
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
$issue_edit_head = '<table class="itd__title">'.
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
                      <td class="itd__col2">ID:</td>
                      <td class="itd__col3">'.$issue[$issue_id]['id'].'</td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">Project:</td>
                      <td class="itd__col6">'.$project.'</td>
                    </tr>';
                   
$issue_edit_head .= '<tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">Severity:</td>
                      <td class="itd__col3">'.$severity_img.$issue[$issue_id]['severity'].'</td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">Product:</td>
                      <td class="itd__col6">'.$issue[$issue_id]['product'].'</td>
                    </tr>';
                   
$issue_edit_head .= '<tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">Status:</td>
                      <td class="itd__col3">'.$status_img.$issue[$issue_id]['status'].'</td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">Version:</td>
                      <td class="itd__col6">'.$issue[$issue_id]['version'].'</td>
                    </tr>';

$issue_edit_head .= '<tr class="itd_tr_standard">                      
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">Reported by:</td>
                      <td class="itd__col3"><a href="mailto:'.$__reportedby.'">'.$__reportedby.'</a></td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">created:</td>
                      <td class="itd__col6">'.$issue[$issue_id]['created'].'</td>
                    </tr>
                   
                    <tr class="itd_tr_standard">
                      <td class="it__left_indent"></td>
                      <td class="itd__col2">Assigned to:</td>
                      <td class="itd__col3"><a href="mailto:'.$__assigened.'">'.$__assigened.'</a></td>
                      <td class="itd__col4"></td>                   
                      <td class="itd__col5">modified:</td>
                      <td class="itd__col6">'.$issue[$issue_id]['modified'].'</td>
                    </tr>
                    </tbody></table>';


$issue_client_details = '<table class="itd__tables"><tbody>
                        <tr>
                           <td class="itd_tables_tdh" colSpan="3">Reporter Details</td>
                        </tr>
                        <tr class="itd__tables_tr">
                           <td class="it__left_indent"></td>
                           <td class="itd_tables_tdc2">Name:</td>
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
                            <td class="itd_tables_tdc2">Email:</td>
                            <td class="itd_tables_tdc3"><a href="mailto:'.$issue[$issue_id]['user_mail'].'">'.$issue[$issue_id]['user_mail'].'</a></td>
                          </tr>
                          <tr class="itd__tables_tr">
                            <td class="it__left_indent"></td>
                            <td class="itd_tables_tdc2">Phone:</td>
                            <td class="itd_tables_tdc3">'.$issue[$issue_id]['user_phone'].'</td>
                          </tr>
                          <tr class="itd__tables_tr">
                            <td class="it__left_indent"></td>
                            <td class="itd_tables_tdc2">Add contact:</td>
                            <td class="itd_tables_tdc3"><a href="mailto:'.$issue[$issue_id]['add_user_mail'].'">'.$issue[$issue_id]['add_user_mail'].'</a></td>
                          </tr>
                          </tbody></table>'; 
                        }
                        else {
                          $issue_client_details .= '</tbody></table>';
                        }

                        $x_comment = $this->convertlabel($issue[$issue_id]['description']);

$issue_initial_description = '<table class="itd__tables"><tbody>
                                <tr>
                                  <td class="itd_tables_tdh" colSpan="2" >Initial description</td>
                                </tr>
                                <tr class="itd__tables_tr">
                                  <td width="1%"></td>
                                  <td>'.$x_comment.'</td>
                                </tr>
                              </tbody></table>';

$issue_attachments = '<table class="itd__tables"><tbody>
                      <tr>
                        <td class="itd_tables_tdh">Links to symptom files</td>
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
                        <td class="itd_tables_tdh" colSpan="2" >Comments (work log)</td>
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
                                                                            <label>'.$this->_get_one_value($a_comment,'timestamp').' </label>&nbsp;&nbsp;&nbsp;
                                                                            <label>'.$x_mail.'</label></td>
                                                </tr>
                                                <tr  class="itd__tables_tr">
                                                  <td class="itd_comment_tr">'.$x_comment.'</td>
                                                </tr>';
                  }
              }
              $issue_comments_log .='</tbody></table>'; 

                     
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

/*        echo 'result of user_check = '.$user_check.'<br />'.
             'result of user_mail[perm] = '.$user_mail['perm'].'<br />'.
             'result of _cFlag = '.$_cFlag.'<br />';
*/
        if($_cFlag === true) {
$issue_add_comment ='<table class="itd__tables">'.
                      '<tr>'.
                        '<td class="itd_tables_tdh" colSpan="2" >Add a new comment</td>
                      </tr><tr><td>';
                      
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
                                            '</form></td></tr></table>';
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
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            
            $body = 'Dear user,'.chr(10).chr(10).'Your reported issue got a new comment.'.chr(10).chr(13).
            'ID:'.chr(9).chr(9).chr(9).chr(9).$issue['id'].chr(10).
            'Status:'.chr(9).chr(9).chr(9).$issue['status'].chr(10).
            'Product:'.chr(9).chr(9).chr(9).$issue['product'].chr(10).
            'Version:'.chr(9).chr(9).chr(9).$issue['version'].chr(10).
            'Severity:'.chr(9).chr(9).chr(9).$issue['severity'].chr(10).
            'Creator:'.chr(9).chr(9).chr(9).$issue['user_name'].chr(10).
            'Title:'.chr(9).chr(9).chr(9).$issue['title'].chr(10).
            'Comment by:'.chr(9).chr(9).$comment['author'].chr(10).
            'submitted on:'.chr(9).$comment['timestamp'].chr(10).
            'Comment:'.chr(9).chr(9).$comment['comment'].chr(10).
            'see details:'.chr(9).chr(9).DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
            'best regards'.chr(10).$project.' Issue Tracker';

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