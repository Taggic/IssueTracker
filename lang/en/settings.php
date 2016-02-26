<?php
/**
 * English language file for issuetracker plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Taggic@t-online.de
 */
 
// for the configuration manager
$lang['d_format']         = 'Y-m-d G:i:s';
$lang['it_data']          = 'IssueTracker root within data folder ("" = meta)<img title="Warning: It is very recommended to store the Issues outside the meta folder. So please enter a path here (e.g. \'data/it_store/\')." alt="warning" src="./lib/plugins/config/images/warning.png" style="float:right;"> ';
$lang['upload']           = 'allow symptom file uploads';
$lang['max_fsize']        = 'limit file size for uploads in Byte';
$lang['ip_blocked']       = 'turn on the anti-spam feature';
$lang['ip_blockd_time']   = 'ip will be blocked for x minutes before another upload can be initiated';
$lang['send_email']       = 'Inform by mail about new issues ?'; 
$lang['mail_templates']   = 'Use of html email templates ?';
$lang['email_address']    = 'Who is to be informed ?';
$lang['registered_users'] = 'Only registered users with edit permission on issue <br> tracker page are allowed to create reports and add comments';
$lang['auth_ad_overflow'] = 'Prevent AUTH:AD overflow';
$lang['assgnee_list']     = 'unique file extension for assignee list (e.g. assignees)';
$lang['profile_updt']     = 'Sync on user profile changes';
$lang['validate_mail_addr']= 'Validate reporters e-mail address with DNS';
$lang['userinfo_email']   = 'Inform user by mail about issue modifications ? (global switch)';
$lang['mail_add_comment']         = 'inform about new comments';
$lang['mail_modify_comment']      = 'inform about comment modification';
$lang['mail_add_resolution']      = 'inform about resolution entry';
$lang['mail_modify_resolution']   = 'inform about resolution modification';
$lang['mail_modify__description'] = 'inform about modification of initial description';
$lang['shw_mail_addr']    = 'mail address visible instead of user name(to registered users only)';
$lang['shw_assignee_as']  = 'show assignee by login, name or mail address';
$lang['shwtbl_usr']       = 'Configure columns to be shown to user as overview';
$lang['use_captcha']      = 'Use captcha'; 
$lang['severity']         = 'Define severity levels you will use <br> (comma separated, dependency to icon file name)';
$lang['status']           = 'Define issue status levels you will use <br> (comma separated, dependency to icon file name)';
$lang['status_special']   = 'hidden issues (deleted), only single status value allowed !';
$lang['projects']         = 'Define Projects <br> (comma separated)';
$lang['products']         = 'Define Products <br> (comma separated)';
$lang['components']       = 'Define Components <br> (comma separated)';
$lang['assign']           = 'select wiki user groups pre-selected for assigning issues <br> (pipe "|" separated)';
$lang['noStatIMG']        = 'status text instead of icons at Issue list';
$lang['noSevIMG']         = 'severity text instead of icons at Issue list';
$lang['ltdReport']        = 'Exclude these controls from report form';
$lang['ltdListFilters']   = 'Exclude these filter controls from Issue List';
$lang['multi_projects']	  = 'turn on for multi project handling';
$lang['shw_project_col']  = 'show project column';
$lang['global_sort']	    = 'issues global sort order';
$lang['listview_sort']    = 'default list view sort order by ID';