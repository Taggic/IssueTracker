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

            // Create Details form
            // {{issuetracker>project=cbc_service_wiki|status=all|display=ID,0}}  
            

                                  
            if (strpos($data['display'],'ID')!== false)
            {
                $Generated_Table = $this->_details_render($issues,$data); 
                
                //If comment to be added
                $Generated_Header = '';

                if (isset($_REQUEST['comment'])) 
                {
//echo sprintf("<p><b>code line 213</b></p>\n");
                    if (($_REQUEST['comment']) && (isset($_REQUEST['comment_issue_ID'])))
                      {
                          if ($this->_captcha_ok())
                            {
                                if (checkSecurityToken())
                                {
                                    // get comment file contents
                                    $cID  = "issuecomments_".$_REQUEST['comment_issue_ID'];
                                    $comments_file = metaFN($cID, '.issues');

                                    if (@file_exists($comments_file))
                                    	{$comments  = unserialize(@file_get_contents($comments_file));}
                                    else
                                        	{$comments = array();}
                                        	
                                    //Add it to the issue file
                                    $comment_id=count($comments);      
                                    foreach ($comments as $value)
                                        {if ($value['id'] >= $comment_id) {$comment_id=$value['id'] + 1;}}
                                    
                                    $comments[$comment_id]['id'] = $comment_id;    
                                    $comments[$comment_id]['author'] = htmlspecialchars(stripslashes($_REQUEST['author']));
                                    $comments[$comment_id]['timestamp'] = htmlspecialchars(stripslashes($_REQUEST['timestamp']));
                                    $comments[$comment_id]['comment'] = htmlspecialchars(stripslashes($_REQUEST['comment']));    
    
                                    //Create comments file
                                    $fh = fopen($comments_file, 'w');
                                    fwrite($fh, serialize($comments));
                                    fclose($fh);
                                    $Generated_Header = '<div style="border: 3px green solid; background-color: lightgreen; margin: 10px; padding: 10px;">Your report have been successfully stored as issue#'.$issue_id.'</div>';

                                    $this->_emailForIssueMod($data['project'],$issues[$issue_id], $comments[$comment_id]);
                                 }
                            }
                       }
                 }                                
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
            $head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$data['project']."' class='sortable editable resizable inline'>".
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
                                            
                    $body .= '<tr id = "'.$data['project'].' '.$this->_get_one_value($issue,'id').'">'.   
                    
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
            //$head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$data['project']."' class=\"sortable resizable inline\"><thead><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Created'>Created</th><th id='Version'>Version</th><th id='User'>User</th><th id='Description'>Description</th><th id='assigned'>assigned to</th><th id='Resolution'>Resolution</th><th id='Modified'>Modified</th></tr></thead>";        

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
                $reduced_issues = $reduced_issues.'<tr id = "'.$data['project'].' '.$this->_get_one_value($issue,'id').'">'.
                                                  '<td'.$style.$this->_get_one_value($issue,'id').'</td>';
                foreach ($configs as $config)
                {
                    $reduced_issues = $reduced_issues.'<td'.$style.$this->_get_one_value($issue,strtolower($config)).'</td>';
                }
                $reduced_issues = $reduced_issues.'</tr>';
            }
            
            $head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$data['project']."' class='sortable resizable inline'>"."<thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th>".$reduced_header."</tr></thead>";
            $body = '<tbody>'.$reduced_issues.'</tbody></table></div>';
        }
