<?php
/**
 * English language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Taggic <taggic@t-online.de>
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue'] = 'Your report has been successfully stored as issue #';
$lang['wmsg1'] = 'Please enter valid eMail address, preferrably your own, for clarifications and/or feedback regarding your reported issue.';
$lang['wmsg2'] = 'Please enter a valid product version to relate this issue properly.';
$lang['wmsg3'] = 'Please provide a better description of your issue.';
$lang['wmsg4'] = '&nbsp;Please <a href="?do=login&amp" class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">Sign in</a> if you want to report an issue.';
$lang['wmsg5'] = 'Please provide a descriptive issue title.';
$lang['wmsg6'] = 'Upload Error: The max file-size of %s bytes was exceeded';
$lang['wmsg7'] = 'Upload Error: The file extension is invalid.';
$lang['wmsg8'] = 'Upload Error: The Mime-Type of the file is faulty or not supported.';
$lang['wmsg9'] = 'Spam security: Upload from your IP is blocked for at least another %s minutes.';
$lang['lbl_symptomupload'] = 'Symptom file upload:';
$lang['btn_reportsave'] = 'Submit';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']           = 'Quantity of Issues:&nbsp;';
$lang['lbl_scroll']             = 'Scroll Issue List: &nbsp;';
$lang['lbl_filtersev']          = 'Filter Severity:&nbsp;';
$lang['lbl_filterstat']         = 'Filter Status:&nbsp;';
$lang['lbl_filterprod']         = 'Filter Product:&nbsp;';
$lang['lbl_filtervers']         = 'Filter Version:&nbsp;';
$lang['lbl_filtercomp']         = 'Filter Component:&nbsp;';
$lang['lbl_filterblock']        = 'Filter Test blocking:&nbsp;';
$lang['lbl_filterassi']         = 'Filter Assignee:&nbsp;';
$lang['lbl_filterreporter']     = 'Filter Reporter:&nbsp;';
$lang['cbx_myissues']           = 'MyIssues:&nbsp;';
$lang['btn_go']                 = 'Go';

$lang['btn_previuos']           = '<<<';
$lang['btn_previuos_title']     = 'previous Issues';
$lang['btn_next']               = '>>>';
$lang['btn_next_title']         = 'next Issues';

$lang['lbl_showid']             = 'Show details of Issue:';
$lang['btn_showid']             = 'Show';
$lang['btn_showid_title']       = 'Show';

$lang['lbl_sort']               = 'Sort by:';
$lang['btn_sort']               = 'Sort';
$lang['btn_sort_title']         = 'Sort the issue list globally by array key.';

$lang['msg_commentfalse']       = 'This comment does already exist and was not added again.';
$lang['msg_commenttrue']        = 'Your comment has been successfully stored with ID #';
$lang['msg_wroundtrue']         = 'Your workaround has been successfully stored';
$lang['msg_commentmodtrue']     = 'Your comment was successfully modified as ID #';
$lang['msg_commentdeltrue']     = 'Your comment #%s successfully deleted.';
$lang['msg_commentmodfalse']    = 'No diff of comment ID #';
$lang['msg_pfilemissing']       = 'Project file does not exist: %s.issues. ';
$lang['msg_issuemissing']       = 'There does no Issue exist with ID ';
$lang['msg_inotexisting1']      = 'The issue does not exist at the given project. <br /> Project = %s <br /> Issue ID = %s <br /> <a href="%sdoku.php?id=%s"> << back</a>';
$lang['msg_captchawrong']       = 'Wrong answer to the antispam question.';
$lang['msg_descrmodtrue']       = 'Description successfully modified.';
$lang['msg_slinkmodtrue']       = 'Symptom links modified successfully.';
$lang['msg_severitymodtrue']    = 'Severity modified successfully.';
$lang['msg_statusmodtrue']      = 'Status modified successfully.';
$lang['msg_addFollower_true']   = 'ID: %s -> Follower added: ';
$lang['msg_rmvFollower_true']   = 'ID: %s -> Follower removed: ';
$lang['msg_addFollower_failed'] = 'ID: %s -> failed to update Follower: ';
$lang['itd_follower']           = '(Follower: %s)';
$lang['msg_showCase']           = 'This Issue ID exist on multiple projects. Please choose which one you mean:';

$lang['lbl_project']            = 'Project:';
$lang['lbl_issueid']            = 'ID:';
$lang['lbl_reporter']           = 'Creator:';
$lang['lbl_reporterdtls']       = 'Creator Details';
$lang['lbl_initdescr']          = 'Initial description';
$lang['lbl_reportername']       = 'Name:';
$lang['lbl_reportermail']       = 'eMail:';
$lang['lbl_reporterphone']      = 'Phone:';
$lang['lbl_reporteradcontact']  = 'Add contact:';
$lang['lbl_symptlinks']         = 'Links to symptom files';
$lang['lbl_cmts_wlog']          = 'Comments (work log)';
$lang['lbl_cmts_adcmt']         = 'Add a new comment';
$lang['lbl_cmts_edtres']        = 'Resolution';
$lang['btn_add']                = 'Add';
$lang['btn_add_title']          = 'Add';
$lang['btn_mod']                = 'Update'; // to submit comment modification
$lang['btn_mod_title']          = 'Update';
$lang['del_title']              = 'Delete this comment';
$lang['lbl_signin']             = 'Sign in</a> if you want to add a comment or resolution note.'; // </a> necessary to close the link tag
$lang['lbl_please']             = 'Please ';
$lang['lbl_lessPermission']     = 'Your permission level is to low. Please contact the admin.';
$lang['lbl_workaround']         = 'Workaround';

$lang['th_project']             = 'Project';
$lang['th_id']                  = 'Id';
$lang['th_created']             = 'Created';
$lang['th_product']             = 'Product';
$lang['th_components']          = 'Component';
$lang['th_tblock']              = 'Test blocking';
$lang['th_tversion']            = 'Target version';
$lang['th_begin']               = 'Begin';
$lang['th_deadline']            = 'Deadline';
$lang['th_progress']            = 'Progress in %';
$lang['th_version']             = 'Version';
$lang['th_severity']            = 'Severity';
$lang['th_status']              = 'Status';
$lang['th_user_name']           = 'Creator';
$lang['th_usermail']            = 'Creator email';
$lang['th_userphone']           = 'Creator phone';
$lang['th_reporteradcontact']   = 'Add contact';
$lang['th_title']               = 'Title';
$lang['th_description']         = 'Issue Description';
$lang['th_sympt']               = 'Symptom link ';
$lang['th_assigned']            = 'Assigned to'; 
$lang['th_resolution']          = 'Resolution';
$lang['th_modified']            = 'Modified';
$lang['th_showmodlog']          = 'Status history';
$lang['h_modlog']               = 'Status modification history of #';
$lang['mod_valempty']           = '[deleted]';
$lang['back']                   = 'back';
$lang['gen_tab_open']           = 'Details';
$lang['descr_tab_mod']          = 'Modify';
$lang['cmt_tab_open']           = 'add Comment';
$lang['cmt_tab_mod']            = 'modify Comment';
$lang['rsl_tab_open']           = 'add / modify Resolution';
$lang['dtls_usr_hidden']        = 'user details hidden';
$lang['dtls_reporter_hidden']   = 'Creator';
$lang['dtls_follower_hidden']   = 'Follower';
$lang['dtls_assignee_hidden']   = 'Assignee';
$lang['dtls_foreigner_hidden']  = 'Foreigner';
$lang['minor_mod']              = 'Minor change';
$lang['minor_mod_cbx_title']    = 'prevent sending eMails upon cosmetic changes';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['cmnt_new_subject']       = 'Issue #%s on %s: new comment added'; // $issue['id'], $project
$lang['cmnt_mod_subject']       = 'Issue #%s on %s: comment modified';  // $issue['id'], $project
$lang['cmnt_del_subject']       = 'Issue #%s on %s: comment deleted';   // $issue['id'], $project
$lang['cmnt_wa_subject']        = 'Issue #%s on %s: workaround provided';   // $issue['id'], $project
$lang['issuemod_subject']       = 'Issue #%s on %s: %s'; //$issue['id'], $project, $column
$lang['issuemod_head']          = 'Dear user,';
$lang['issuemod_intro']         = 'Your reported issue was modified.';
$lang['cmt_del_intro']          = 'A comment was deleted.';
$lang['issuemod_issueid']       = 'ID:           ';
$lang['issuemod_status']        = 'Status:       ';
$lang['issuemod_product']       = 'Product:      ';
$lang['issuemod_version']       = 'Version:      ';
$lang['issuemod_severity']      = 'Severity:     ';
$lang['issuemod_creator']       = 'Creator:      ';
$lang['issuemod_assignee']      = 'Assignee:     ';
$lang['issuemod_title']         = 'Title:        ';
$lang['issuemod_cmntauthor']    = 'Comment by:   ';
$lang['issuemod_date']          = 'submitted on: ';
$lang['issuemod_cmnt']          = 'Comment:      ';
$lang['issuemod_see']           = 'see details:  ';
$lang['issuemod_br']            = 'best regards';
$lang['issuemod_end']           = ' Issue Tracker';    // project name placed before this
$lang['issuedescrmod_subject']  = 'Issue #%s on %s: initial description modified'; // $issue['id'], $project
$lang['issuemod_changes']       = 'The issue changed on %s from %s to %s.'; //$column, $old_value, $new_value
$lang['btn_upd_addinfo']        = 'Save';
/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject'] = 'Issue #%s on %s was resolved';
$lang['issue_resolved_intro']   = 'Your reported issue was resolved.';
$lang['issue_resolved_status']  = 'Solved';
$lang['issue_resolved_text']    = 'Solution:   ';
$lang['msg_resolution_true']    = 'Your Resolution was added sucessfully to ID';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']       = '%s issue reported for %s on Product: %s (%s)';
$lang['issuenew_head']          = 'Dear admin,';
$lang['issuenew_intro']         = 'a new issue was created for the project:';
$lang['issuenew_descr']         = 'Description:  ';

/******************************************************************************/
/* deviations from before for sending an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']  = 'Issue #%s on %s assigned to you'; //$issue['id'], $project
$lang['issueassigned_head']     = 'Hi,';
$lang['issueassigned_intro']    = 'you are assigned to following Issue:';
$lang['it__none']               = '--';
/******************************************************************************/
/* following text is related to search feature                                
*/
$lang['lbl_search']             = 'Search for following:&nbsp;&nbsp;';
$lang['btn_search']             = 'Start';
$lang['btn_search_title']       = 'Search';
$lang['search_hl1']             = '<h1>Search</h1>';
$lang['search_txt1']            = "You can find the results of your search below. Please remember that this is a simple search, which is looking for single word items and does not understand query logics.";
$lang['search_hl2']             = '<h2>Result</h2>';
$lang['search_Issue']           = 'Issue';
$lang['search_Comment']         = 'Comment';
$lang['search_Type']            = 'Type';
$lang['search_ID']              = 'ID';
$lang['search_Subject']         = 'Subject';

/******************************************************************************/
$lang['table_kit_OK']           = 'OK';
$lang['table_kit_Cancel']       = ' Cancel';
$lang['yes']                    = ' YES';
$lang['no']                    = ' NO';

/******************************************************************************/
// Report Manager
$lang['it_btn_rprt_mngr']       = 'Create Report';
