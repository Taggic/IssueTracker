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
    function handle($match, $state, $pos, &$handler){
        $match = substr($match,15,-2); //strip markup from start and end
        //handle params
        $data = array();
        $params = explode('|',$match);
        
        //Default Value
        $data['display']  = 'ISSUES';
        $data['product']  = 'ALL';
        $data['status']   = 'ALL';
        $data['severity'] = 'ALL';
        $data['view']     = '10';
        $data['controls'] = 'ON';
        
        foreach($params as $param){            
            $splitparam = explode('=',$param);
            if ($splitparam[1] != '')
                {
                if ($splitparam[0]=='project')
                	{$data['project'] = $splitparam[1];
                    /*continue;*/}

                if ($splitparam[0]=='product')   
                	{$data['product'] = strtoupper($splitparam[1]);
                	 if ($data['product'] == '') {$data['product'] = 'ALL';}
                    /*continue;*/}

                if ($splitparam[0]=='status')   
                	{$data['status'] = strtoupper($splitparam[1]);
                	 if ($data['status'] == '') {$data['status'] = 'ALL';}
                    /*continue;*/}
                    
                if ($splitparam[0]=='severity')   
                	{$data['severity'] = strtoupper($splitparam[1]);
                	 if ($data['severity'] == '') {$data['severity'] = 'ALL';}
                    /*continue;*/}
                    
                if ($splitparam[0]=='display')
                	{$data['display'] = strtoupper($splitparam[1]);
                	 if ($data['display'] == '') {$data['display'] = 'ISSUES';}
                   /*continue;*/}  
                                                    
                if ($splitparam[0]=='view')
                	{$data['view'] = strtoupper($splitparam[1]);
                	 if ($data['view'] == '') {$data['view'] = '10';}
                   /*continue;*/}
                   
                 if ($splitparam[0]=='controls')
                	{$data['controls'] = strtoupper($splitparam[1]);
                	 if ($data['controls'] == '') {$data['controls'] = 'ON';}
                   /*continue;*/}                                   
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
    function render($mode, &$renderer, $data) {        
        global $ID;
        $project = $data['project'];           
        if ($mode == 'xhtml'){
            
            $renderer->info['cache'] = false;     
               
            // get issues file contents
            $pfile = metaFN($data['project'], '.issues'); 

            if (@file_exists($pfile))
            	{$issues  = unserialize(@file_get_contents($pfile));}
            else
            	{$issues = array();}            	          

            $Generated_Header = '';
            $Generated_Table = '';
            $Generated_Scripts = '';
            $Generated_Report = '';
            


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
                                {                                
                                    //Add it to the issue file
                                    $issue_id=count($issues);      
                                    foreach ($issues as $value)
                                        {if ($value['id'] >= $issue_id) {$issue_id=$value['id'] + 1;}}
                                    
                                    $issues[$issue_id]['id'] = $issue_id;    
                                    $issues[$issue_id]['product'] = htmlspecialchars(stripslashes($_REQUEST['product']));
                                    $issues[$issue_id]['version'] = htmlspecialchars(stripslashes($_REQUEST['version']));
                                    $issues[$issue_id]['severity'] = htmlspecialchars(stripslashes($_REQUEST['severity']));
                                    $issues[$issue_id]['created'] = htmlspecialchars(stripslashes($_REQUEST['created']));
                                    $status = explode(',', $this->getConf('status')) ;
                                    $issues[$issue_id]['status'] = $status[0];
                                    $issues[$issue_id]['user_name'] = htmlspecialchars(stripslashes($_REQUEST['user_name']));
                                    $issues[$issue_id]['user_mail'] = trim(htmlspecialchars(stripslashes($_REQUEST['user_mail'])));
                                    $issues[$issue_id]['user_phone'] = htmlspecialchars(stripslashes($_REQUEST['user_phone']));
                                    $issues[$issue_id]['add_user_mail'] = htmlspecialchars(stripslashes($_REQUEST['add_user_mail']));
                                    $issues[$issue_id]['title'] = htmlspecialchars(stripslashes($_REQUEST['title']));
                                    $issues[$issue_id]['description'] = htmlspecialchars(stripslashes($_REQUEST['description']));
                                    $issues[$issue_id]['attachment1'] = htmlspecialchars(stripslashes($_REQUEST['attachment1']));
                                    $issues[$issue_id]['attachment2'] = htmlspecialchars(stripslashes($_REQUEST['attachment2']));
                                    $issues[$issue_id]['attachment3'] = htmlspecialchars(stripslashes($_REQUEST['attachment3']));
                                    $issues[$issue_id]['assigned'] = '';
                                    $issues[$issue_id]['resolution'] = '';
                                    $issues[$issue_id]['comments'] = '';
                                    $issues[$issue_id]['modified'] = htmlspecialchars(stripslashes($_REQUEST['modified']));
    
                                    $xuser = $issues[$issue_id]['user_mail'];
                                    $xdescription = $issues[$issue_id]['description'];
                                    //check user mail address, necessary for further clarification of the issue
                                    $valid_umail = $this->validEmail($xuser);
                                    if ( ($valid_umail == true) && ((stripos($xdescription, " ") > 0) || (strlen($xdescription)>5)) && (strlen($issues[$issue_id]['version']) >0))
                                    {                                
                                        //save issue-file
                                          $xvalue = io_saveFile($pfile,serialize($issues));
                                          $this->_log_mods($data['project'], $issues[$issue_id], $issues[$issue_id]['user_name'], 'status', $issues[$issue_id]['status']);
//                                        echo "\$xvalue = ".$xvalue;
                                          $Generated_Header = '<div class="it__positive_feedback">'.$this->getLang('msg_reporttrue').$issue_id.'</div>';
                                          $this->_emailForNewIssue($data['project'],$issues[$issue_id]);
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
                                        
                                        $Generated_Header = '<div class="it__negative_feedback">'.$wmsg.'</div>';
                                    }
                                
                                }
                          else
                                {
                                $Generated_Header = ':<div class="it__negative_feedback">'.$this->getLang('msg_captchawrong').'</div>';
                                }  
                          }
                    }            
                }
                else
                {$Generated_Report = $this->_report_render($data);}
            }
            // Create issue list            
            elseif (stristr($data['display'],'ISSUES')!= false)
            {   $step = $data['view'];
                $Generated_Table = $this->_table_render($issues,$data,$step,$start); 
                if (strtolower($data['controls'])==='on') {
                    $Generated_Scripts = $this->_scripts_render();
                }
            }
            // Count only ...        
            elseif (stristr($data['display'],'COUNT')!= false) 
            {
                $Generated_Table = $this->_count_render($issues,$start,$step,$next_start,$data);                
            }            

            // Render            
            $renderer->doc .= $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report;
        }
    }

