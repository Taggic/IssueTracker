<?php
/**
 * Options for the issuetracker plugin
 */

$conf['d_format']          = 'Y-m-d G:i:s';
$conf['send_email']        = 0;                                                                  // Send email off by default 
$conf['email_address']     = 'email@yourdomain.com';                                             // Who will be informed about new issues ?
$conf['registered_users']  = true;                                                               // only registered users with edit permission on issue tracker page are allowed to create reports and add comments
$conf['userinfo_email']    = 0;                                                                  // Send email off by default 
$conf['shwtbl_usr']        = '';                                                                 // configure columns for user view of issue list 
$conf['use_captcha']       = 1;                                                                  // Use captcha on by default 
$conf['severity']          = 'Query,Minor,Medium,Major,Critical,Feature Request,';               // Configure allowed severities
$conf['status']            = 'New,Assigned,External Pending,In Progress,Solved,Canceled,Double'; // Configure allowed status info
$conf['products']          = 'ProdA, ProdB';                                                     // Configure Products coverd by one project
//$conf['versions']          = '1.0,1.5,2.0,2.5|1.0,1.5,2.0,2.5';                                // Configure allowed versions of all defined products
$conf['assign']            = 'admin';                                                            // Configure groups usable for issue asignments
$conf['noStatIMG']         = false;                                                              // define if status text instead of pictures will be displayed at Issue list
$conf['noSevIMG']          = false;                                                              // define if severity text instead of pictures will be displayed at Issue list
$conf['ltdReport']         = '';                                                                 // empty by default
