<?php
/**
*  IssueTracker Plugin: allows to create simple issue tracker
*
* initial code from DokuMicroBugTracker Plugin: allows to create simple bugtracker
*
* @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
* @author     Taggic <taggic@t-online.de>
* 
* 
* 
*/
//session_start();
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');  
require_once(DOKU_PLUGIN.'issuetracker/assilist.php');    
/******************************************************************************
* All DokuWiki plugins to extend the parser/rendering mechanism
* need to inherit from this class
*/
class syntax_plugin_issuetracker extends DokuWiki_Syntax_Plugin 
{
/******************************************************************************/
/* return some info
*/
    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    function getType(){ return 'substition';}
    function getPType(){ return 'block';}
    function getSort(){ return 167;}
    
/******************************************************************************/
/* Connect pattern to lexer
*/
    function connectTo($mode){
        $this->Lexer->addSpecialPattern('\{\{issuetracker>[^}]*\}\}',$mode,'plugin_issuetracker');
    }
    
/******************************************************************************/
/* Handle the match
*/
    function handle($match, $state, $pos,Doku_Handler &$handler){
        $match = substr($match,15,-2); //strip markup from start and end
        //handle params
        $data = array();
        $params = explode('|',$match);

        foreach($params as $param) {            
            $splitparam = explode('=',$param);
            if ($splitparam[1] != '')
            {
                if ($splitparam[0]=='project')    { $data['project']   = trim(strtolower($splitparam[1])) ;}
                if ($splitparam[0]=='product')    { $data['product']    = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='status')     { $data['status']     = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='severity')   { $data['severity']   = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='display')    { $data['display']    = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='view')       { $data['view']       = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='controls')   { $data['controls']   = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='prod_limit') { $data['prod_limit'] = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='id')         { $data['id']         = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='version')    { $data['version']    = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='component')  { $data['component']  = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='tblock')     { $data['tblock']     = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='assignee')   { $data['assignee']   = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='reporter')   { $data['reporter']   = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='myissues')   { $data['myissues']   = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='config')     { $data['config']     = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='sort')       { $data['sort']       = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='columns')    { $data['columns']    = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='asympupl')   { $data['asympupl']    = trim(strtoupper($splitparam[1])) ;}
                if ($splitparam[0]=='email_address') {$data['email_address'] = trim(strtolower($splitparam[1])) ;}
                   /*continue;*/
            }
        }
               
        return $data;
    }

/******************************************************************************/
/* Captcha OK	    
*/
		function _captcha_ok()
		{        			
			$helper = null;		

			if(@is_dir(DOKU_PLUGIN.'captcha')) 
      { $helper = plugin_load('helper','captcha'); }
      
			if(!is_null($helper) && $helper->isEnabled())
			{	return $helper->check(); }
 
			return ($this->getConf('use_captcha'));
		}
    
/******************************************************************************/
/* Create output
*/
    function render($mode,Doku_Renderer &$renderer, $data) {        
        global $ID;
        $project = htmlspecialchars(stripslashes($_REQUEST['project']));
        if(strlen($project) <1) $project = $data['project'];
        else $data['project'] = $project;
        
        if ($mode == 'xhtml'){
            
              $renderer->info['cache'] = false;     
                 
              $Generated_Header  = '';
              $Generated_Table   = '';
              $Generated_Scripts = '';
              $Generated_Report  = '';
  
              if ($data['display']    == '') {$data['display']    = 'ISSUES';}
              if ($data['view']       == '') {$data['view']       = '10';}
              if ($data['controls']   == '') {$data['controls']   = 'ON';}
              if ($data['prod_limit'] == '') {$data['prod_limit'] = 'OFF';}
              if ($data['id']         == '') {$data['id'] = '0';}
              if ($data['severity']   == '') {$data['severity']   = 'ALL';}
              if ($data['status']     == '') {$data['status']     = 'ALL';}
              if ($data['product']    == '') {$data['product']    = 'ALL';}
              if ($data['version']    == '') {$data['version']    = 'ALL';}
              if ($data['component']  == '') {$data['component']  = 'ALL';}
              if ($data['tblock']     == '') {$data['tblock']     = false;}
              else { $data['tblock']  = true; }
              if ($data['assignee']   == '') {$data['assignee']   = 'ALL';}
              if ($data['reporter']   == '') {$data['reporter']   = 'ALL';}
          	  if ($data['myissues']   == '') {$data['myissues']   = false;}
              else { $data['myissues']= true; }
              if ($data['config']     == '') {$data['config']     = false;}
              else { $data['config']  = true; }
              if($data['asympupl']    == '') {$data['asympupl']=false;}
              else{$data['asympupl']=true;}
              
              if ($this->validEmail($data['email_address'])===false ) { 
                  if($data['email_address']!='') { msg("invalid mail given by syntax",-1);}
                  $data['email_address'] = $this->getConf('email_address');
              }

            if (stristr($data['display'],'FORM')!= false) 
            {
                //If it is a user report add it to the db-file
                if (isset($_REQUEST['severity'])) 
                {
                    if ($_REQUEST['severity'])
                      {
                          // check if captcha is to be used by issue tracker in general
                          if ($this->getConf('use_captcha') === 0) { $captcha_ok = 1;}
                          else { $captcha_ok = ($this->_captcha_ok());}
                          
                          if ($captcha_ok)
                            {
                                if (checkSecurityToken())
                                {   // get issues file contents
                                    $all = false;
                                    $issues = $this->_get_issues($data, $all);
                                   
                                    //Add it to the issue file
                                    $issue_id=count($issues);      
                                    foreach ($issues as $value)
                                        {if ($value['id'] >= $issue_id) {$issue_id=$value['id'] + 1;}}

                                    $issues[$issue_id]['id'] = $issue_id;    
                                    $issues[$issue_id]['product'] = htmlspecialchars(stripslashes($_REQUEST['product']));
                                    $issues[$issue_id]['version'] = htmlspecialchars(stripslashes($_REQUEST['version']));
                                    $issues[$issue_id]['component'] = htmlspecialchars(stripslashes($_REQUEST['component']));
                                    $issues[$issue_id]['tblock'] = htmlspecialchars(stripslashes($_REQUEST['tblock']));
                                    $issues[$issue_id]['severity'] = htmlspecialchars(stripslashes($_REQUEST['severity']));
                                    $issues[$issue_id]['created'] = htmlspecialchars(stripslashes($_REQUEST['created']));
                                    $status = explode(',', $this->getConf('status')) ;
                                    $issues[$issue_id]['status'] = $status[0];
                                    $issues[$issue_id]['user_name'] = htmlspecialchars(stripslashes($_REQUEST['user_name']));
                                    $issues[$issue_id]['user_mail'] = trim(htmlspecialchars(stripslashes($_REQUEST['user_mail'])));
                                    $issues[$issue_id]['user_phone'] = htmlspecialchars(stripslashes($_REQUEST['user_phone']));
                                    $issues[$issue_id]['add_user_mail'] = htmlspecialchars(stripslashes($_REQUEST['add_user_mail']));
//                                    $issues[$issue_id]['title'] = htmlspecialchars(stripslashes($_REQUEST['title']));
                                    $issues[$issue_id]['title'] = htmlspecialchars($_REQUEST['title']);
//                                    $issues[$issue_id]['description'] = htmlspecialchars(stripslashes($_REQUEST['description']));
                                    $issues[$issue_id]['description'] = htmlspecialchars($_REQUEST['description']);
                                    $issues[$issue_id]['attachment1'] = htmlspecialchars(stripslashes($_REQUEST['attachment1']));
                                    $issues[$issue_id]['attachment2'] = htmlspecialchars(stripslashes($_REQUEST['attachment2']));
                                    $issues[$issue_id]['attachment3'] = htmlspecialchars(stripslashes($_REQUEST['attachment3']));
                                    $issues[$issue_id]['assigned'] = '';
                                    $issues[$issue_id]['resolution'] = '';
                                    $issues[$issue_id]['comments'] = '';
                                    $issues[$issue_id]['modified'] = htmlspecialchars(stripslashes($_REQUEST['modified']));
    
                                    $xuser = $issues[$issue_id]['user_mail'];
                                    $xdescription = $issues[$issue_id]['description'];

//echo "Beschreibung: ".$xdescription."<br />";

// *****************************************************************************
// upload a symptom file
// *****************************************************************************
                                    // check if current user is admin/assignee
                                    // check if syntax parameter asympupl is switched on
                                    $user_grp    = pageinfo();
                                    if(array_key_exists('userinfo', $user_grp))
                                    {   foreach ($user_grp['userinfo']['grps'] as $ugrp)
                                        {  $user_grps = $user_grps . $ugrp;  }
                                    }
                                    else
                                    {   $user_grps = 'all';  }
                                    $allowed_users = explode('|', $this->getConf('assign'));
                                    $cFlag = false;
                                    foreach ($allowed_users as $w) 
                                    {   // check if one of the assigned user roles does match with current user roles
                                        if (strpos($user_grps,$w)!== false)
                                        {   $cFlag = true;
                                            break;  } 
                                    }      
  
                                    $mime_type1 = $_FILES['attachment1']['type'];
                                    if((($this->getConf('upload')> 0) ||(($cFlag === true) && ($data['asympupl']=true)) || ($this->getConf('registered_users')== 0)) && (strlen($mime_type1)>1)) {
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

                                    //check user mail address, necessary for further clarification of the issue
                                    $valid_umail = $this->validEmail($xuser);
                                    if ( ($valid_umail == true) && ((stripos($xdescription, " ") > 0) || (strlen($xdescription)>5)) && (strlen($issues[$issue_id]['version']) >0))
                                    {                                
                                          // assemble the path to IssueTracker data store & file
                                          if($this->getConf('it_data')==false) $pfile = DOKU_CONF."../data/meta/".$data['project'].'.issues';
                                          else $pfile = DOKU_CONF."../". $this->getConf('it_data').$data['project'].'.issues';
                                
                                          //save issue-file
                                          $xvalue = io_saveFile($pfile,serialize($issues)); 
                                          $this->_log_mods($data['project'], $issues[$issue_id], $issues[$issue_id]['user_name'], 'status', '', $issues[$issue_id]['status']);
                                          
                                          $pstring = sprintf("showid=%s&project=%s", urlencode($issues[$issue_id]['id']), urlencode($project));
                                          $tmp_link = '<a href="'.DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" >'.$issue_id.'</a>';
                                          
                                          $Generated_Header .= '<div class="it__positive_feedback">'.$this->getLang('msg_reporttrue').$tmp_link.'</div>';
                                          $this->_emailForNewIssue($data['project'],$issues[$issue_id],$data['email_address']);
                                          $_REQUEST['description'] = '';
                                    }
                                
                                    else
                                    {
                                        $wmsg ='';
                                        if ($valid_umail == false) 
                                            { $wmsg = $this->getLang('wmsg1'); }
                                        elseif (strlen($issues[$issue_id]['version']) <1)
                                            { $wmsg = $this->getLang('wmsg2'); }
                                        else 
                                            { $wmsg = $this->getLang('wmsg3').' ('.stripos($xdescription, " ").', '.strlen($xdescription).')'; }
                                        
                                        $Generated_Header .= '<div class="it__negative_feedback">'.$wmsg.'</div>';
                                    }
                                }
                          else
                                {
                                $Generated_Header .= ':<div class="it__negative_feedback">'.$this->getLang('msg_captchawrong').'</div>';
                                }  
                          }
                    }            
                }
                else
                {$Generated_Report = $this->_report_render($data);}
            }

            // Create issue list            
            elseif (stristr($data['display'],'ISSUES')!= false)
            {   // get issues file contents
                $all = true;
                $issues = $this->_get_issues($data, $all);

                // global sort of issues array
                if($data['sort']='') $data['sort']='id';
                $sort_key = $data['sort'];
                $issues = $this->_issues_globalsort($issues, $sort_key);
                $step = $data['view'];
                $Generated_Table = $this->_table_render($data['project'],$issues,$data,$step,$start); 
                if (strtolower($data['controls'])==='on') {
                    $Generated_Scripts = $this->_scripts_render($project);
                }
            }
            // Count only ...        
            elseif (stristr($data['display'],'COUNT')!= false) 
            {   // get issues file contents
                $all = true;
                $issues = $this->_get_issues($data, $all);
                $a_result = $this->_count_render($issues,$start,$step,$next_start,$data,$project);
                $Generated_Table =$a_result[1];
                $count = $a_result[0];                
            }            
            // syntax to display a single issue inside wiki text
            elseif (stristr($data['display'],'single_issue')!= false)
            {   // retrieve issue details
                $all      = false;
                $issues   = $this->_get_issues($data, $all);
                $issue_id = $data['id'];
                if($issues[$issue_id]['status'] == $this->getLang('issue_resolved_status')) { $a_style="<del>"; $e_style="</del>";}
                // define output
                $Generated_Table  = $a_style."<a href='".DOKU_URL.'doku.php?id='.$ID."&do=showcaselink&showid=".$issue_id."&project=".$data['project']."' title='".$issues[$issue_id]['status']."'>".NL;
                $Generated_Table .= "<span>#".$issue_id.":</span>&nbsp;";
                $Generated_Table .= "<span>".$issues[$issue_id]['title']."</span>&nbsp;";
                $Generated_Table .= "<span>(".$issues[$issue_id]['status'].")</span>";
                $Generated_Table .= "</a>".$e_style;
                
                $Generated_Header  = "";
                $Generated_Scripts = "";
                $Generated_Report  = "";                 
            }
// *****************************************************************************
// Show configuration
// *****************************************************************************            
            elseif (stristr($data['display'],'config')!= false)
            {   /* 1. load the defined elements per type
                   - the related config file (it_matrix.cfg) is stored to the plugin folder
                   - the matrix structure is as follows
                     elmnt_type | elmnt_name | rel_childs
                   - type project has childs of type product
                   - type product has at least one relative parent or is just newly created
                */
/*                $cfg_file = DOKU_PLUGIN."issuetracker/conf/it_matrix.cfg";
                $it_cfg = array();
                if (@file_exists($cfg_file)) { $it_cfg  = unserialize(@file_get_contents($cfg_file)); }    
                echo "<pre style='font:italic 11px/15px Arial, serif;'>".print_r($it_cfg, true)."</pre>";

                // 2. replace the placeholder with config values
                  // a) loop through the matrix and collect all defined projects
                  //    initially we start with projects
                  //    string to be built according: <option value="strtolower($name)" >$name</option>
                  $itm_counter = 0;
                  $itm_counter2 = 0;
                  foreach($it_cfg as $item) {
                    if ($item['elmnt_type']=="project") {
                      $itm_counter++;
                      if($itm_counter===1){
                        $name2  .= '<option value="'.$item['elmnt_name'].'" selected="selected">'.$item['elmnt_name'].'</option>'.NL;
                        $sel_prod = $item['elmnt_name'];
                      }
                      else {
                        $name2  .= '<option value="'.$item['elmnt_name'].'" >'.$item['elmnt_name'].'</option>'.NL;
                      }
                    }
                    
                    // list all potential childs and check the already related items
                    elseif ($item['elmnt_type']=="product") {
                      $itm_counter2++;
                      if (stripos($item['rel_childs'],$sel_prod)!== false) {
                        $cfgelements .= '<input id="childs_'.$itm_counter2.'" name="childs_'.$itm_counter2.'" class="element checkbox" type="checkbox" checked />
                                       <label class="choice" for="childs_'.$itm_counter2.'">'.$item['elmnt_name'].'</label> <br />'.NL;
                      }
                      else {
                        $cfgelements .= '<input id="childs_'.$itm_counter2.'" name="childs_'.$itm_counter2.'" class="element checkbox" type="checkbox" />
                                       <label class="choice" for="childs_'.$itm_counter2.'">'.$item['elmnt_name'].'</label> <br />'.NL;
                      }
                    }
                  }

                  $type1 = '<option value="project" selected="selected">Project</option>
                            <option value="product" >Product</option>
                            <option value="component" >Component</option>'.NL;
*/               
/*                  $name2  = ' <option value="" selected="selected"></option>
                              <option value="1" >First element</option>
                              <option value="2" >Second element</option>
                              <option value="3" >Third element</option>'.NL;
                
                  $cfgelements = '<input id="element_6_1" name="element_6_1" class="element checkbox" type="checkbox" value="1" />
                                 <label class="choice" for="element_6_1">First option</label> <br />
                                  <input id="element_6_2" name="element_6_2" class="element checkbox" type="checkbox" value="1" />
                                  <label class="choice" for="element_6_2">Second option</label> <br />
                                  <input id="element_6_3" name="element_6_3" class="element checkbox" type="checkbox" value="1" />
                                  <label class="choice" for="element_6_3">Third option</label>  <br />'.NL;
*/                  
/*                // 3. load html-skeleton
                  $html_skeleton = DOKU_PLUGIN."issuetracker/cfg_skeleton.html";
                  if (@file_exists($html_skeleton)) { $Generated_config  = @file_get_contents($html_skeleton); }
                  else { msg("The file 'cfg_skeleton.html' was not found at plugin directory.",-1); return;}
                  
                // 4. replace placeholders at html-skeleton
                  $Generated_config = str_ireplace("%%ID%%",$ID,$Generated_config);
                  $Generated_config = str_ireplace("%%type1%%",$type1,$Generated_config);
                  $Generated_config = str_ireplace("%%name2%%",$name2,$Generated_config);
                  $Generated_config = str_ireplace("%%cfgelements%%",$cfgelements,$Generated_config);

                // 5. load the script to assemble the element childs for submit to action.php
                  $it_script = DOKU_PLUGIN."issuetracker/it_matrix.script";
                  if (@file_exists($it_script)) { $it_cfg_script  = @file_get_contents($it_script); }
                  else { msg("The file 'it_matrix.script' was not found at plugin directory.",-1); }
                  $Generated_config = $it_cfg_script .NL. $Generated_config;
                
*/            }

            // Render            
            $renderer->doc .= $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report.$Generated_config;

        }
    }

/******************************************************************************/
/* Create count output
*/
    function _count_render($issues,$start,$step,$next_start,$data, $project)
    {   global $ID;
        $count = array();
        $productfilter=$data['product'];

        foreach ($issues as $issue)
        {
            if(($issue['project'] !== $project) && ($this->getConf('multi_projects')==0)) {
              continue;
            }
            elseif((strcasecmp($productfilter,'ALL')===0) || (stristr($productfilter,$this->_get_one_value($issue,'product'))!= false))
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
            $rendered_count .= '<tr><td><a href="'.DOKU_URL.'doku.php?id='.$ID.'&do=issuelist_filterlink'.'&itl_start='.$start.'&itl_step='.$step.'&itl_next='.$next_start.'&itl_stat_filter='.$value[1].'&itl_sev_filter='.$data['severity'].'&itl__prod_filter='.$data['product'].'&itl_project='.$data['project'].'" >'.$value[1].'</a>&nbsp;</td><td>&nbsp;'.$value[0].'</td></tr>';
        }
        $rendered_count .= '</table></div>';
        $ret_array = array($a_count,$rendered_count);
        return $ret_array;
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
        // search also action.php for 'auth_ad_overflow'
        if($this->getConf('auth_ad_overflow') == false) {
            global $auth;
            global $conf;
            $filter['grps']  = $this->getConf('assign');
            $target          = $auth->retrieveUsers(0,0,$filter); 
            $shw_assignee_as = trim($this->getConf('shw_assignee_as'));
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
                $fileextension = $this->getConf('assgnee_list');
                $x_umail_select = __get_assignees_from_files($fileextension);                	 
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
/* Create list of Issues
*/
    function _table_render($project,$issues,$data,$step,$start)
    {
        global $ID;
        global $lang;
        if ($step==0) $step=10;
        if ($start==0) $start=count($issues)-$step+1;
        $next_start  = $start + $step + 1;
        if ($next_start>count($issues)) $next_start=count($issues);
        
        $imgBASE     = DOKU_BASE."lib/plugins/issuetracker/images/";
        $style       =' style="text-align:center; white-space:pre-wrap;">';
//        $date_style =' style="text-align:center; white-space:pre;">';
        $user_grp    = pageinfo();
        $noStatIMG   = $this->getConf('noStatIMG');
        $noSevIMG    = $this->getConf('noSevIMG');
        $a_project     = $data['project'];

        if(array_key_exists('userinfo', $user_grp))
        {   foreach ($user_grp['userinfo']['grps'] as $ugrp)
            {  $user_grps = $user_grps . $ugrp;  }
        }
        else
        {   $user_grps = 'all';  }
        
        if (strtolower($data['controls'])==='on') {
        $ret = '<br /><br /><form class="issuetracker__form2" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'"><p>';
        $ret .= formSecurityToken(false).'<input type="hidden" name="do" value="show" />';        
        }
        // the user maybe member of different user groups
        // check if one of its assigned groups match with configuration
        $allowed_users = explode('|', $this->getConf('assign'));
        $cFlag = false;
        foreach ($allowed_users as $w) 
        {   // check if one of the assigned user roles does match with current user roles
            if (strpos($user_grps,$w)!== false)
            {   $cFlag = true;
                break;  } 
        }      
                
        // members of defined groups $user_grps allowed to change issue contents 
        if (($cFlag === true) || ($this->getConf('registered_users')== 0))       
        {   $dynatable_id = "t_".uniqid((double)microtime()*1000000,1);
            if(($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) 
                { $th_project = "<th id='project'>".$this->getLang('th_project')."</th>"; }
                
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
            if($data['myissues'] == '') { $data['myissues']= false; }
            else { $data['myissues']= true; }
            
            for ($i=$next_start-1;$i>=0;$i=$i-1)
            {   // check start and end of rows to be displayed            
                if($i>count($issues)) break;  // prevent the php-warning
                $issue = $issues[$i];                    

                $a_status     = strtolower($this->_get_one_value($issue,'status'));
                $a_severity   = strtolower($this->_get_one_value($issue,'severity'));
                $a_product    = strtoupper($this->_get_one_value($issue,'product'));
                $a_version    = strtoupper($this->_get_one_value($issue,'version'));
                $a_component  = strtoupper($this->_get_one_value($issue,'component'));
                $a_tblock     = strtoupper($this->_get_one_value($issue,'tblock'));
                $a_assignee   = strtoupper($this->_get_one_value($issue,'assignee'));
                $a_reporter   = strtoupper($this->_get_one_value($issue,'user_name'));
                                    
                if ((($data['status']    =='ALL') || (stristr($data['status'],$a_status)          != false)) && 
                    (($data['severity']  =='ALL') || (stristr($data['severity'],$a_severity)      != false)) && 
                    (($data['product']   =='ALL') || (stristr($data['product'],$a_product)        != false)) &&
                    (($data['version']   =='ALL') || (stristr($data['version'],$a_version)        != false)) &&
                    (($data['component'] =='ALL') || (stristr($data['component'],$a_component)    != false)) &&
                    (($data['assignee']  =='ALL') || (stristr($data['assignee'],$a_assignee)      != false)) &&
                    (($data['reporter']  =='ALL') || (stristr($data['reporter'],$a_reporter)      != false)) &&
                    (($data['myissues']  == false  ) || ($this->_find_myissues($issue, $user_grp) == true)))
                {   
                    if ($y>=$step) break;
                    if (stripos($this->getConf('status_special'),$a_status) !== false) continue;
                    $y=$y+1;
                    // check if status image or text to be displayed
                    if ($noStatIMG == false) {                    
                        $status_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($a_status))).'.gif';
//                                if(!file_exists(str_replace("//", "/", DOKU_INC.$status_img)))  { $status_img = $imgBASE . 'status.gif' ;}
                        $status_img ='  class="it_center"><span style="display : none;">'.$a_status.'</span><img border="0" alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"/>'.NL;
                    }                    
                    else { $status_img = $style.$a_status; }
                    // check if severity image or text to be displayed                                            
                    if ($noSevIMG == false) {                    
                        $severity_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($a_severity))).'.gif';
//                                if(!file_exists(str_replace("//", "/", DOKU_INC.$severity_img)))  { $severity_img = $imgBASE . 'status.gif' ;}
                        $severity_img ='  class="it_center"><span style="display : none;">'.$a_severity.'</span><img border="0" alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"/>'.NL;
                    }
                    else { $severity_img = $style.$a_severity; }
                    

                    $it_issue_username = $this->_get_one_value($issue,'user_name');
                    $a_project = $this->_get_one_value($issue,'project');
                    if(($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) 
                    {   $td_project = '<td class="itl__td_standard">'.$a_project.'</td>';
                    }
                       
                    // build parameter for $_GET method
                        $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($a_project));
                        $itl_item_title = '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$this->_get_one_value($issue,'title').'">'.$this->_get_one_value($issue,'title').'</a>'.NL;

                                            
                    if((($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) || $project == $a_project  )
                    { if($rowEven==="it_roweven") $rowEven="it_rowodd";
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
            $body .= '</tbody></table></div>'.NL;          
        } 

        else       
        {   //$head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$project."' class=\"sortable resizable inline\"><thead><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Created'>Created</th><th id='Version'>Version</th><th id='User'>User</th><th id='Description'>Description</th><th id='assigned'>assigned</th><th id='Resolution'>Resolution</th><th id='Modified'>Modified</th></tr></thead>";        
            $dynatable_id = "t_".uniqid((double)microtime()*1000000,1);
            //Build table header according settings or syntax
            if(strlen($data['columns'])>0) {
              $configs = explode(',', strtolower($data['columns']));
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
                $issue = $issues[$i];
                $a_project      = strtolower($this->_get_one_value($issue,'project'));
                $a_status     = strtolower($this->_get_one_value($issue,'status'));
                $a_severity   = strtolower($this->_get_one_value($issue,'severity'));
                $a_product    = strtoupper($this->_get_one_value($issue,'product'));
                $a_version    = strtoupper($this->_get_one_value($issue,'version'));
                $a_component  = strtoupper($this->_get_one_value($issue,'component'));
                $a_tblock     = strtoupper($this->_get_one_value($issue,'tblock'));
                $a_assignee   = strtoupper($this->_get_one_value($issue,'assignee'));
                $a_reporter   = strtoupper($this->_get_one_value($issue,'user_name'));
                                    
                if ((($data['status']    =='ALL') || (stristr($data['status'],$a_status)          != false)) && 
                    (($data['severity']  =='ALL') || (stristr($data['severity'],$a_severity)      != false)) && 
                    (($data['product']   =='ALL') || (stristr($data['product'],$a_product)        != false)) &&
                    (($data['version']   =='ALL') || (stristr($data['version'],$a_version)        != false)) &&
                    (($data['component'] =='ALL') || (stristr($data['component'],$a_component)    != false)) &&
                    (($data['assignee']  =='ALL') || (stristr($data['assignee'],$a_assignee)      != false)) &&
                    (($data['reporter']  =='ALL') || (stristr($data['reporter'],$a_reporter)      != false)) &&
                    (($data['myissues']  == false  ) || ($this->_find_myissues($issue, $user_grp) == true)))
               {   
                    
                    if (stripos($this->getConf('status_special'),$a_status) !== false) continue;
                    if((($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) || $project == $a_project  )
                    {   if ($y>=$step) break;
                        $y=$y+1;
    
                        if($rowEven==="it_roweven") $rowEven="it_rowodd";
                        else $rowEven="it_roweven";
    
                        if(($this->getConf('multi_projects')!==0) && ($this->getConf('shw_project_col')!==0)) 
                        {   $a_project = $this->_get_one_value($issue,'project');
                            $td_project = '<td class="itl__td_standard">'.$a_project.'</td>';
                        }
                        else{$td_project="";}

                        $reduced_issues = $reduced_issues.'<tr id = "'.$a_project.' '.$this->_get_one_value($issue,'id').'" class="'.$rowEven.'" >'.NL.
                                                          $td_project.NL.
                                                          '<td'.$style.$this->_get_one_value($issue,'id').'</td>'.NL;
                        foreach ($configs as $config)
                        {
                            $isval = $this->_get_one_value($issue,strtolower($config));
                            if ($config == 'status')
                            {
                                if ($noStatIMG == false) {                    
                                    $status_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($isval))).'.gif';
                                    $reduced_issues .='<td class="it_center"><span style="display : none;">'.$a_status.'</span><img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"/></td>'.NL;
                                }
                                else { $reduced_issues .= '<td'.$style.$isval.'</td>'.NL; }
                            }                                            
                            elseif ($config == 'severity')
                            {
                                if ($noSevIMG == false) {                    
                                    $severity_img = $imgBASE . implode('', explode(' ',$this->img_name_encode($isval))).'.gif';
                                    $reduced_issues .='<td  class="it_center"><span style="display : none;">'.$a_severity.'</span><img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"/></td>'.NL;
                                }
                                else { $reduced_issues .= '<td'.$style.$isval.'</td>'.NL; }
                            }
                            elseif ($config == 'title')
                            {   // build parameter for $_GET method
                                $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($a_project));
                                $reduced_issues .='<td>'.
                                                  '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$isval.'">'.$isval.'</a></td>'.NL;
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
                            {
                                $reduced_issues .= '<td'.$style.$isval.'</td>'.NL;
                            }
                        }
                        $reduced_issues .= '</tr>'.NL;
                    }
                  }
              }
            
            $head = NL.$reduced_header.NL;
            $body = '<tbody>'.$reduced_issues.'</tbody>'.NL.'</table>'.NL.'</div>'.NL;
        }
// -----------------------------------------------------------------------------
// Control render        
        if($data['myissues']==false) {$data['myissues'] = "";}
        else {$data['myissues'] = "checked='true'";}                
        
        if($data['tblock']==false) { $data['tblock'] = ""; }
        else $data['tblock'] = "checked='true'";

        if (strtolower($data['controls'])==='on') {
          $a_result = $this->_count_render($issues,$start,$step,$next_start,$data,$project);
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
               '   <td colspan="4" align="left" valign="middle" height="30">'.NL.
               '       <label class="it__cir_projectlabel">'.sprintf($this->getLang('lbl_issueqty'),$project).$count.'</label>'.NL.
               '   </td>'.NL.
               '   <td class="itl__showdtls" rowspan="2" width="30%">'.$li_count.'</td>'.NL.
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

               $ret .= '   <td align ="left" valign="top" width="25%">'.NL.
               '    <form name="myForm" action="" method="post">'.NL.
               '       <input                          type="hidden" name="itl_start"        id="itl_start"        value="'.$start.'"/>'.NL.
               '       <input                          type="hidden" name="itl_step"         id="itl_step"         value="'.$step.'"/>'.NL.
               '       <input                          type="hidden" name="itl_next"         id="itl_next"         value="'.$next_start.'"/>'.NL.
               '       <input                          type="hidden" name="itl_project"      id="itl_project"      value="'.$project.'"/>'.NL.
               '       <input                          type="hidden" name="it_th_cols"       id="it_th_cols"       value="'.$data['columns'].'"/>'.NL.
               '       <input class="itl__buttons"     type="button" name="showprevious"                           value="'.$this->getLang('btn_previuos').'" title="'.$this->getLang('btn_previuos_title').'" onClick="changeAction(1)"/>'.NL.
               '       <input class="itl__step_input"  type="text"   name="itl_step"         id="itl_step"         value="'.$step.'"/>'.NL.
               '       <input class="itl__buttons"     type="button" name="shownext"                               value="'.$this->getLang('btn_next').'"     title="'.$this->getLang('btn_next_title').'"     onClick="changeAction(2)"/><br />'.NL;
               
                   if(stripos($this->getConf('ltdListFilters'),'Filter Severity')=== false)      $ret .= '<input class="itl__sev_filter"  type="text"   name="itl_sev_filter"   id="itl_sev_filter"   value="'.$data['severity'].'"/><br />'.NL;                         
                   if(stripos($this->getConf('ltdListFilters'),'Filter Status')=== false)        $ret .= '<input class="itl__stat_filter" type="text"   name="itl_stat_filter"  id="itl_stat_filter"  value="'.$data['status'].'"/><br /></p>'.NL;
               $ret .= '</p><p>'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Product')=== false)       $ret .= '<input class="itl__prod_filter" type="text"   name="itl__prod_filter" id="itl__prod_filter" value="'.$data['product'].'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Version')=== false)       $ret .= '<input class="itl__prod_filter" type="text"   name="itl__vers_filter" id="itl__vers_filter" value="'.$data['version'].'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Component')=== false)     $ret .= '<input class="itl__prod_filter" type="text"   name="itl__comp_filter" id="itl__comp_filter" value="'.$data['component'].'"/><br /></p>'.NL;
               $ret .= '</p><p>'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Test blocking')=== false) $ret .= '<input                          type="checkbox" name="itl__block_filter" id="itl__block_filter" '   .$data['tblock'].'/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Assignee')=== false)      $ret .= '<input class="itl__prod_filter" type="text"   name="itl__assi_filter" id="itl__assi_filter" value="'.$data['assignee'].'"/><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Filter Reporter')=== false)      $ret .= '<input class="itl__prod_filter" type="text"   name="itl__user_filter" id="itl__user_filter" value="'.$data['reporter'].'"/><br />'.NL;
               $ret .= '</p><p>'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'MyIssues')=== false)             $ret .= '<input                          type="checkbox" name="itl_myis_filter" id="itl_myis_filter" '       .$data['myissues'].' title="'.$this->getLang('cbx_myissues').'" /><br />'.NL;
                   if(stripos($this->getConf('ltdListFilters'),'Sort by')=== false)              $ret .= '<input class="itl__sev_filter"  type="text"   name="it_glbl_sort"      id="it_glbl_sort"    value="'.$data['sort'].'"/><br /></p>'.NL;
               $ret .= '</p><input class="itl__buttons" type="button" name="go" value="'.$this->getLang('btn_go').'" title="'.$this->getLang('btn_go').'" onClick="changeAction(3)"/><br />'.NL;
               $ret .= '</form>'.NL.                      
               '   </td>'.NL.
               '   <td width="2%">&nbsp;</td>'.NL.
               '   <td class="itl__showdtls" align ="left" width="40%">'.NL.
               '    <form  method="post" action="doku.php?id=' . $ID . '&do=showcase">'.NL.
               '       <label class="it__searchlabel">'.$this->getLang('lbl_showid').'</label>'.NL.
               '       <input class="itl__sev_filter"    type="text"   name="showid"          id="showid"          value="0"/>'.NL.
               '       <input                            type="hidden" name="project"         id="project"         value="'.$project.'"/>'.NL.
               '       <input                            type="hidden" name="itl_sev_filter"  id="itl_sev_filter"  value="'.$data['severity'].'"/>'.NL.
               '       <input                            type="hidden" name="itl_stat_filter" id="itl_stat_filter" value="'.$data['status'].'"/>'.NL.
               '       <input                            type="hidden" name="itl_myis_filter" id="itl_myis_filter" '       .$data['myissues'].' />'.NL.
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
         }


         $usr  = '<span style="display:none;" id="currentuser">'.$user_grp['userinfo']['name'].'</span>' ;   //to log issue mods            
         $usr .= '<span style="display:none;" id="currentID">'.urlencode($ID).'</span>' ; // to log issue mods
         $a_lang  = '<span style="display:none;" name="table_kit_OK" id="table_kit_OK">'.$this->getLang('table_kit_OK').'</span>'; // for tablekit.js
         $a_lang .= '<span style="display:none;" name="table_kit_Cancel" id="table_kit_Cancel">'.$this->getLang('table_kit_Cancel').'</span>'; // for tablekit.js
         $ret  = $a_lang.$usr.$ret.$head.$body;              
         return $ret;
    }
/******************************************************************************/
/* pic-up a single value
*/
    function _get_one_value($issue, $key) {
        if (@array_key_exists($key,$issue))
            return $issue[$key];
        return '';
    }

/******************************************************************************/
/* elaborate the display string of assignee (login, name or mail)
*/
    function _get_assignee($issue, $key) {
        if(!$issue) return;
        if (array_key_exists($key,$issue)) {
            global $auth;
            global $conf;
            $filter['grps']  = $this->getConf('assign');
            $usr_array       = $auth->retrieveUsers(0,0,$filter);
            $shw_assignee_as = trim($this->getConf('shw_assignee_as'));
            if(stripos("login, mail, name",$shw_assignee_as) === false) $shw_assignee_as = "login";
            foreach ($usr_array as $u_key => $usr)
            {     if($usr['mail']==$issue[$key]) 
                  {   if   ($shw_assignee_as=='login') { return $u_key; }
                      elseif($shw_assignee_as=='mail') { return $usr['mail']; }
                      else                             { return $usr['name']; }
                  }
            } 
        }
        if(stripos("mail",$shw_assignee_as) !== false) return $issue[$key];
        else {
          $b_display = explode("@",$issue[$key]);
          return $b_display[0];
        }
    }
/******************************************************************************/
/* send an e-mail to admin due to new issue created
*/
    function _emailForNewIssue($project,$issue,$email_to)
    {
        if ($this->getConf('send_email')==1)
        {   global $ID;
            
            if ($this->getConf('mail_templates')==1) {
              // load user html mail template
              $sFilename = DOKU_PLUGIN.'issuetracker/mailtemplate/new_issue_mail.html';
              $bodyhtml = file_get_contents($sFilename);
            }
            $subject=sprintf($this->getLang('issuenew_subject'),$issue['severity'], $project, $issue['product'],$issue['version']);
            $subject= mb_encode_mimeheader($subject, "UTF-8", "Q" );
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));

            $body = $this->getLang('issuenew_head').chr(10).chr(10).
                    $this->getLang('issuenew_intro').chr(10).
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).
                    $this->getLang('th_assigned').$issue['assigned'].chr(10).chr(10).
                    $this->getLang('issuenew_descr').$this->xs_format($issue['description']).chr(10).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$this->getLang('issuemod_end');

