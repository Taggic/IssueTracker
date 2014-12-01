<?php
/**
 * Metadata for configuration manager plugin
 * Additions for the issuetracker plugin
 *
 * @author   Taggic@t-online.de 
 */

$meta['d_format']          = array('string');
$meta['it_data']           = array('string');  // issue tracker data folder (empty = metaFN)
$meta['upload']            = array('onoff');   // allow symptom file uploads
$meta['max_fsize']         = array('string');  // limit file size for uploads in Byte (default 1MB)
$meta['ip_blocked']        = array('onoff');   // turn on the anti-spam-bot savety feature
$meta['ip_blockd_time']    = array('string');  // each ip will be blocked for 3 minutes before another upload can be initiated
$meta['send_email']        = array('onoff');   // Send email off by default
$meta['mail_templates']    = array('onoff');   // Send html email off by default
$meta['email_address']     = array('string');  // Who will be informed about new issues ? 
$meta['registered_users']  = array('onoff');   // only registered users with edit permission on issue tracker page are allowed to create reports and add comments     
$meta['auth_ad_overflow']  = array('onoff');
$meta['assgnee_list']      = array('string');  // needs unique file extension like assignees
$meta['profile_updt']      = array('onoff');   // Sync on user profile changes
$meta['validate_mail_addr']= array('onoff');   // validate mail address
$meta['userinfo_email']           = array('onoff');   // Send email off by default
$meta['mail_add_comment']         = array('onoff');
$meta['mail_modify_comment']      = array('onoff');
$meta['mail_add_resolution']      = array('onoff');
$meta['mail_modify_resolution']   = array('onoff');
$meta['mail_modify__description'] = array('onoff');
$meta['shw_mail_addr']  = array('onoff');   // show mail address instead of user names to registered users
$meta['shw_assignee_as']= array('multichoice', '_choices' => array('login', 'name', 'mail'));// show assignee by login, name or mail address
$meta['shwtbl_usr']	    =	array('multicheckbox', '_choices' => array('created', 'product', 'version', 'severity', 'status', 'title', 'description', 'user_name', 'assigned', 'modified', 'resolution'));
$meta['use_captcha']    = array('onoff');   // Use captcha on by default
$meta['severity']       = array('string');  // Configure allowed severities
$meta['status']         = array('string');  // Configure allowed status info
$meta['status_special'] = array('string');  // hidden issues, only single status value allowed !
$meta['projects']       = array('string');  // Configure list of projects
$meta['products']       = array('string');  // Configure list of products
$meta['components']     = array('string');  // Configure list of components
$meta['assign']         = array('string');  // Configure groups usable for issue asignments
$meta['noStatIMG']      = array('onoff');   // define if status text instead of pictures will be displayed at Issue list
$meta['noSevIMG']       = array('onoff');   // define if severity text instead of pictures will be displayed at Issue list
$meta['ltdReport']      = array('multicheckbox', '_choices' => array('Version', 'Components', 'Test blocking', 'User phone', 'Add contact', 'Severity', 'Symptom link 1', 'Symptom link 2', 'Symptom link 3')); // default = false or a comma separated list of controls to be hidden
$meta['ltdListFilters'] = array('multicheckbox', '_choices' => array('Filter Severity','Filter Status','Filter Product','Filter Version','Filter Component','Filter Test blocking','Filter Assignee','Filter Reporter','MyIssues','Sort by')); // default = false or a comma separated list of filter controls to be hidden
$meta['multi_projects']	= array('onoff');   // global switch to tell IssueTracker that syntax parameter "project" to be ignored. However, the syntax has to contain the parameter.
$meta['shw_project_col']= array('onoff');   // show an additional column for the project name per issue on ListView
$meta['global_sort']	  = array('multichoice', '_choices' => array('SORT_DESC', 'SORT_ASC'));  // global sort order of selected key
$meta['listview_sort']	= array('multichoice', '_choices' => array('sortfirstasc', 'sortfirstdesc'));  // default listview sort order
$meta['wysiwyg']	      = array('onoff');   // 