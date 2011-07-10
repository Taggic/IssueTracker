<?php
/**
 * Options for the issuetracker plugin
 */

$conf['send_email']    = 0;                                                                  // Send email off by default 
$conf['email_address'] = 'email@yourdomain.com';                                             // should unregistred users be able to comment?
$conf['use_captcha']   = 1;                                                                  // Use captcha on by default 
$conf['severity']      = 'Query,Minor,Medium,Major,Critical,Feature Request,';               // added by Taggic on 2011-07-08
$conf['status']        = 'New,Assigned,External Pending,In Progress,Solved,Canceled,Double'; // added by Taggic on 2011-07-08
$conf['versions']      = '1.0., 1.1,1.5,2.0,2.5';                                            // added by Taggic on 2011-07-08
$conf['assigns']       ='@ADMIN,@USER';                                                      // added by Taggic on 2011-07-08
//Setup VIM: ex: et ts=2 enc=utf-8 :