//        $body = $body . '<p><label> User & Groups : &nbsp;&nbsp;'.$user_grps.' = '.strpos($this->getConf('assign'),$user_grps).'</label></p>';
        
        $ret = $head.$body;
        $ret .= '<form  method="post" action=""><p><label> Show details of Issue: &nbsp;&nbsp;</label><input class="issuetracker__option" name="issueid" type="text" size="10" value=""/>'.
                '<input class="button" id="showcase" type="submit" name="do[showcase]" value="showcase" title="showcase");/></form>';                
                
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
        $comments_file == metaFN($ID, '.issues');
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
/**output issue details view
Template for Issue Overview maybe added to the side bar:
--------------------------------------------------------
*/
    function _details_render($issues, $data)
    {
      
//        $li = '<a href="/bugreports/issuedetails" class="wikilink1" title="bugreports:issuedetails">Issue Details</a>';
//        $fp = fopen('http://www.example.com/index.php', 'r');

        // load issue details and display on page
        global $lang;
        $imgBASE = DOKU_BASE."lib/plugins/issuetracker/images/";
        $project = $data['project'];
        $d_param = explode(',',$data['display']);
        $issue_id = $d_param[1];
        
        // get issues file contents
        $pfile = metaFN($data['project'], '.issues');   
        if (@file_exists($pfile))
        	{$issue  = unserialize(@file_get_contents($pfile));}
        else
        	{
              // promt error message that issue with ID does not exist
              return $ret;
          }	          
        
        // get detail information from issue comment file
        $cID = "issuecomments_".$issue_id;
        $comments_file = metaFN($cID, '.issues');
        if (@file_exists($comments_file))
        	{$comments  = unserialize(@file_get_contents($comments_file));}
        else
            	{$comments = array();}

//--------------------------------------
//Tables for the Issue details view:
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
                      <TD width="25%">'.$issue[$issue_id]['id'].'</TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>
                      <TD width="10%"><B>Project:</B></TD>
                      <TD>'.$project.'</TD></FONT>
                   </TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center>
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Severity:</B></TD>
                      <TD width="25%">'.$issue[$issue_id]['severity'].'</TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>'.
                      '<TD width="10%"><B>Product:</B></TD>
                      <TD>'.$issue[$issue_id]['product'].'</TD>
                   </FONT></TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center>
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Status:</B></TD>
                      <TD width="25%">'.$issue[$issue_id]['status'].'</TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>
                      <TD width="10%"><B>Affects Version:</B></TD>
                      <TD>'.$issue[$issue_id]['version'].'</TD>
                   </Font></TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center>                      
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Assigned:</B></TD>
                      <TD width="25%"><A  href="mailto:'.$issue[$issue_id]['assigned'].'">'.$issue[$issue_id]['assigned'].'</A></TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>'.
                      '<TD width="10%"><B>Issue created:</B></TD>
                      <TD><SPAN class=date>'.$issue[$issue_id]['created'].'</SPAN></TD>
                   </FONT></TR>'.
                   
                   '<TR bgColor=#f0f0f0 vAlign=center>
                      <TD width="3%"></TD>
                      <TD  width="10%"><B>Reporter:</B></TD>
                      <TD width="25%"><A  href="mailto:'.$issue[$issue_id]['user_mail'].'">'.$issue[$issue_id]['user_mail'].'</A></TD>
                      <TD width="25%"></TD>                   
                   <FONT size=1>'.                   
                      '<TD width="10%"><B>Issue modified:</B></TD>
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

$issue_initial_description = '<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY><TR>'.
        '<TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=2 >&nbsp;<FONT color=#ffffff><B>Initial description</B></FONT>&nbsp;</TD></TR></TBODY></TABLE>'.
        '<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%"><TBODY><TR>'.
        '<TD width="1%"><TD id=descriptionArea>'.$issue[$issue_id]['description'].'</TD></TR></TBODY></TABLE>';

$issue_attachments = '<DIV id=client_details><TABLE id=tab1 class=gridTabBox border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY>'.
                     '<TR><TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=3><FONT color=#ffffff><B>Links to symptom files</B></FONT></TD></TR>'.
                     '<TR><TD width="1%"></TD><TD bgColor=#ffffff vAlign=top colSpan=5>
                          1. <A href="'.$issue[$issue_id]['attachment1'].'"><IMG border=0 alt="symptoms 1" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment1'].'" href="'.$issue[$issue_id]['attachment1'].'">'.$issue[$issue_id]['attachment1'].'</A>'.
                     '<BR>2. <A href="'.$issue[$issue_id]['attachment2'].'"><IMG border=0 alt="symptoms 2" style="margin-right:0.5em" vspace=1em align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment2'].'" href="'.$issue[$issue_id]['attachment2'].'">'.$issue[$issue_id]['attachment2'].'</A>'.
                     '<BR>3. <A href="'.$issue[$issue_id]['attachment3'].'"><IMG border=0 alt="symptoms 3" style="margin-right:0.5em" vspace=1 align=absMiddle src="'.$imgBASE.'sympt.gif" width=16 height=16></A><A title="'.$issue[$issue_id]['attachment3'].'" href="'.$issue[$issue_id]['attachment3'].'">'.$issue[$issue_id]['attachment3'].'</A>'.
                     '<BR><BR></TD></TR></TBODY></TABLE></DIV></BR>';              