            $body = html_entity_decode($body);
            if ($this->getConf('mail_templates')==1) $bodyhtml = $this->replace_bodyhtml($bodyhtml, $pstring, $project, $issue, $comment);
            $from=$email_to ;
            $to=$from;
            $cc=$issue['add_user_mail'];
            if ($this->getConf('mail_templates')==1) { 
              $headers = "Mime-Version: 1.0 Content-Type: text/plain; charset=ISO-8859-1 Content-Transfer-Encoding: quoted-printable";
              $this->mail_send_html($to, $subject, $body, $bodyhtml, $from, $cc, $bcc='', $headers, $params=null);
            }
            else {
              mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
            }
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
        return mail($to,$subject,$message,$header);
      }else{
        return mail($to,$subject,$message,$header,$params);
      }
    }
/******************************************************************************/
    function replace_bodyhtml($bodyhtml, $pstring, $project, $issue, $comment) {
        global $ID;
        $bodyhtml = str_ireplace("%%_SEE%%",DOKU_URL.'doku.php?id='.$ID.'&do=showcaselink&'.$pstring,$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_head%%",$this->getLang('issuemod_head'),$bodyhtml);
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
            $bodyhtml = str_ireplace("%%CMNT_AUTHOR%%",$comment['author'],$bodyhtml);
            $bodyhtml = str_ireplace("%%CMNT_TIMESTAMP%%",date($this->getConf('d_format'),strtotime($comment['timestamp'])),$bodyhtml);
            $frmt_cmnt = str_ireplace(chr(10),"<br />",$comment['comment']);
            $bodyhtml = str_ireplace("%%COMMENT%%",$this->xs_format($frmt_cmnt),$bodyhtml);
//        }
        $bodyhtml = str_ireplace("%%issuemod_br%%",$this->getLang('issuemod_br'),$bodyhtml);
        $bodyhtml = str_ireplace("%%issuemod_end%%",$this->getLang('issuemod_end'),$bodyhtml);
        
        return $bodyhtml;
    }
/******************************************************************************/
/*  Report an Issue 
*/
    function _report_render($data)
    {
        global $lang;
        global $ID;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $project = $data['project'];
        
        // retrive some basic information
        $user_mail = pageinfo();  //to get mail address of reporter
        $cur_date = date($this->getConf('d_format'));
        $user_check = $this->getConf('registered_users');

        // check if current user is admin/assignee
        // check if syntax parameter asympupl is switched on
        $user_mail    = pageinfo();
        if(array_key_exists('userinfo', $user_mail))
        {   foreach ($user_mail['userinfo']['grps'] as $ugrp)
            {  $user_grps = $user_grps . $ugrp;  }
        }
        else
        {   $user_grps = 'all';  }
        $allowed_users = explode('|', $this->getConf('assign'));
        $cFlag = false;
        foreach ($allowed_users as $w) 
        {   // check if one of the assigned user roles does match with current user roles
            if (strpos($user_grps,$w)!== false)
            {   $cFlag = true;
                break;  } 
        }      

        $_cFlag = false;
        if($user_check == 0) {
            $_cFlag = true;  }             
        elseif (($user_check == 1) && ($user_mail['perm'] > 1)) {
            $_cFlag = true;  }

        if($_cFlag === true) {
            /*--------------------------------------------------------------------*/
            // load set of projects defined by admin
            /*--------------------------------------------------------------------*/
            $STR_PROJECT = "";
            $l_projects = explode(',', $this->getConf('projects')) ;
            $STR_PROJECT = '<option value="" >'.$project.'</option>'.NL;
            foreach ($l_projects as $a_project)
            {
                $a_project = trim($a_project);
                $STR_PROJECT .= '<option value="'.$a_project.'" >'.$a_project."</option>".NL;
            }
            /*--------------------------------------------------------------------*/
            // load set of product names defined by admin
            /*--------------------------------------------------------------------*/
            if($data['prod_limit'] !== "OFF") { 
                $temp2 = explode(',', $this->getConf('products'));
                foreach($temp2 as $a) {
                    if(stripos(strtoupper($data['product']),strtoupper($a))!==false) {
                        if(strlen($temp)<1){
                            $temp .= $a;
                        }
                        else $temp .= ','.$a;   
                    }
                }
                $products = explode(',', $temp);
            }
            else {
                 $products = explode(',', $this->getConf('products'));
            }
            
            $STR_PRODUCTS = "";
            foreach ($products as $_products)
            {
                $x_products = trim($x_products);
                //if product is preselected by syntax
                if(strtoupper ($_products) == strtoupper ($data['product'])) { $option_param = '<option value="'.$_products.'" selected >'; }
                else { $option_param = '<option value="'.$_products.'" >'; }
                
                $STR_PRODUCTS = $STR_PRODUCTS . $option_param .$_products."</option>".NL;
            }
            
            /*--------------------------------------------------------------------*/
            // load set of components defined by admin
            /*--------------------------------------------------------------------*/
            $STR_COMPONENTS = "";
            $components = explode(',', $this->getConf('components')) ;
//            $STR_COMPONENTS = '<option value="" ></option>'.NL;
            foreach ($components as $_component)
            {
                $_component = trim($_component);
                $STR_COMPONENTS .= '<option value="'.$_component.'" >'.$_component."</option>".NL;
            }
            if($STR_COMPONENTS === "") $STR_COMPONENTS = '<option value="" ></option>'.NL;

            /*--------------------------------------------------------------------*/
            // load set of severity values defined by admin
            /*--------------------------------------------------------------------*/
            $STR_SEVERITY = "";
            $severity = explode(',', $this->getConf('severity')) ;
            foreach ($severity as $_severity)
            {
                $_severity = trim($_severity);
                $STR_SEVERITY = $STR_SEVERITY . '<option value="'.$_severity.'" >'.$_severity."</option>".NL;
            }
            
            /*--------------------------------------------------------------------*/
            // create the report template and check input on client site
            /*--------------------------------------------------------------------*/
            if($this->getConf('wysiwyg')==true) {
              $_editor_java ='if ((document.getElementById("a_description").innerHTML.length <= 5) & (document.getElementById("a_description").innerHTML.indexOf(" ") == -1)) {
                          alert ("'.$this->getLang('wmsg3').'");
                          frm.description.focus();
                          return false;}';
              $_editor_java_2 ='function myFunction() {
                                  document.getElementById("description").value = document.getElementById("a_description").innerHTML;
                                }';
              $myFunc = 'onsubmit="myFunction()"';
              
            }
            else {
              $_editor_java ='if ((frm.description.value.length <= 5) & (frm.description.value.indexOf(" ") == -1)) {
                          alert ("'.$this->getLang('wmsg3').'");
                          frm.description.focus();
                          return false;';
                          
              $_editor_java_2 ='';
              $myFunc = 'onsubmit="return chkFormular(this)"';
            }
            
            $ret = '<div class="it__cir_form"><script>
                   // JavaScript Document
                    function chkFormular(frm) {

                        if (frm.product.value == "") {
                          alert("Please select a valid product!");
                          frm.product.focus();
                          return false;
                        }

                        if (frm.version.value == "") {
                          alert("'.$this->getLang('wmsg2').'");
                          frm.version.focus();
                          return false;
                        }

                        if (frm.user_name.value < 3) {
                          alert("Please enter your user name!");
                          frm.user_name.focus();
                          return false;
                        }

                        if (frm.user_mail.value.indexOf("@") == -1) { 
                          alert ("'.$this->getLang('wmsg1').'");
                          frm.user_mail.focus();
                          return false;
                        }

                        if (frm.severity.value == "") {
                          alert ("Please select a severity");
                          frm.severity.focus();
                          return false;
                        }

                        if ((frm.ittitle.value.length <= 5) & (frm.ittitle.value.indexOf(" ") == -1)) {
                          alert ("'.$this->getLang('wmsg5').'");
                          frm.ittitle.focus();
                          return false;
                        }

                        '.NL.$_editor_java.NL.'
                    }
                    '.NL.$_editor_java_2.NL.'
                   }</script>'.NL;
            $ret .= '<form class="issuetracker__form" name="issuetracker__form" method="post" '.$myFunc.' accept-charset="'.$lang['encoding'].'" enctype="multipart/form-data" ><p>'.NL.
            formSecurityToken(false).
            '<input type="hidden" name="do" value="show" />'.NL.
            '<input type="hidden" name="id" value="'.$ID.'" />'.NL.
            '<input type="hidden" name="created" value="'.$cur_date.'"/>'.NL.
