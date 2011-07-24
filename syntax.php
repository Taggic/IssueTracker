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
        return confToHash(dirname(__FILE__).'/INFO');
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
        $params = explode('|',$match,3);
        
        //Default Value
        $data['display'] = 'ALL';
        $data['status'] = 'ALL';
        
        foreach($params as $param){            
            $splitparam = explode('=',$param);
            if ($splitparam[1] != '')
                {
                if ($splitparam[0]=='project')
                	{$data['project'] = $splitparam[1];
                    /*continue;*/}

                if ($splitparam[0]=='status')   
                	{$data['status'] = strtoupper($splitparam[1]);
                    /*continue;*/}
                
                if ($splitparam[0]=='display')
                	{$data['display'] = strtoupper($splitparam[1]);
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
				$helper = plugin_load('helper','captcha');
			if(!is_null($helper) && $helper->isEnabled())
				{	
				return $helper->check();
				}
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
            if (($data['display']=='FORM') || ($data['display']=='ALL'))
            {
                //If it s a user report add it to the db-file
                $Generated_Header = '';
                if (isset($_REQUEST['severity'])) 
                {
                    if ($_REQUEST['severity'])
                      {
                          if ($this->_captcha_ok())
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
                                    $issues[$issue_id]['status'] = "New";
                                    $issues[$issue_id]['user_name'] = htmlspecialchars(stripslashes($_REQUEST['user_name']));
                                    $issues[$issue_id]['user_mail'] = htmlspecialchars(stripslashes($_REQUEST['user_mail']));
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
                                    if (($valid_umail == true) && (strlen($issues[$issue_id]['description'])>9) && (stripos($xdescription, " ") > 0) && (strlen($issues[$issue_id]['version']) >0))
                                    {                                
                                        //Create db-file
                                        $fh = fopen($pfile, 'w');
                                        fwrite($fh, serialize($issues));
                                        fclose($fh);
                                        $Generated_Header = '<div style="border: 3px green solid; background-color: lightgreen; margin: 10px; padding: 10px;">Your report have been successfully stored as issue#'.$issue_id.'</div>';
//                                        $this->_emailForNewBug($issue_id,$data['project'],$issues[$issue_id]['product'],$issues[$issue_id]['version'],$issues[$issue_id]['severity'],$issues[$issue_id]['description']);
                                        $this->_emailForNewIssue($data['project'],$issues[$issue_id]);
                                        $_REQUEST['description'] = '';
                                    }
                                
                                    else
                                    {
                                        $wmsg ='';
                                        if ($valid_umail == false) 
                                            { $wmsg = 'Please enter valid eMail address, preferrably your own, for clarifications and/or feedback regarding your reported issue.'; }
                                        elseif (strlen($issues[$issue_id]['version']) <1)
                                            { $wmsg = 'Please enter a valid product version to relate this issue properly.'; }
                                        else 
                                            { $wmsg = 'Please provide a better description of your issue.'; }
                                        
                                        $Generated_Header = '<div style="border: 3px red solid; background-color: #FFAAAD; margin: 10px; padding: 10px;">'.$wmsg.'</div>';
                                    }
                                
                                }
                          else
                                {
                                $Generated_Header = ':<div class ="important">Wrong answer to the antispam question.</div>';
                                }  
                          }
                    }            
                }
            }
            $Generated_Table = '';
            $Generated_Scripts = '';
            $Generated_Report = '';
            

            // Create table            
            if (($data['display']=='ISSUES') || ($data['display']=='ALL'))
            {
                $Generated_Table = $this->_table_render($issues,$data); 
                $Generated_Scripts = $this->_scripts_render();
            }

            // Count only ...        
            if ($data['display']=='COUNT') 
            {
                $Generated_Table = $this->_count_render($issues);                
            }            
            // Generate form
            if (($data['display']=='FORM') || ($data['display']=='ALL'))
            {$Generated_Report = $this->_report_render($data);}

            // Render            
            $renderer->doc .= $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report;
        }
    }

