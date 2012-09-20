<?php
/**
 * Metadata for configuration manager plugin
 * Additions for the issuetracker plugin
 *
 * @author   Taggic@t-online.de 
 */

$meta['d_format']         = array('string');
$meta['send_email']       = array('onoff');   // Send email off by default
$meta['mail_templates']       = array('onoff');   // Send html email off by default
$meta['email_address']    = array('string');  // Who will be informed about new issues ? 
$meta['registered_users'] = array('onoff');   // only registered users with edit permission on issue tracker page are allowed to create reports and add comments     
$meta['auth_ad_overflow'] = array('onoff');
$meta['userinfo_email']           = array('onoff');   // Send email off by default
$meta['mail_add_comment']         = array('onoff');
$meta['mail_modify_comment']      = array('onoff');
$meta['mail_add_resolution']      = array('onoff');
$meta['mail_modify_resolution']   = array('onoff');
$meta['mail_modify__description'] = array('onoff');
$meta['shw_mail_addr']  = array('onoff');   // show mail address instead of user names to registered users
$meta['shwtbl_usr']	    =	array('multicheckbox', '_choices' => array('product', 'version', 'severity', 'created', 'status', 'user', 'title', 'description', 'assigned', 'resolution', 'modified'));
$meta['use_captcha']    = array('onoff');   // Use captcha on by default
$meta['severity']       = array('string');  // Configure allowed severities
$meta['status']         = array('string');  // Configure allowed status info
$meta['status_special'] = array('string');  // hidden issues, only single status value allowed !
$meta['products']       = array('string');  // Configure Products coverd by one project
//$meta['versions']       = array('string');// Configure allowed versions of defined products
$meta['assign']         = array('string');  // Configure groups usable for issue asignments
$meta['noStatIMG']      = array('onoff');   // define if status text instead of pictures will be displayed at Issue list
$meta['noSevIMG']       = array('onoff');   // define if severity text instead of pictures will be displayed at Issue list
$meta['ltdReport']      = array('multicheckbox', '_choices' => array('Version', 'User phone', 'Add contact', 'Severity', 'Symptom link 1', 'Symptom link 2', 'Symptom link 3')); // default = false or a comma separated list of controls to be hidden