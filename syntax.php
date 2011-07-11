<?php
/**
*  IssueTracker Plugin: allows to create simple issue tracker
*
* initial code from DokuMicroBugTracker Plugin: allows to create simple bugtracker
*
* @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
* @author     Taggic <taggic@t-online.de>
* 
* feature extensions by Taggic on 2011-07-08
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
    /**************************************************************************
    * return some info
    */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/INFO');
    }

    function getType(){ return 'substition';}
    function getPType(){ return 'block';}
    function getSort(){ return 167;}
    
    /**************************************************************************
    * Connect pattern to lexer
    */
    function connectTo($mode){
        $this->Lexer->addSpecialPattern('\{\{issuetracker>[^}]*\}\}',$mode,'plugin_issuetracker');
    }
    
    /**************************************************************************
    * Handle the match
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

    /**************************************************************************
	  * Captcha OK	    
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
    
    /**************************************************************************
    * Create output
    */
    function render($mode, &$renderer, $data) {        
        global $ID;
        $project = $data['project'];           
        
        if ($mode == 'xhtml'){
            
            $renderer->info['cache'] = false;     
               
            // get bugs file contents
            $pfile = metaFN($data['project'], '.bugs'); 

            if (@file_exists($pfile))
            	{$bugs  = unserialize(@file_get_contents($pfile));}
            else
            	{$bugs = array();}	          

            $Generated_Header = '';
            if (($data['display']=='FORM') || ($data['display']=='ALL'))
            {
                //If it s a usr report add it to the pseudo db
                $Generated_Header = '';
                if (isset($_REQUEST['severity'])) 
                {
                    if ($_REQUEST['severity'])
                      {
                          if ($this->_captcha_ok())
                            {
                                if (checkSecurityToken())
                                {
                                
                                    //Add it
                                    $bug_id=count($bugs);      
                                    foreach ($bugs as $value)
                                        {if ($value['id'] >= $bug_id) {$bug_id=$value['id'] + 1;}}
                                    $bugs[$bug_id]['id'] = $bug_id;    
                                    $bugs[$bug_id]['version'] = htmlspecialchars(stripslashes($_REQUEST['version']));
                                    $bugs[$bug_id]['severity'] = htmlspecialchars(stripslashes($_REQUEST['severity']));
                                    $bugs[$bug_id]['created'] = htmlspecialchars(stripslashes($_REQUEST['created']));
                                    $bugs[$bug_id]['status'] = "New";
                                    $bugs[$bug_id]['user'] = htmlspecialchars(stripslashes($_REQUEST['user']));
                                    $bugs[$bug_id]['description'] = htmlspecialchars(stripslashes($_REQUEST['description']));
                                    $bugs[$bug_id]['assigned'] = 'admin';
                                    $bugs[$bug_id]['resolution'] = '';
                                    $bugs[$bug_id]['modified'] = htmlspecialchars(stripslashes($_REQUEST['modified']));
    
    
                                    $xuser = $bugs[$bug_id]['user'];
                                    $xdescription = $bugs[$bug_id]['description'];
                                    //check user mail address, necessary for further clarification of the issue
                                    if ((stripos($xuser, "@") > 1) && (strlen($bugs[$bug_id]['description'])>9) && (stripos($xdescription, " ") > 0))
                                    {                                
                                        //Create db-file
                                        $fh = fopen($pfile, 'w');
                                        fwrite($fh, serialize($bugs));
                                        fclose($fh);
                                        $Generated_Header = '<div style="border: 3px green solid; background-color: lightgreen; margin: 10px; padding: 10px;">Your report have been successfully stored as issue#'.$bug_id.'</div>';
                                        $this->_emailForNewBug($bug_id,$data['project'],$bugs[$bug_id]['version'],$bugs[$bug_id]['severity'],$bugs[$bug_id]['description']);
                                        $_REQUEST['description'] = '';
                                    }
                                
                                    else
                                    {
                                        $wmsg ='';
                                        if (stripos($xuser, "@") == 0) 
                                            { $wmsg = 'Please enter your eMail address for clarifications and/or feedback regarding your reported issue.'; }
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
            if (($data['display']=='BUGS') || ($data['display']=='ALL'))
            {
                $Generated_Table = $this->_table_render($bugs,$data); 
                $Generated_Scripts = $this->_scripts_render();
            }

            // Count only ...        
            if ($data['display']=='COUNT') 
            {
                $Generated_Table = $this->_count_render($bugs);                
            }            
            // Generate form
            if (($data['display']=='FORM') || ($data['display']=='ALL'))
            {$Generated_Report = $this->_report_render($data);}
                        
            // Render            
            $renderer->doc .= $Generated_Header.$Generated_Table.$Generated_Scripts.$Generated_Report;
        }
    }

    /**************************************************************************/
    function _count_render($bugs)
    {
        $count = array();
        foreach ($bugs as $bug)
        {
            $status = $this->_get_one_value($bug,'status');
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
    
    /**************************************************************************/
    function _scripts_render()
    {
        // added by Taggic on 2011-07-08
        // load status values from config into control
        $s_counter = 0;
        $status = explode(',', $this->getConf('status')) ;
        foreach ($status as $x_status)
        {
            $s_counter = $s_counter + 1;
            $STR_STATUS = $STR_STATUS . "case '".$x_status."':  val = ".$s_counter."; break;";
            $pattern = $pattern . "|" .  $x_status;
        }
        
        $BASE = DOKU_BASE."lib/plugins/issuetracker/";
        return    "<script type=\"text/javascript\" src=\"".$BASE."prototype.js\"></script><script type=\"text/javascript\" src=\"".$BASE."fabtabulous.js\"></script>
        <script type=\"text/javascript\" src=\"".$BASE."tablekit.js\"></script>
        <script type=\"text/javascript\">
            TableKit.Sortable.addSortType(new TableKit.Sortable.Type('status', {editAjaxURI : '".$BASE."edit.php', ajaxURI : '".$BASE."edit.php',
                    pattern : /^[".$pattern."]$/,
                    normal : function(v) {
                        var val = ".$s_counter.";
                        switch(v) {".$STR_STATUS."
                        }
                        return val;
                    }
                }
            ));
            TableKit.options.editAjaxURI = '".$BASE."edit.php';
            TableKit.Editable.multiLineInput('Description');
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

    /**************************************************************************/
    function _table_render($bugs,$data)
    {
        global $ID;

        $hdr_style="style='width:500px; text-align:left; font-size:0.85em;'";
        $style =' style="text-align:left; white-space:pre-wrap;">';
        $date_style =' style="text-align:center; white-space:pre;">';

        if (auth_quickaclcheck($ID) >= AUTH_ADMIN)        
            {   
                $head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$data['project']."' class=\"sortable editable resizable inline\"><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Created'>Created</th><th id='Version'>Version</th><th id='User'>User</th><th id='Description'>Description</th><th id='assigned'>assigned to</th><th id='Resolution'>Resolution</th><th id='Modified'>Modified</th></tr></thead>";        
                $body = '<tbody>';
                
                foreach ($bugs as $bug)
                {
                    if (($data['status']=='ALL') || (strtoupper($bug['status'])==$data['status']))
                    {
                        $body .= '<tr id = "'.$data['project'].' '.$this->_get_one_value($bug,'id').'">'.
                        '<td'.$style.$this->_get_one_value($bug,'id').'</td>'.
                        '<td'.$style.$this->_get_one_value($bug,'status').'</td>'.
                        '<td'.$style.$this->_get_one_value($bug,'severity').'</td>'.
                        '<td'.$date_style.$this->_get_one_value($bug,'created').'</td>'.
                        '<td'.$style.$this->_get_one_value($bug,'version').'</td>'.
                        '<td'.$style.'<a href="mailto:'.$this->_get_one_value($bug,'user').'">'.$this->_get_one_value($bug,'user').'</a></td>'. 
                        '<td class="canbreak"'.$style.$this->_get_one_value($bug,'description').'</td>'.
                        '<td'.$style.'<a href="mailto:'.$this->_get_one_value($bug,'assigned').'">'.$this->_get_one_value($bug,'assigned').'</a></td>'. 
                        '<td'.$style.$this->_get_one_value($bug,'resolution').'</td>'.
                        '<td'.$date_style.$this->_get_one_value($bug,'modified').'</td>'.
                        '</tr>';        
                    }
                }           
            } 

        else       
            {   
                //$head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$data['project']."' class=\"sortable resizable inline\"><thead><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Created'>Created</th><th id='Version'>Version</th><th id='User'>User</th><th id='Description'>Description</th><th id='assigned'>assigned to</th><th id='Resolution'>Resolution</th><th id='Modified'>Modified</th></tr></thead>";        
                $head = "<div class='issuetracker_div' ".$hdr_style."><table id='".$data['project']."' class=\"sortable resizable inline\"><thead><thead><tr><th class=\"sortfirstdesc\" id='id'>Id</th><th id='Status'>Status</th><th id='Severity'>Severity</th><th id='Version'>Version</th><th id='Description'>Description</th><th id='Resolution'>Resolution</th></tr></thead>";
                $body = '<tbody>';
                
                foreach ($bugs as $bug)
                {
                    if (($data['status']=='ALL') || (strtoupper($bug['status'])==$data['status']))
                    {
                        $body .= '<tr id = "'.$data['project'].' '.$this->_get_one_value($bug,'id').'">'.
                        '<td'.$style.$this->_get_one_value($bug,'id').'</td>'.
                        '<td'.$style.$this->_get_one_value($bug,'status').'</td>'.
                        '<td'.$style.$this->_get_one_value($bug,'severity').'</td>'.
                        //'<td'.$date_style.$this->_get_one_value($bug,'created').'</td>'.
                        '<td'.$style.$this->_get_one_value($bug,'version').'</td>'.
                        //'<td'.$style.'<a href="mailto:'.$this->_get_one_value($bug,'user').'">'.$this->_get_one_value($bug,'user').'</a></td>'. 
                        '<td class="canbreak"'.$style.$this->_get_one_value($bug,'description').'</td>'.
                        //'<td'.$style.'<a href="mailto:'.$this->_get_one_value($bug,'assigned').'">'.$this->_get_one_value($bug,'assigned').'</a></td>'. 
                        '<td'.$style.$this->_get_one_value($bug,'resolution').'</td>'.
                        //'<td'.$date_style.$this->_get_one_value($bug,'modified').'</td>'.
                        '</tr>';        
                    }
                }            
            }
        

        $body .= '</tbody></table></div>';        
        return $head.$body;
    }

    /**************************************************************************/
    function _get_one_value($bug, $key) {
        if (array_key_exists($key,$bug))
            return $bug[$key];
        return '';
    }

    /**************************************************************************/
    function _emailForNewBug($id,$project,$version,$severity,$description)
    {
        if ($this->getConf('send_email')==1)
        {
            $body='A new bug have entered in the project : '.$project.' (id : '.$id.")\n\n".' Version : '.$version.' ('.$severity.") :\n".$description;
            $subject='A new bug have entered in the project : '.$project.' Version :'.$version.' ('.$severity.') : '.$id;
            $from=$this->getConf('email_address') ;
            $to=$from;
            mail_send($to, $subject, $body, $from, $cc='', $bcc='', $headers=null, $params=null);
        }
    }

    /**************************************************************************/
    function _report_render($data)
    {
        global $lang;
        global $ID;
        $project = $data['project'];

        // load severity values from config file into control
        $user_mail = pageinfo();  //to get mail address of reporter
        $cur_date=date ('Y-m-d');
        $STR_SEVERITY = "";
        $severity = explode(',', $this->getConf('severity')) ;
        foreach ($severity as $_severity)
        {
            $STR_SEVERITY = $STR_SEVERITY . '<option value="'.$_severity.'" >'.$_severity."</option>'     ";
        }
        $versions = explode(',', $this->getConf('versions'));
        $STR_VERSIONS = "";
        foreach ($versions as $_versions)
        {
            $STR_VERSIONS = $STR_VERSIONS . '<option value="'.$_versions.'" >'.$_versions."</option>'     ";
        }

        $ret = '<br /><br /><form class="issuetracker__form" method="post" action="'.$_SERVER['REQUEST_URI'].'" accept-charset="'.$lang['encoding'].'"><p>';
        $ret .= formSecurityToken(false).
        '<input type="hidden" name="do" value="show" /><input type="hidden" name="id" value="'.$ID.'" /><input type="hidden" name="created" type="text" value="'.$cur_date.'"/></p>'.
        '<p><label> User : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input class="issuetracker__option" name="user" type="text" size="30" value="'.$user_mail['userinfo']['mail'].'"/></p>'.
        '<p><label> Severity :&nbsp;</label>'.
        '  <select class="element select small issuetracker__option" name="severity">'.
        '       '.$STR_SEVERITY.
        '	 </select></p>'.      
        '<p><label> Project : &nbsp;&nbsp;'.$project.'</label></p>'.
        '<p><label> Version : &nbsp;</label>'.
        '  <select class="element select small issuetracker__option" name="version">'.
        '       '.$STR_VERSIONS.
        '	 </select></p>'.      
        '<p><label> Issue Description : </label><br /><textarea class="issuetracker__option" name="description" cols="125" rows="5">'.$_REQUEST['description'].'</textarea></p>'.
        '<p><input type="hidden" name="modified" type="text" value="'.$cur_date.'"/><input type="hidden" name="assigned" type="text" value="" />';

        if ($this->getConf('use_captcha')==1) 
        {        
//              $ret .= "<p><div class='captcha_div'<table id='captcha_id'><table><tr><td id='captcha_pic'><img src='".DOKU_BASE."lib/plugins/issuetracker/image.php' alt='captcha' /></td>".
//                      "<td id='Answer'><label>What is the result? </label><br><input class='issuetracker__option' name='captcha' type='text' maxlength='3' value=''/></td></tr></table></div></p>";      
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

    /**************************************************************************/
    function _show_message($string){
        return "<script type='text/javascript'>
            alert('$string');
        </script>";
    }
}
?>