$issue_comments_log ='<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY><TR>'.
        '<TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=2 >&nbsp;<FONT color=#ffffff><B>Comments (work log)</B></FONT>&nbsp;</TD></TR></TBODY></TABLE>'.
        '<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%"></TABLE></DIV>';
              // loop through the comments
              foreach ($comments as $a_comment)
              {
                    $x_id = $this->_get_one_value($a_comment,'id');
                    $x_mail = $this->_get_one_value($a_comment,'author');
                    $issue_comments_log .= '<FONT size=1><I><label>['.$this->_get_one_value($a_comment,'id').'] </label>&nbsp;&nbsp;&nbsp;'.
                                           '<label>'.$this->_get_one_value($a_comment,'timestamp').' </label>&nbsp;&nbsp;&nbsp;'.
                                           '<label><a href="mailto:'.$x_mail.'">'.$x_mail.'</a></label></I></FONT><BR>'.
                                           '<label>'.$this->_get_one_value($a_comment,'comment').'</label><BR><BR><hr width="90%"><BR>';
              } 
              $issue_comments_log .= '</DIV>';

/*$issue_comments_log = '<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY>'.
                      '<TR><TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=3><FONT color=#ffffff><B>Comments (work log)</B></FONT></TD></TR>'.
                      '<TR id=rowForcustomfield_10100><TD width="1%"></TD><TD><A name=action_48442><A href="mailto:'.$comments['comment']['mail'].'">'.$comments['comment']['mail'].'</A> <SPAN class=subText>[<SPAN class=date>'.$comments['comment']['timestamp'].'</SPAN>]</SPAN><TD width="1%"></TD></TR>'.
                      '<TR id=rowForcustomfield_10100><TD width="1%"></TD><TD>'.$comments['comment']['text'].'</TD><TD width="1%"></TD></TR></TBODY></TABLE></DIV>';
*/

        $issue_add_comment ='<DIV id=description-open><TABLE border=0 cellSpacing=0 cellPadding=4 width="90%" bgColor=#ffffff ><TBODY><TR>'.
        '<TD bgColor=#bbbbbb width="1%" noWrap align=middle colSpan=2 >&nbsp;<FONT color=#ffffff><B>Add a new comment</B></FONT>&nbsp;</TD></TR></TBODY></TABLE>'.
        '<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%"></TABLE></DIV>';

$issue_add_comment .= '<br /><script type="text/javascript" src="include/selectupdate.js"></script>'.
                     '<form class="comments__form" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'">';
                     
        // retrive basic information
        $user_mail = pageinfo();  //to get mail address of reporter
        $cur_date = date ('Y-m-d G:i:s');

        if($user_mail['userinfo']['mail']=='') {$u_mail_check ='unknown';}
        else {$u_mail_check =$user_mail['userinfo']['mail'];}

$issue_add_comment .= formSecurityToken(false). 
                     '<input type="hidden" name="comment_file" type="text" value="'.$comments_file.'"/>'.
                     '<input type="hidden" name="comment_issue_ID" type="text" value="'.$issue[$issue_id]['id'].'"/>'.
                     '<input type="hidden" name="author" type="text" value="'.$u_mail_check.'"/>'.        
                     '<input type="hidden" name="timestamp" type="text" value="'.$cur_date.'"/>'.        
                     '<p><textarea name="comment" type="text" cols="107" rows="7" value="'.$_REQUEST['comment'].'"></textarea></p>';        
             
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
            
                     $issue_add_comment .= '<p><input class="button" type="submit" '.
                                           'value="Add" /></p>'.
                                           '</form>';
                                           
        $body = $issue_edit_head . $issue_client_details . $issue_initial_description . $issue_attachments . $issue_comments_log . $issue_add_comment;
        return $body;    
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
/* for test purposes only => to be deleted
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