/******************************************************************************/
/* Create count output
*/
    function _count_render($issues,$start,$step,$next_start,$data)
    {   global $ID;
        $count = array();
        $productfilter=$data['product'];
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
            $rendered_count .= '<tr><td><a href="'.DOKU_URL.'doku.php?id='.$ID.'&do=issuelist_filterlink'.'&itl_start='.$start.'&itl_step='.$step.'&itl_next='.$next_start.'&itl_stat_filter='.$value[1].'&itl_sev_filter='.$data['severity'].'&itl_prod_filter='.$data['product'].'&itl_project='.$data['project'].'" >'.$value[1].'</a>&nbsp;</td><td>&nbsp;'.$value[0].'</td></tr>';
        }
        $rendered_count .= '</table></div>';
        return $rendered_count;
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
        $x_umail_select .= "['',''],";      
        
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
/* Create list of Issues
*/
    function _table_render($issues,$data,$step,$start)
    {
        global $ID;
        global $lang;
        if ($step==0) $step=10;
        if ($start==0) $start=count($issues)-$step+1;
        $next_start = $start + $step + 1;
        if ($next_start>count($issues)) $next_start=count($issues);

        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $style =' style="text-align:left; white-space:pre-wrap;">';
//        $date_style =' style="text-align:center; white-space:pre;">';
        $user_grp = pageinfo();
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
        $project = $data['project'];
        $prod_filter = $data['product'];
        $stat_filter = $data['status'];
        $sev_filter = $data['severity'];
                
        if(array_key_exists('userinfo', $user_grp))
        {
            foreach ($user_grp['userinfo']['grps'] as $ugrp)
            {
                $user_grps = $user_grps . $ugrp;
            }
        }
        else
        {   $user_grps = 'all';  }
        
        if (strtolower($data['controls'])==='on') {
        $ret = '<br /><br /><script type="text/javascript" src="include/selectupdate.js"></script>'.
               '<form class="issuetracker__form2" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'"><p>';
        $ret .= formSecurityToken(false).'<input type="hidden" name="do" value="show" />';        
        }
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
        
        // members of defined groups $user_grps allowed to change issue contents 
        if ($cFlag === true)       
        {   
            $dynatable_id = "t_".uniqid((double)microtime()*1000000,1);
            $head = "<div class='itl__table'><table id='".$dynatable_id."' class='sortable editable resizable inline' width='100%'>".NL.
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
                    
                if ((($data['status']=='ALL') || (stristr($data['status'],$a_status)!= false)) && (($data['severity']=='ALL') || (stristr($data['severity'],$a_severity)!= false)) && (($data['product']=='ALL') || (stristr($data['product'],$a_product)!= false)))
                {   
                    if ($y>=$step) break;
                    if (stripos($this->getConf('status_special'),$a_status) !== false) continue;
                    $y=$y+1;
                    // check if status image or text to be displayed
                    if ($noStatIMG === false) {                    
                        $status_img = $imgBASE . implode('', explode(' ',strtolower($a_status))).'.gif';
//                                if(!file_exists(str_replace("//", "/", DOKU_INC.$status_img)))  { $status_img = $imgBASE . 'status.gif' ;}
                        $status_img =' align="center"> <img border="0" alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"/></td>'.NL;
                    }                    
                    else { $status_img = $style.$a_status; }
                    // check if severity image or text to be displayed                                            
                    if ($noSevIMG === false) {                    
                        $severity_img = $imgBASE . implode('', explode(' ',strtolower($a_severity))).'.gif';
//                                if(!file_exists(str_replace("//", "/", DOKU_INC.$severity_img)))  { $severity_img = $imgBASE . 'status.gif' ;}
                        $severity_img =' align="center"> <img border="0" alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"/></td>'.NL;
                    }
                    else { $severity_img = $style.$a_severity; }
                    
                    // build parameter for $_GET method
                        $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($project));
                        $itl_item_title = '<a href="doku.php?id='.$ID.'&do=showcaselink&'.$pstring.'" title="'.$this->_get_one_value($issue,'title').'">'.$this->_get_one_value($issue,'title').'</a></td>'.NL;
                    
                    
                    $body .= '<tr id = "'.$project.' '.$this->_get_one_value($issue,'id').'" onMouseover="this.bgColor=\'#DDDDDD\'" onMouseout="this.bgColor=\'#FFFFFF\'">'.NL.                       
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'id').'</td>'.NL.
                             '<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'created'))).'</td>'.NL.
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'product').'</td>'.NL.
                             '<td class="itl__td_standard">'.$this->_get_one_value($issue,'version').'</td>'.NL.
                             '<td'.$severity_img.'</td>'.NL.
                             '<td'.$status_img.'</td>'.NL.
                             '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'user_mail').'">'.$this->_get_one_value($issue,'user_name').'</a></td>'.NL. 
                             '<td class="canbreak itl__td_standard">'.$itl_item_title.'</td>'.NL.
                             '<td class="canbreak itl__td_standard"><a href="mailto:'.$this->_get_one_value($issue,'assigned').'">'.$this->_get_one_value($issue,'assigned').'</a></td>'.NL. 
                             '<td class="canbreak itl__td_standard">'.$this->xs_format($this->_get_one_value($issue,'resolution')).'</td>'.NL.
                             '<td class="itl__td_date">'.date($this->getConf('d_format'),strtotime($this->_get_one_value($issue,'modified'))).'</td>'.NL.
                             '</tr>'.NL;        
                }
            } 
            $body .= '</tbody></table></div>'.NL;          
        } 

        else       
        {   
            //$head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$project."' class=\"sortable resizable inline\"><thead><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Created'>Created</th><th id='Version'>Version</th><th id='User'>User</th><th id='Description'>Description</th><th id='assigned'>assigned</th><th id='Resolution'>Resolution</th><th id='Modified'>Modified</th></tr></thead>";        
            $dynatable_id = "t_".uniqid((double)microtime()*1000000,1);
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
                    $a_product = strtoupper($this->_get_one_value($issue,'product'));
                    
                if ((($data['status']=='ALL') || (stristr($data['status'],$a_status)!= false)) && (($data['severity']=='ALL') || (stristr($data['severity'],$a_severity)!= false)) && (($data['product']=='ALL') || (stristr($data['product'],$a_product)!= false)))
                {   
                    if ($y>=$step) break;
                    if (stripos($this->getConf('status_special'),$a_status) !== false) continue;
                    $y=$y+1;

                    $reduced_issues = $reduced_issues.'<tr id = "'.$project.' '.$this->_get_one_value($issue,'id').'" onMouseover="this.bgColor=\'#DDDDDD\'" onMouseout="this.bgColor=\'#FFFFFF\'">'.NL.
                                                      '<td'.$style.$this->_get_one_value($issue,'id').'</td>'.NL;
                    foreach ($configs as $config)
                    {
                        $isval = $this->_get_one_value($issue,strtolower($config));
                        if ($config == 'status')
                        {
                            if ($noStatIMG === false) {                    
                                $status_img = $imgBASE . implode('', explode(' ',strtolower($isval))).'.gif';
                                $reduced_issues .='<td align="center"> <img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$status_img.'" width="16" height="16"/></td>'.NL;
                            }
                            else { $reduced_issues .= '<td'.$style.$isval.'</td>'.NL; }
                        }                                            
                        elseif ($config == 'severity')
                        {
                            if ($noSevIMG === false) {                    
                                $severity_img = $imgBASE . implode('', explode(' ',strtolower($isval))).'.gif';
                                $reduced_issues .='<td align="center"> <img border="0" alt="'.$isval.'" title="'.$isval.'" style="margin-right:0.5em" vspace="1" align="middle" src="'.$severity_img.'" width="16" height="16"/></td>'.NL;
                            }
                            else { $reduced_issues .= '<td'.$style.$isval.'</td>'.NL; }
                        }
                        elseif ($config == 'title')
                        {   // build parameter for $_GET method
                            $pstring = sprintf("showid=%s&amp;project=%s", urlencode($this->_get_one_value($issue,'id')), urlencode($project));
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
            
            $head = NL.$reduced_header.NL;
            $body = '<tbody>'.$reduced_issues.'</tbody>'.NL.'</table>'.NL.'</div>'.NL;
        }


        if (strtolower($data['controls'])==='on') {
          $li_count = $this->_count_render($issues,$start,$step,$next_start,$data);
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
                  '<td colspan="4" align="left" valign="middle" height="30">'.NL.
                      '<label class="it__cir_projectlabel">'.sprintf($this->getLang('lbl_issueqty'),$project).count($issues).'</label>'.NL.
                  '</td>'.NL.
                  '<td class="itl__showdtls" rowspan="2" width="30%">'.$li_count.'</td>'.NL.
               '</tr>'.NL.

               '<tr class="itd__tables_tr">'.NL.
               '   <td align ="left" valign="top" width="15%">'.NL.
               '     <p class="it__cir_projectlabel">'.$this->getLang('lbl_scroll').' <br />'.NL.
                                                      $this->getLang('lbl_filtersev').' <br />'.NL.
                                                      $this->getLang('lbl_filterstat').' </p>'.NL.
               '   </td>'.NL.
               '   <td align ="left" valign="top" width="25%">'.NL.
               '    <form name="myForm" action="" method="post">'.NL.
               '       <input type="hidden" name="itl_start" id="itl_start" value="'.$start.'"/>'.NL.
               '       <input type="hidden" name="itl_step" id="itl_step" value="'.$step.'"/>'.NL.
               '       <input type="hidden" name="itl_next" id="itl_next" value="'.$next_start.'"/>'.NL.
               '       <input type="hidden" name="itl_project" id="itl_project" value="'.$project.'"/>'.NL.
               '       <input type="hidden" class="itl__prod_filter" name="itl__prod_filter" id="itl__prod_filter" value="'.$data['product'].'"/>'.NL.
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
               '       <input class="itl__sev_filter" name="it_str_search" id="it_str_search" type="text" value="'.$search.'"/>'.NL.
               '       <input type="hidden" name="project" id="project" value="'.$project.'"/>'.NL.
               '       <input class="itl__search_button" id="searchcase" type="submit" name="searchcase" value="'.$this->getLang('btn_search').'" title="'.$this->getLang('btn_search_title').'"/>'.NL.
               '    </form>'.NL.
               '   </td>'.NL.
               '</tr>'.NL.'</tbody>'.NL.'</table>'.NL.'</div>'.NL;
         }

         $usr = '<span style="display:none;" id="currentuser">'.$user_grp['userinfo']['name'].'</span>' ;   //to log issue mods            
         $a_lang  = '<span style="display:none;" name="table_kit_OK" id="table_kit_OK">'.$this->getLang('table_kit_OK').'</span>'; // for tablekit.js
         $a_lang .= '<span style="display:none;" name="table_kit_Cancel" id="table_kit_Cancel">'.$this->getLang('table_kit_Cancel').'</span>'; // for tablekit.js
         $ret  = $a_lang.$usr.$ret.$head.$body;              
         return $ret;
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
/* send an e-mail to admin due to new issue created
*/
    function _emailForNewIssue($project,$issue)
    {
        if ($this->getConf('send_email')==1)
        {
            $subject=sprintf($this->getLang('issuenew_subject'),$issue['severity'], $project, $issue['product'],$issue['version']);
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;

            $body = $this->getLang('issuenew_head').chr(10).chr(10).
            $this->getLang('issuenew_intro').chr(10).
            $this->getLang('issuemod_issueid').$issue['id'].chr(10).
            $this->getLang('issuemod_product').$issue['product'].chr(10).
            $this->getLang('issuemod_version').$issue['version'].chr(10).
            $this->getLang('issuemod_severity').$issue['severity'].chr(10).
            $this->getLang('issuemod_creator').$issue['user_name'].chr(10).chr(10).
            $this->getLang('issuemod_title').$issue['title'].chr(10).
            $this->getLang('issuenew_descr').$this->xs_format($issue['description']).chr(10).chr(10).
            $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
            $this->getLang('issuemod_br').chr(10).$this->getLang('issuemod_end');

            $from=$this->getConf('email_address') ;
            $to=$from;
            mail_send($to, $subject, $body, $from, $cc='', $bcc='', $headers=null, $params=null);
        }     
    }

/******************************************************************************/
/* send an e-mail to user due to issue resolution
*/                            
    function _emailForResolution($project,$issue)
    {  if ($this->getConf('userinfo_email')==1)
        {
            $subject = sprintf($this->getLang('issue_resolved_subject'),$issue['id'], $project);            
            $pstring = sprintf("showid=%s&project=%s", urlencode($issue['id']), urlencode($project));
            global $ID;
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issue_resolved_intro').chr(10).chr(13).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).chr(10).
                    $this->getLang('issue_resolved_text').$this->xs_format($issue['resolution']).chr(10).chr(10).
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
            
            $body = $this->getLang('issuemod_head').chr(10).chr(10).
                    $this->getLang('issuemod_intro').chr(10).chr(13).
                    $this->getLang('issuemod_issueid').$issue['id'].chr(10).
                    $this->getLang('issuemod_status').$issue['status'].chr(10).
                    $this->getLang('issuemod_product').$issue['product'].chr(10).
                    $this->getLang('issuemod_version').$issue['version'].chr(10).
                    $this->getLang('issuemod_severity').$issue['severity'].chr(10).
                    $this->getLang('issuemod_creator').$issue['user_name'].chr(10).chr(10).
                    $this->getLang('issuemod_title').$issue['title'].chr(10).
                    $this->getLang('issuemod_cmntauthor').$comment['author'].chr(10).
                    $this->getLang('issuemod_date').$comment['timestamp'].chr(10).chr(10).
                    $this->getLang('issuemod_cmnt').$this->xs_format($comment['comment']).chr(10).chr(10).
                    $this->getLang('issuemod_see').DOKU_URL.'doku.php?&do=showcaselink&'.$pstring.chr(10).chr(10).
                    $this->getLang('issuemod_br').chr(10).$project.$this->getLang('issuemod_end');

            $from=$this->getConf('email_address') ;
            $to=$issue['user_mail'];
            $cc=$issue['add_user_mail'];
            mail_send($to, $subject, $body, $from, $cc, $bcc='', $headers=null, $params=null);
        }
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
        $cur_date = date ('Y-m-d G:i:s');
        $user_check = $this->getConf('registered_users');

        $_cFlag = false;             
        if($user_check == false) {
            if ($user_mail['perm'] > 1) 
            { $_cFlag = true; } }             
        elseif ($user_check == true) {
            if ($user_mail['perm'] > 1) 
            { $_cFlag = true; } }

        if($_cFlag === true) {
            /*--------------------------------------------------------------------*/
            // load set of product names defined by admin
            /*--------------------------------------------------------------------*/
            $products = explode(',', $this->getConf('products'));
            $STR_PRODUCTS = "";
            foreach ($products as $_products)
            {
                //if product is preselected by syntax
                if(strtoupper ($_products) == strtoupper ($data['product'])) { $option_param = '<option value="'.$_products.'" selected >'; }
                else { $option_param = '<option value="'.$_products.'" >'; }
                
                $STR_PRODUCTS = $STR_PRODUCTS . $option_param .$_products."</option>'     ";
            }
            
            /*--------------------------------------------------------------------*/
            // load set of severity values defined by admin
            /*--------------------------------------------------------------------*/
            $STR_SEVERITY = "";
            $severity = explode(',', $this->getConf('severity')) ;
            foreach ($severity as $_severity)
            {
                $STR_SEVERITY = $STR_SEVERITY . '<option value="'.$_severity.'" >'.$_severity."</option>'     ";
            }
            
            // a file to store the comments regarding an issue going for and back
            $comments_file == metaFN($ID, '.cmnts');
            /*--------------------------------------------------------------------*/
            // create the report template and check input on client site
            /*--------------------------------------------------------------------*/
            $ret = '<div class="it__cir_form"><script type="text/javascript" src="include/selectupdate.js"></script>'.NL.
                   '<script>
                   // JavaScript Document
                    function chkFormular (frm) {
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
                        if ((frm.title.value.length <= 5) & (frm.title.value.indexOf(" ") == -1)) {
                          alert ("'.$this->getLang('wmsg5').'");
                          frm.title.focus();
                          return false;
                        }
                        if ((frm.description.value.length <= 5) & (frm.description.value.indexOf(" ") == -1)) {
                          alert ("'.$this->getLang('wmsg3').'");
                          frm.description.focus();
                          return false;
                      	}
                    }
                   </script>'.
                   '<form class="issuetracker__form" name="issuetracker__form" method="post" onsubmit="return chkFormular(this)"'.'" accept-charset="'.$lang['encoding'].'"><p>';
            $ret .= formSecurityToken(false).
            '<input type="hidden" name="do" value="show" />'.
            '<input type="hidden" name="id" value="'.$ID.'" />'.
            '<input type="hidden" name="created" type="text" value="'.$cur_date.'"/>'.
            '<input type="hidden" name="comments" type="text" value="'.$comments_file.'"/>'.
            '<table>
              <tr>
                <td>'.$this->getLang('th_project').'</td>
                <td><label class="it__cir_projectlabel">'.$project.'</label></td>
              </tr>'.
             '<tr>
                <td>'.$this->getLang('th_product').'</td>
                <td><select class="element select small it__cir_select" name="product">'.$STR_PRODUCTS.'</select></td>
              </tr>'.
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
        $ret .= '</tr>'.
             '<tr><td colspan=2>&nbsp;</td></tr>'.
             '<tr>
                <td>'.$this->getLang('th_username').'</td>
                <td><input class="it__cir_input" name="user_name" value="'.$user_mail['userinfo']['name'].'"/></td>
              </tr>'.
             '<tr>
                <td>'.$this->getLang('th_usermail').'</td>
                <td><input class="it__cir_input" name="user_mail" value="'.$user_mail['userinfo']['mail'].'"/></td>
              </tr>'.
             '<tr>';
                  //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'User phone')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_input" name="user_phone" value="'.$user_phone['userinfo']['phone'].'"/>';
                  } 
                  else {
                      $ret .= ' <td>'.$this->getLang('th_userphone').'</td>
                                <td><input class="it__cir_input" name="user_phone" value="'.$user_phone['userinfo']['phone'].'"/></td>';
                  }             
              $ret .= '</tr>'.
             '<tr>';
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Add contact')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_input" name="add_user_mail" value="'.$_REQUEST['add_user_mail'].'"/>';
                  } 
                  else {
                      $ret .= ' <td>'.$this->getLang('th_reporteradcontact').'</td>
                                <td><input class="it__cir_input" name="add_user_mail" value="'.$_REQUEST['add_user_mail'].'"/></td>';
                  }             
        $ret .= '</tr>'.
            '<tr><td colspan=2>&nbsp;</td></tr>'.
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
        $ret .= '</tr>'.
            '<tr>
                <td>'.$this->getLang('th_title').'</td>
                <td><input class="it__cir_linput" name="title" value="'.$_REQUEST['title'].'"/></input></td>
             </tr>'.
            '<tr>
                <td>'.$this->getLang('th_description').'</td>
                <td>';

// mod for editor ---------------------------------------------------------------------
$ret .= '<div class="it_edittoolbar" style="margin-left:30px; margin-top:6px;">
         <script>
          function doHLine(tag1,obj)
          { textarea = document.getElementById(obj);
          	if (document.selection) 
          	{     // Code for IE
          				textarea.focus();
          				var sel = document.selection.createRange();
          				sel.text = tag1 + sel.text;
          	}
            else 
            {   // Code for Mozilla Firefox
             		var len = textarea.value.length;
             	  var start = textarea.selectionStart;
             		var end = textarea.selectionEnd;
              		
             		var scrollTop = textarea.scrollTop;
             		var scrollLeft = textarea.scrollLeft;
              		
                var sel = textarea.value.substring(start, end);
         		    var rep = tag1 + sel;
                textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
              		
             		textarea.scrollTop = scrollTop;
             		textarea.scrollLeft = scrollLeft;
          	}
          }'.

         "function doLink(tag1,tag2,obj)
          {   var sel;
              textarea = document.getElementById(obj);
              var url = prompt('Enter the URL:','http://');
              var scrollTop = textarea.scrollTop;
              var scrollLeft = textarea.scrollLeft;
              
              if (url != '' && url != null) 
              {   if (document.selection) 
                  {   textarea.focus();
                      var sel = document.selection.createRange();
                      
                      if(sel.text=='') { sel.text = '<a href=\"' + url + '\">' + url + '</a>'; }
                      else { sel.text = '<a href=\"' + url + '\">' + sel.text + '</a>'; }				
                  }
                  else 
                  {   var len = textarea.value.length;
                      var start = textarea.selectionStart;
                      var end = textarea.selectionEnd;
                      var sel = textarea.value.substring(start, end);
                      
                      if(sel==''){ sel=url; } 
                      else { var sel = textarea.value.substring(start, end); }
                      
                      var rep = '<a href=\"' + url + '\">' + sel + '</a>';
                      textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
                      textarea.scrollTop = scrollTop;
                      textarea.scrollLeft = scrollLeft;
                	}
              }
          }".
         'function doAddTags(tag1,tag2,obj)
          { textarea = document.getElementById(obj);
          	// Code for IE
          	if (document.selection) 
          			{ textarea.focus();
          				var sel = document.selection.createRange();
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
          		  var rep = tag1 + sel + tag2;
                textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
          		
          		  textarea.scrollTop = scrollTop;
          		  textarea.scrollLeft = scrollLeft;
          	}
          }
          
          function doList(tag1,tag2,obj)
          {
              textarea = document.getElementById(obj);

          		if (document.selection) 
          			{ // Code for IE
          				textarea.focus();
          				var sel = document.selection.createRange();
          				var list = sel.text.split("\n");
          		
          				for(i=0;i<list.length;i++) 
          				{
          				list[i] = "[li]" + list[i] + "[/li]";
          				}
          				sel.text = tag1 + "\n" + list.join("\n") + "\n" + tag2;
          			} 
              else
          			{ // Code for Firefox
          		    var len = textarea.value.length;
          	      var start = textarea.selectionStart;
          		    var end = textarea.selectionEnd;
          		    var i;

          		    var scrollTop = textarea.scrollTop;
          		    var scrollLeft = textarea.scrollLeft;

                  var sel = textarea.value.substring(start, end);
          		    var list = sel.split("\n");
          		
              		for(i=0;i<list.length;i++) 
              		{ list[i] = "[li]" + list[i] + "[/li]"; }

              		var rep = tag1 + "\n" + list.join("\n") + "\n" +tag2;
              		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);

              		textarea.scrollTop = scrollTop;
              		textarea.scrollLeft = scrollLeft;
              }
          }
         </script>';                      
// mod for editor ---------------------------------------------------------------------

	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/bold.png\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('[b]','[/b]','description')\">".NL;
  $ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/italic.png\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('[i]','[/i]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/underline.png\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('[u]','[/u]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/strikethrough.png\" name=\"btnStrike\" title=\"Strike through\" onClick=\"doAddTags('[s]','[/s]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/subscript.png\" name=\"btnSubscript\" title=\"Subscript\" onClick=\"doAddTags('[sub]','[/sub]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/superscript.png\" name=\"btnSuperscript\" title=\"Superscript\" onClick=\"doAddTags('[sup]','[/sup]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/hr.png\" name=\"btnLine\" title=\"hLine\" onClick=\"doHLine('[hr]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/ordered.png\" name=\"btnList\" title=\"Ordered List\" onClick=\"doList('[ol]','[/ol]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/unordered.png\" name=\"btnList\" title=\"Unordered List\" onClick=\"doList('[ul]','[/ul]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/quote.png\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('[blockquote]','[/blockquote]','description')\">".NL; 
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/code.png\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('[code]','[/code]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/pen_red.png\" name=\"btnRed\" title=\"Red\" onClick=\"doAddTags('[red]','[/red]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/pen_green.png\" name=\"btnGreen\" title=\"Green\" onClick=\"doAddTags('[grn]','[/grn]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/pen_blue.png\" name=\"btnBlue\" title=\"Blue\" onClick=\"doAddTags('[blu]','[/blu]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/bg_yellow.png\" name=\"btn_bgYellow\" title=\"bgYellow\" onClick=\"doAddTags('[bgy]','[/bgy]','description')\">".NL;
	$ret .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/link.png\" name=\"btn_link\" title=\"Link\" onClick=\"doAddTags('[link]','[/link]','description')\">".NL;
	$it_edit_tb .= "<img class=\"xseditor_button\" src=\"".$imgBASE."/img.png\" name=\"btn_img\" title=\"Image\" onClick=\"doAddTags('[img]','[/img]','$type')\">".NL;
	$it_edit_tb .= "<a href=\"http://www.imageshack.us/\" target=\"_blank\"><<img class=\"xseditor_button\" src=\"".$imgBASE."/imageshack.png\" name=\"btn_ishack\" title=\"ImageShack upload (ext TaC !)\">></a>".NL;
  $ret .= "<br></div>";

          $ret .= '<textarea class="it__cir_linput" id="description" name="description" cols="109" rows="7">'.$_REQUEST['description'].'</textarea></td>
             </tr>'.
            '<tr><td colspan=2>&nbsp;</td></tr>';
                   //Check config if hidden
                  if(strpos($this->getConf('ltdReport'),'Symptom link 1')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_linput" name="attachment1" value="'.$_REQUEST['attachment1'].'"/>';
                  } 
                  else {
                      $ret .= '<tr><td>'.$this->getLang('th_sympt').'1</td>
                                   <td><input class="it__cir_linput" name="attachment1" value="'.$_REQUEST['attachment1'].'"/></td></tr>';
                  }             
                  if(strpos($this->getConf('ltdReport'),'Symptom link 2')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_linput" name="attachment2" value="'.$_REQUEST['attachment2'].'"/>';
                  } 
                  else {
                      $ret .= '<tr><td>'.$this->getLang('th_sympt').'2</td>
                                   <td><input class="it__cir_linput" name="attachment2" value="'.$_REQUEST['attachment2'].'"/></td></tr>';
                  }             
                  if(strpos($this->getConf('ltdReport'),'Symptom link 3')!==false){
                      $ret .= ' <input type="hidden" class="it__cir_linput" name="attachment3" value="'.$_REQUEST['attachment3'].'"/>';
                  } 
                  else {
                      $ret .= '<tr><td>'.$this->getLang('th_sympt').'3</td>
                                   <td><input class="it__cir_linput" name="attachment3" value="'.$_REQUEST['attachment3'].'"/></td></tr>';
                  }             
        $ret .= '</table><p><input type="hidden" name="modified" type="text" value="'.$cur_date.'"/>'.
                '<input type="hidden" name="assigned" type="text" value="" />';
    
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
            $ret .= '<p><input name="submit" type="submit" value="'.$this->getLang('btn_reportsave').'" class="button" id="edbtn__save" title="'.$this->getLang('btn_reportsave').'"/>'.
            '</p></form></div>';
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
          
          if(!function_exists('checkdnsrr'))
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
          else if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
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

        $x_comment = preg_replace('/\[img\]/i', '<img src="', $x_comment);
        $x_comment = preg_replace('/\[\/img\]/i', '" class="it_cmnt_pics" alt="" />', $x_comment);    

      return $x_comment;
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
          
          $mods[$mod_id]['timestamp'] = $issue['created'];
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
?>