<?php
/**
 * Options for the issuetracker plugin
 */




$conf['d_format']          = 'Y-m-d G:i:s';
$conf['it_data']           = '';                       // issue tracker data folder (empty = metaFN)
$conf['upload']            = 0;                        // allow symptom file uploads
$conf['max_fsize']         = '1048576';                // limit file size for uploads in Byte (default 1MB)
$conf['ip_blocked']        = 1;                        // turn on the anti-spam-bot savety feature
$conf['ip_blockd_time']    = 5;                        // each ip will be blocked for 10 minutes before another upload can be initiated
$conf['send_email']        = 0;                        // Send email off by default 
$conf['mail_templates']    = 0;                        // Send html email off by default
$conf['email_address']     = 'email@yourdomain.com';   // Who will be informed about new issues ?
$conf['registered_users']  = 1;                        // only registered users with edit permission on issue tracker page are allowed to create reports and add comments
$conf['auth_ad_overflow']  = 0;
$conf['assgnee_list']      = '';                        // needs unique file extension like assignees
$conf['profile_updt']      = 1;                        // Sync on user profile changes
$conf['validate_mail_addr']= 1;                        // validate mail address
$conf['userinfo_email']              = 1;              // Global mail switch 
$conf['mail_add_comment']            = 1;
$conf['mail_modify_comment']         = 1;
$conf['mail_add_resolution']         = 1;
$conf['mail_modify_resolution']      = 1;
$conf['mail_modify__description']    = 1;
$conf['shw_mail_addr']     = 1;                        // show mail address instead of user names to registered users
$conf['shw_assignee_as']   = 'login';                  // show assignee by login, name or mail address
$conf['shwtbl_usr']        = 'created,product,version,severity,status,title,modified,resolution';                       // configure columns for user view of issue list 
$conf['use_captcha']       = 1;                        // Use captcha on by default 
$conf['severity']          = 'Query,Minor,Medium,Major,Critical,Feature Request,';  // Configure allowed severities
$conf['status']            = 'New,Assigned,External Pending,In Progress,Solved,Canceled,Double,Deleted'; // Configure allowed status info
$conf['status_special']    = 'Deleted';                // hidden issues, only single status value allowed !
$conf['projects']          = '';                       // Configure list of projects, empty by default
$conf['products']          = '';                       // Configure list of products, empty by default
$conf['components']        = '';                       // Configure list of components, empty by default
$conf['assign']            = 'admin';                  // Configure groups usable for issue asignments
$conf['noStatIMG']         = 0;                        // define if status text instead of pictures will be displayed at Issue list
$conf['noSevIMG']          = 0;                        // define if severity text instead of pictures will be displayed at Issue list
$conf['ltdReport']         = '';                       // empty by default
$conf['ltdListFilters']    = 'Filter Severity,Filter Status,Filter Product,Filter Version,Filter Component,Filter Test blocking,Filter Assignee,Filter Reporter,MyIssues,Sort by';                       // empty by default
$conf['multi_projects']	   = 0;                        // global switch to tell IssueTracker that syntax parameter "project" to be ignored. However, the syntax has to contain the parameter.
$conf['shw_project_col']	 = 0;                        // show an additional column for the project name per issue on ListView
$conf['global_sort']	     = "SORT_DESC";              // sort order of selected key
$conf['listview_sort']	   = "sortfirstdesc";          // default listview sort order