/******************************************************************************/
/* Create count output
*/
    function _count_render($issues)
    {
        $count = array();
        foreach ($issues as $issue)
        {
            $status = $this->_get_one_value($issue,'status');
            if ($status != '')
                if ($this->_get_one_value($count,$status)=='')
                    {$count[$status] = array(1,$status);}
                else
                    {$count[$status][0] += 1;}                                
        }
        $rendered_count = '<ul>';
        foreach ($count as $value)
        {
            $rendered_count .= '<li>'.$value[1].' : '.$value[0].'</li>';
        }
        $rendered_count .= '</ul>';
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
/* Create list of Issues
*/
    function _table_render($issues,$data)
    {
        global $ID;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $hdr_style="style='text-align:left; font-size:0.85em;'";
        $style =' style="text-align:left; white-space:pre-wrap;">';
        $date_style =' style="text-align:center; white-space:pre;">';
        $user_grp = pageinfo();
        $noStatIMG = $this->getConf('noStatIMG');
        $noSevIMG = $this->getConf('noSevIMG');
        $project = $data['project'];
                
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
        
        // members of defined groups allowed changing issue contents 
        if ((strpos($this->getConf('assign'),$user_grps)!== false))       
        {   
            $head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$project."' class='sortable editable resizable inline'>".
                    "<thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th>".
                    "<th id='created'>Created</th>".
                    "<th id='product'>Product</th>".
                    "<th id='version'>Version</th>".
                    "<th id='severity'>Severity</th>".
                    "<th id='status'>Status</th>".
                    "<th id='user_name'>User name</th>".
                    "<th id='title'>Title</th>".
                    "<th id='assigned'>assigned to</th>". 
                    "<th id='resolution'>Resolution</th>".
                    "<th id='modified'>Modified</th></tr></thead>";        
            $body = '<tbody>';
            
            foreach ($issues as $issue)
            {
                if (($data['status']=='ALL') || (strtoupper($issue['status'])==$data['status']))
                {
                    $a_status = $this->_get_one_value($issue,'status');
                    $a_severity = $this->_get_one_value($issue,'severity');
                    
                    if ($noStatIMG === false) {                    
                        $status_img = $imgBASE . implode('', explode(' ',strtolower($a_status))).'.gif';
                        $status_img =' align="center"> <IMG border=0 alt="'.$a_status.'" title="'.$a_status.'" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$status_img.'" width=16 height=16>';
                    }                    
                    else {
                        $status_img = $style.$a_status; }
                                                                
                    if ($noSevIMG === false) {                    
                        $severity_img = $imgBASE . implode('', explode(' ',strtolower($a_severity))).'.gif';
                        $severity_img =' align="center"> <IMG border=0 alt="'.$a_severity.'" title="'.$a_severity.'" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$severity_img.'" width=16 height=16>';
                    }
                    else {
                        $severity_img = $style.$a_severity; }
                                            
                    $body .= '<tr id = "'.$project.' '.$this->_get_one_value($issue,'id').'">'.   
                    
                    '<td'.$style.$this->_get_one_value($issue,'id').'</td>'.
                    '<td'.$date_style.$this->_get_one_value($issue,'created').'</td>'.
                    '<td'.$style.$this->_get_one_value($issue,'product').'</td>'.
                    '<td'.$style.$this->_get_one_value($issue,'version').'</td>'.
                    '<td'.$severity_img.'</td>'.
                    '<td'.$status_img.'</td>'.
                    '<td'.$style.'<a href="mailto:'.$this->_get_one_value($issue,'user_mail').'">'.$this->_get_one_value($issue,'user_name').'</a></td>'. 
                    '<td class="canbreak"'.$style.$this->_get_one_value($issue,'title').'</td>'.
                    '<td'.$style.'<a href="mailto:'.$this->_get_one_value($issue,'assigned').'">'.$this->_get_one_value($issue,'assigned').'</a></td>'. 
                    '<td'.$style.$this->_get_one_value($issue,'resolution').'</td>'.
                    '<td'.$date_style.$this->_get_one_value($issue,'modified').'</td>'.
                    '</tr>';        
                }
            } 
            $body .= '</tbody></table></div>';          
        } 

        else       
        {   
            //$head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$project."' class=\"sortable resizable inline\"><thead><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Created'>Created</th><th id='Version'>Version</th><th id='User'>User</th><th id='Description'>Description</th><th id='assigned'>assigned to</th><th id='Resolution'>Resolution</th><th id='Modified'>Modified</th></tr></thead>";        

            //Build table header according settings
            $configs = explode(',', $this->getConf('shwtbl_usr')) ;
            $reduced_header = '';
            foreach ($configs as $config)
            {
                $reduced_header = $reduced_header."<th id='".$config."'>".strtoupper($config)."</th>";
            }

            //Build rows according settings
            $reduced_issues='';
            foreach ($issues as $issue)
            {
                $reduced_issues = $reduced_issues.'<tr id = "'.$project.' '.$this->_get_one_value($issue,'id').'">'.
                                                  '<td'.$style.$this->_get_one_value($issue,'id').'</td>';
                foreach ($configs as $config)
                {
                    $reduced_issues = $reduced_issues.'<td'.$style.$this->_get_one_value($issue,strtolower($config)).'</td>';
                }
                $reduced_issues = $reduced_issues.'</tr>';
            }
            
            $head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$project."' class='sortable resizable inline'>"."<thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th>".$reduced_header."</tr></thead>";
            $body = '<tbody>'.$reduced_issues.'</tbody></table></div>';
        }
//        $body = $body . '<p><label> User & Groups : &nbsp;&nbsp;'.$user_grps.' = '.strpos($this->getConf('assign'),$user_grps).'</label></p>';
        
        $ret = $head.$body;
     $ret = '<form  method="post" action="doku.php?id=' . $ID . '&do=showcase"><p><label> Show details: &nbsp;&nbsp;</label>'.
                 '<input class="showid__option" name="showid" id="showid" type="text" size="10" value="0"/>'.
//                 '<input type="hidden" name="pfile" value="'.$project.'" />'.
                 '<input type="hidden" name="project" id="project" type="text" value="'.$project.'"/>'.
                 '<input class="button" id="showcase" type="submit" name="showcase" value="Go" title="Go");/>'.


             '</form>' . $ret;
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
            $subject=$issue['severity'].' issue reported for '.$project.' on Product:'.$issue['product'].' v'.$issue['version'];            
            
            $body = 'Dear admin, \n\n A new issue was created in the project.\n'.
            'ID: '          .$issue['id'].
            'Product: '     .$issue['product'].'\n'.
            'Version: '     .$issue['version'].'\n'.
            'Severity: '    .$issue['severity'].'\n'.
            'Creator: '     .$issue['user_name'].'\n'.
            'Title: '       .$issue['title'].'\n';
            'Description: ' .$issue['description'].'\n';

            $from=$this->getConf('email_address') ;
            $to=$from;
            mail_send($to, $subject, $body, $from, $cc='', $bcc='', $headers=null, $params=null);
        }
    }