//            '<input type="hidden" name="comments" value="'.$comments_file.'"/>'.
            '</p>'.NL.
            '<table class="it_form_table">
              <tr>
                <td>'.$this->getLang('th_project').'</td>'.NL;
                
                   //Check config if hidden
                  if($this->getConf('projects')==""){ 
                        $ret .= '<td><label class="it__cir_projectlabel">'.$project.'</label></td>'.NL; } 
                  else {
                      $ret .='<td><select class="element select small it__cir_select" name="project">'.$STR_PROJECT.'</select></td>'.NL;
                  }
     $ret .= '</tr>'.NL.
             '<tr>
                <td>'.$this->getLang('th_product').'</td>
                <td><select class="element select small it__cir_select" name="product">'.$STR_PRODUCTS.'</select></td>
              </tr>'.NL.
             '<tr>';
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Version')!==false){
                      $STR_VERSIONS = ' ';
                      $ret .= ' <input type="hidden" class="it__cir_input" name="version" value="'.$STR_VERSIONS.'"/>';
                  } 
                  else {
                      $ret .= ' <td>'.$this->getLang('th_version').'</td>
                                <td><input class="it__cir_input" name="version" value="'.$STR_VERSIONS.'"/></td>';
                  }             
        
        $ret .= '</tr><tr>'.NL;
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Components')!==false){
                      $STR_COMPONENTS = ' ';
                      $ret .= '<input type="hidden" class="it__cir_input" name="component" value="'.$STR_COMPONENTS.'"/>';
                  } 
                  else {
                      $ret .='<td>'.$this->getLang('th_components').'</td>
                              <td><select class="element select small it__cir_select" name="component">'.$STR_COMPONENTS.'</select></td>'.NL;
                  }
        $ret .= '</tr><tr>'.NL;
                
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Test blocking')!==false){
                      $ret .= '<input type="hidden" name="tblock" value="false">';
                  } 
                  else {
                      $ret .='<td>'.$this->getLang('th_tblock').'</td>
                              <td><input class="it__cir_cbx" type="checkbox" name="tblock"></td>'.NL;
                  }
        
        $ret .= '</tr><tr><td colspan=2>&nbsp;</td></tr>'.NL.
             '<tr>
                <td>'.$this->getLang('th_user_name').'</td>
                <td><input class="it__cir_input" name="user_name" value="'.$user_mail['userinfo']['name'].'"/></td>
              </tr>'.NL.
             '<tr>
                <td>'.$this->getLang('th_usermail').'</td>
                <td><input class="it__cir_input" name="user_mail" value="'.$user_mail['userinfo']['mail'].'"/></td>
              </tr>'.NL.
             '<tr>';
                  //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'User phone')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_input" name="user_phone" value="'.$user_phone['userinfo']['phone'].'"/>';
                  } 
                  else {
                      $ret .= ' <td>'.$this->getLang('th_userphone').'</td>
                                <td><input class="it__cir_input" name="user_phone" value="'.$user_phone['userinfo']['phone'].'"/></td>';
                  }             
              $ret .= '</tr>'.NL.
             '<tr>';
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Add contact')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_input" name="add_user_mail" value="'.$_REQUEST['add_user_mail'].'"/>';
                  } 
                  else {
                      $ret .= ' <td>'.$this->getLang('th_reporteradcontact').'</td>
                                <td><input class="it__cir_input" name="add_user_mail" value="'.$_REQUEST['add_user_mail'].'"/></td>';
                  }             
        $ret .= '</tr>'.NL.
            '<tr><td colspan=2>&nbsp;</td></tr>'.NL.
            '<tr>';
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Severity')!==false){
                      $severity = explode(',', $this->getConf('severity')) ;
                      $STR_SEVERITY = $severity[0]; 
                      $ret .= ' <input type="hidden" class="it__cir_input" name="severity" value="'.$STR_SEVERITY.'"/>';
                  } 
                  else {
                      $ret .= ' <td>'.$this->getLang('th_severity').'</td>
                                <td><select class="element select small it__cir_select" name="severity">'.$STR_SEVERITY.'</select></td>';
                  }             
        $ret .= '</tr>'.NL.
            '<tr>
                <td>'.$this->getLang('th_title').'</td>
                <td><input class="it__cir_linput" name="title" value="'.$_REQUEST['title'].'"/></td>
             </tr>'.NL.
            '<tr>
                <td>'.$this->getLang('th_description').'</td>
                <td>'.NL;