/******************************************************************************/
/* send an e-mail to user due to issue modificaion
*/
    function _emailForIssueMod($project,$issue,$comment)
    {
        if ($this->getConf('userinfo_email')==1)
        {
            $subject='Modification info: '.$issue['id'].' was modified';            
            
            $body = 'Dear user, \n\n Your reported issue was modified.\n'.
            'ID: '          .$issue['id'].
            'Status: '      .$issue['status'].'\n'.
            'Product: '     .$issue['product'].'\n'.
            'Version: '     .$issue['version'].'\n'.
            'Severity: '    .$issue['severity'].'\n'.
            'Creator: '     .$issue['user_name'].'\n'.
            'Title: '       .$issue['title'].'\n'.
            'Comment by: '  .$comment['author'].'\n'.
            'submitted on:' .$comment['timestamp'].'\n'.
            'Comment: '     .$comment['comment'].'\n'.
            'see details'   ;

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
        $project = $data['project'];

        // load severity values from config file into control
        $user_mail = pageinfo();  //to get mail address of reporter
        $cur_date = date ('Y-m-d G:i:s');
        
        /*--------------------------------------------------------------------*/
        // load set of product names defined by admin
        /*--------------------------------------------------------------------*/
        $products = explode(',', $this->getConf('products'));
        $STR_PRODUCTS = "";
        foreach ($products as $_products)
        {
            $STR_PRODUCTS = $STR_PRODUCTS . '<option value="'.$_products.'" >'.$_products."</option>'     ";
        }
        
        /*--------------------------------------------------------------------*/
        // load set of version values defined by admin
        /*--------------------------------------------------------------------*/
/*        $versions = explode('|', $this->getConf('versions'));
        $xversions = explode(',', $versions[0]);
        $STR_VERSIONS = "";
        foreach ($xversions as $_versions)
        {
            $STR_VERSIONS = $STR_VERSIONS . '<option value="'.$_versions.'" >'.$_versions."</option>'     ";
        }
*/        
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
        // create the report template
        /*--------------------------------------------------------------------*/
        $ret = '<br /><br /><script type="text/javascript" src="include/selectupdate.js"></script>'.
               '<form class="issuetracker__form" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'"><p>';
        $ret .= formSecurityToken(false).
        '<input type="hidden" name="do" value="show" />'.
        '<input type="hidden" name="id" value="'.$ID.'" />'.
        '<input type="hidden" name="created" type="text" value="'.$cur_date.'"/>'.
        '<input type="hidden" name="comments" type="text" value="'.$comments_file.'"/>'.
        '<p><label> Project : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$project.'</label></p>'.
        '<p><label> Product : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>'.
            '  <select class="element select small issuetracker__option" name="product" style="width:208px">'.
            '       '.$STR_PRODUCTS.
            '	 </select></p>'.      
        '<p><label> Version : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>'.
            '  <input class="element select small issuetracker__option" name="version" type="text" size="30" value="'.$STR_VERSIONS.'"/></p>'.
        '<p><label> User name : &nbsp;&nbsp;</label><input class="issuetracker__option" name="user_name" type="text" size="30" value="'.$user_mail['userinfo']['name'].'"/></p>'.
        '<p><label> User mail : &nbsp;&nbsp;&nbsp;&nbsp;</label><input class="issuetracker__option" name="user_mail" type="text" size="30" value="'.$user_mail['userinfo']['mail'].'"/></p>'.
        '<p><label> User phone : &nbsp;</label><input class="issuetracker__option" name="user_phone" type="text" size="30" value="'.$user_phone['userinfo']['phone'].'"/></p>'.
        '<p><label> Add contact : &nbsp;</label><input class="issuetracker__option" name="add_user_mail" type="text" size="30" value="'.$_REQUEST['add_user_mail'].'"/></p>'.        
        '<p><label> Severity :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>'.
            '  <select class="element select small issuetracker__option" name="severity" style="width:208px">'.
            '       '.$STR_SEVERITY.
            '	 </select></p>'.      
        '<p><label> Issue Title : </label><br /><input class="issuetracker__option" name="title" type="text" size="146" value="'.$_REQUEST['title'].'"/></input></p>'.
        '<p><label> Issue Description : </label><br /><textarea class="issuetracker__option" name="description" cols="109" rows="7">'.$_REQUEST['description'].'</textarea></p>'.
        '<p><label> Symptom link 1 : </label><input class="issuetracker__option" name="attachment1" type="text" size="129" value="'.$_REQUEST['attachment1'].'"/></p>'.        
        '<p><label> Symptom link 2 : </label><input class="issuetracker__option" name="attachment2" type="text" size="129" value="'.$_REQUEST['attachment2'].'"/></p>'.        
        '<p><label> Symptom link 3 : </label><input class="issuetracker__option" name="attachment3" type="text" size="129" value="'.$_REQUEST['attachment3'].'"/></p>'.        
        '<p><input type="hidden" name="modified" type="text" value="'.$cur_date.'"/>'.
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

        $ret .= '<p><input class="button" type="submit" '.
        'value="Send" /></p>'.
        '</form>';

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
             // character not valid in local part unless 
             // local part is quoted
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
}
?>