// mod for editor ---------------------------------------------------------------------
// check the config setting what editor to be used
        if($this->getConf('wysiwyg')==true)
        { 
                  //hack if DOKU_BASE is not properly set
                  if(strlen(DOKU_BASE) < strlen(DOKU_URL)) $sFilename = DOKU_URL."lib/plugins/issuetracker/wysiwyg_editor.js";
                  else $sFilename = DOKU_BASE."lib/plugins/issuetracker/wysiwyg_editor.js";
                  $ret      .= '<script type="text/javascript" src="'.$sFilename.'"></script>';
                  $sFilename = DOKU_PLUGIN.'issuetracker/wysiwyg_editor.html';
                  $ret      .= file_get_contents($sFilename);
                  $ret       = str_ireplace("%%DOKU_BASE%%",DOKU_BASE,$ret);
                  $ret .= '<span class="reply_close_link">
                             <a href="javascript:resizeBoxId(\'description\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                             <a href="javascript:resizeBoxId(\'description\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                           </span>'.NL;
                  $ret .= '<textarea id="description" name="description" style="display: none;">'.$_REQUEST['description'].'</textarea>';                  
                  
                  
        }
        else
        {
                  if(strlen(DOKU_BASE) < strlen(DOKU_URL)) $sFilename = DOKU_URL."lib/plugins/issuetracker/xsEditor.js";
                  else $sFilename = DOKU_BASE."lib/plugins/issuetracker/xsEditor.js";
                  $ret      .= '<script type="text/javascript" src="'.$sFilename.'"></script>';
                  $ret .= file_get_contents($sFilename);
                  global $_FILES;
                  $ret .= $this->it_edit_toolbar('description');
                  $ret .= '<textarea class="it__cir_linput" id="description" name="description" cols="109" rows="7">'.$_REQUEST['description'].'</textarea></td></tr>
                           <span class="reply_close_link">
                              <a href="javascript:resizeBoxId(\'description\', -20)"><img src="'.$imgBASE.'reduce.png" title="reduce textarea" style="float:right;" /></a>
                              <a href="javascript:resizeBoxId(\'description\', +20)"><img src="'.$imgBASE.'enlarge.png" title="enlarge textarea" style="float:right;" /></a>
                           </span>'.NL;
        }                      
// mod for editor ---------------------------------------------------------------------

          $ret .= '<tr><td colspan=2>&nbsp;</td></tr>';
                  // check if symptom file upload is allowed
                  if(($this->getConf('upload')>0) || (($cFlag === true) && ($data['asympupl']=true))) {
                      //Check config if hidden
                      if(($cFlag === true) && ($data['asympupl']=true)) {
                          $ret .= '<tr><td>'.$this->getLang('th_sympt').'1</td>
                                       <td><input type="file" class="it__cir_linput" name="attachment1" value="'.$_REQUEST['attachment1'].'"/></td></tr>';
                      }                                   
                      elseif(strpos($this->getConf('ltdReport'),'Symptom link 1')!==false){
                          $ret .= ' <input type="hidden" class="it__cir_linput" name="attachment1" value="'.$_REQUEST['attachment1'].'"/>';
                      } 
                      else {
                          $ret .= '<tr><td>'.$this->getLang('th_sympt').'1</td>
                                       <td><input type="file" class="it__cir_linput" name="attachment1" value="'.$_REQUEST['attachment1'].'"/></td></tr>';
                      }             
                      if(strpos($this->getConf('ltdReport'),'Symptom link 2')!==false){
                          $ret .= ' <input type="hidden" class="it__cir_linput" name="attachment2" value="'.$_REQUEST['attachment2'].'"/>';
                      } 
                      else {
                          $ret .= '<tr><td>'.$this->getLang('th_sympt').'2</td>
                                       <td><input type="file" class="it__cir_linput" name="attachment2" value="'.$_REQUEST['attachment2'].'"/></td></tr>';
                      }             
                      if(strpos($this->getConf('ltdReport'),'Symptom link 3')!==false){
                          $ret .= ' <input type="hidden" class="it__cir_linput" name="attachment3" value="'.$_REQUEST['attachment3'].'"/>';
                      } 
                      else {
                          $ret .= '<tr><td>'.$this->getLang('th_sympt').'3</td>
                                       <td><input type="file" class="it__cir_linput" name="attachment3" value="'.$_REQUEST['attachment3'].'"/></td></tr>';
                      }
                  }             
        $ret .= '</table><p><input type="hidden" name="modified" value="'.$cur_date.'"/>'.NL.
                '<input type="hidden" name="assigned" value="" />'.NL;
    
            if ($this->getConf('use_captcha')==1) 
            {        
                $helper = null;
      		      if(@is_dir(DOKU_PLUGIN.'captcha'))
      			       $helper = plugin_load('helper','captcha');
      			       
      		      if(!is_null($helper) && $helper->isEnabled())
      			    {
      			       $ret .= '<p>'.$helper->getHTML().'</p>';
      			    }
            }
         //<input name="do[save]" type="submit" value="Save" class="button" id="edbtn__save" accesskey="s" tabindex="4" title="Save [S]" />
            $ret .= '</p><p><input name="submit" type="submit" value="'.$this->getLang('btn_reportsave').'" class="button" id="edbtn__save" title="'.$this->getLang('btn_reportsave').'"/>'.
            '</p></form></div>'.NL;
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
           $ret .= '<div class="it__standard_feedback">'.$this->getLang('wmsg4').'</div>';                      
        }
        
        return $ret;    
    }
/******************************************************************************/
/* Display positive/negative message box according user input on submit->Report
*/
    function _show_message($string){
        return "<script type='text/javascript'>
            alert('$string');
        </script>";
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
/**  http://www.linuxjournal.com/article/9585?page=0,0
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
    function validEmail($email)
    {
       $isValid = true;
       $atIndex = strrpos($email, "@");
       if (is_bool($atIndex) && !$atIndex)
       {
          $isValid = false;
       }
       else
       {
          $domain = substr($email, $atIndex+1);
          $local = substr($email, 0, $atIndex);
          $localLen = strlen($local);
          $domainLen = strlen($domain);
          if ($localLen < 1 || $localLen > 64)
          {
             // local part length exceeded
             $isValid = false;
          }
          else if ($domainLen < 1 || $domainLen > 255)
          {
             // domain part length exceeded
             $isValid = false;
          }
          else if ($local[0] == '.' || $local[$localLen-1] == '.')
          {
             // local part starts or ends with '.'
             $isValid = false;
          }
          else if (preg_match('/\\.\\./', $local))
          {
             // local part has two consecutive dots
             $isValid = false;
          }
          else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
          {
             // character not valid in domain part
             $isValid = false;
          }
          else if (preg_match('/\\.\\./', $domain))
          {
             // domain part has two consecutive dots
             $isValid = false;
          }
          else if
    (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                     str_replace("\\\\","",$local)))
          {
             // character not valid in local part unless local part is quoted
             if (!preg_match('/^"(\\\\"|[^"])+"$/',
                 str_replace("\\\\","",$local)))
             {
                $isValid = false;
             }
          }
          
          if((!function_exists('checkdnsrr')) && ($this->getConf('validate_mail_addr')==true))
          {
              function checkdnsrr($host, $type='')
              {
                if(!empty($host))
                {
                    $type = (empty($type)) ? 'MX' :  $type;
                    exec('nslookup -type='.$type.' '.escapeshellcmd($host), $result);
                    $it = new ArrayIterator($result);
                    foreach(new RegexIterator($it, '~^'.$host.'~', RegexIterator::GET_MATCH) as $result)
                    {
                         if($result) {  return true;  }                
                    }
                 }
                 return false;
              }
           }
          else if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) && ($this->getConf('validate_mail_addr')==true))
          {
             // domain not found in DNS
             $isValid = false;
          }
       }
       return $isValid;
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
    function it_edit_toolbar($type) {
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $it_edit_tb  = '<div class="itr_edittoolbar">'.NL;
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
/* log issue modificaions
 * who changed what and when per issue
*/                                          
    function _log_mods($project, $issue, $usr, $column, $new_value)
    {     global $conf;
          // get mod-log file contents
          if($this->getConf('it_data')==false) $modfile = DOKU_CONF."../data/meta/".$project.'_'.$issue_id.'.mod-log';
          else $modfile = DOKU_CONF."../". $this->getConf('it_data').$project.'_'.$issue_id.'.mod-log';
          if (@file_exists($modfile))
              {$mods  = unserialize(@file_get_contents($modfile));}
          else 
              {$mods = array();}
          
          $mod_id = count($mods);
          
          $mods[$mod_id]['timestamp'] = date ($this->getConf('d_format'));
          $mods[$mod_id]['user'] = $usr;
          $mods[$mod_id]['field'] = $column;
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
      $umlaute = $this->getLang('umlaute');
      $replace = $this->getLang('conv_umlaute');
      if((count($umlaute)>1) && (count($replace)>1)) $f_name = preg_replace($umlaute, $replace, $f_name);
      $f_name = strtolower($f_name);
      return $f_name;
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
                  // check upload remaining attempts (to be larger than 0)
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
          $issues[$issue_id]['attachment1'] = DOKU_URL.$target_path;
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
 * Check for MyIssues
 *  
 * Check if the issue is related to the current user
 * the user maybe the issue reporter, assignee or registered as follower      
 * it will return true/false
 *  
 * @author   Taggic <taggic@t-online.de>
 * @param    array $issue the single issue
 * @param    array $user the current user info  
 * @return   bool       true if foo in bar
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
/* 
 * Load all issues into an array
 *  
 * Check if multi_project is set to true
 * Load current $project or all projects of it_data_store     
 * Return the issues array
 *  
 * @author   Taggic <taggic@t-online.de>
 * @param    array $data  
 * @return   $issues
 *
 */
 
  function _get_issues($data, $all = false) {
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
        if($this->getConf('it_data')==false) $pfile = $it_datastore.$data['project'].'.issues';
        else $pfile = $it_datastore.$data['project'].'.issues';
    
        if (@file_exists($pfile))
        	{$issues  = unserialize(@file_get_contents($pfile));}
        else
        	{$issues = array();}
        }

    return $issues;
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
}
