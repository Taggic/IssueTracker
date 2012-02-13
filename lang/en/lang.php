<?php
/**
 * English language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Taggic <taggic@t-online.de>
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue']     = 'Your report has been successfully stored as issue #';
$lang['wmsg1'] = 'Please enter valid eMail address, preferrably your own, for clarifications and/or feedback regarding your reported issue.';
$lang['wmsg2'] = 'Please enter a valid product version to relate this issue properly.';
$lang['wmsg3'] = 'Please provide a better description of your issue.';
$lang['wmsg4'] = '&nbsp;Please <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">Sign in</a> if you want to report an issue.';
$lang['wmsg5'] = 'Please provide a descriptive issue title.';
$lang['btn_reportsave'] = 'Submit';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']       = 'Quantity of %s Issues:&nbsp;';
$lang['lbl_scroll']         = 'Scroll issue List: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']      = 'Filter Severity:&nbsp;';
$lang['lbl_filterstat']     = 'Filter Status:&nbsp;';
$lang['btn_go']             = 'Go';

$lang['btn_previuos']       = '<<<';
$lang['btn_previuos_title'] = 'previous Issues';
$lang['btn_next']           = '>>>';
$lang['btn_next_title']     = 'next Issues';

$lang['lbl_showid']         = 'Show details of Issue:';
$lang['btn_showid']         = 'Show';
$lang['btn_showid_title']   = 'Show';

$lang['msg_commentfalse']   = 'This comment does already exist and was not added again.';
$lang['msg_commenttrue']    = 'Your comment has been successfully stored with ID #';
$lang['msg_commentmodtrue'] = 'Your comment was successfully modified as ID #';
$lang['msg_commentdeltrue'] = 'Your comment #%s successfully deleted.';
$lang['msg_commentmodfalse'] = 'No diff of comment ID #';
$lang['msg_pfilemissing']   = 'Project file does not exist: %s .issues. ';
$lang['msg_issuemissing']   = 'There does no Issue exist with ID ';
$lang['msg_captchawrong']   = 'Wrong answer to the antispam question.';
$lang['msg_descrmodtrue']   = 'Description successfully modified.';
$lang['msg_slinkmodtrue']   = 'Symptom links modified successfully.';

$lang['lbl_project']        = 'Project:';
$lang['lbl_issueid']        = 'ID:';
$lang['lbl_reporter']       = 'Reported by:';
$lang['lbl_reporterdtls']   = 'Reporter Details';
$lang['lbl_initdescr']      = 'Initial description';
$lang['lbl_reportername']   = 'Name:';
$lang['lbl_reportermail']   = 'eMail:';
$lang['lbl_reporterphone']  = 'Phone:';
$lang['lbl_reporteradcontact']  = 'Add contact:';
$lang['lbl_symptlinks']     = 'Links to symptom files';
$lang['lbl_cmts_wlog']      = 'Comments (work log)';
$lang['lbl_cmts_adcmt']     = 'Add a new comment';
$lang['lbl_cmts_edtres']    = 'Resolution';
$lang['btn_add']            = 'Add';
$lang['btn_add_title']      = 'Add';
$lang['btn_mod']            = 'Update'; // to submit comment modification
$lang['btn_mod_title']      = 'Update';
$lang['del_title']          = 'Delete this comment';
$lang['lbl_signin']         = 'Sign in</a> if you want to add a comment or resolution note.';       // </a> necessary to close the link tag
$lang['lbl_please']         = 'Please ';

$lang['th_project']         = 'Project';
$lang['th_id']              = 'Id';
$lang['th_created']         = 'Created';
$lang['th_product']         = 'Product';
$lang['th_version']         = 'Version';
$lang['th_severity']        = 'Severity';
$lang['th_status']          = 'Status';
$lang['th_username']        = 'User name ';
$lang['th_usermail']        = 'User email';
$lang['th_userphone']       = 'User phone';
$lang['th_reporteradcontact']  = 'Add contact';
$lang['th_title']           = 'Title';
$lang['th_description']     = 'Issue Description';
$lang['th_sympt']           = 'Symptom link ';
$lang['th_assigned']        = 'Assigned to'; 
$lang['th_resolution']      = 'Resolution';
$lang['th_modified']        = 'Modified';
$lang['th_showmodlog']      = 'Status history';
$lang['h_modlog']           = 'Status modification history of #';
$lang['back']               = 'back';
$lang['gen_tab_open']       = 'Details';
$lang['descr_tab_mod']      = 'Modify';
$lang['cmt_tab_open']       = 'add Comment';
$lang['cmt_tab_mod']        = 'modify Comment';
$lang['rsl_tab_open']       = 'add / modify Resolution';
$lang['dtls_usr_hidden']       = 'user details hidden';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['cmnt_new_subject']    = 'New comment added to Service Request %s on %s ';
$lang['cmnt_mod_subject']    = 'Comment modfification at Service Request %s on %s ';
$lang['cmnt_del_subject']    = 'Comment deleted of Service Request %s on %s ';
$lang['cmt_del_intro']       = 'A comment was deleted.';
$lang['issuemod_subject']    = 'Service Request %s on %s was modified';
$lang['issuemod_head']       = 'Dear user,';
$lang['issuemod_intro']      = 'Your reported issue got a new comment.';
$lang['issuemod_issueid']    = 'ID:     ';
$lang['issuemod_status']     = 'Status:   ';
$lang['issuemod_product']    = 'Product:   ';
$lang['issuemod_version']    = 'Version:   ';
$lang['issuemod_severity']   = 'Severity:   ';
$lang['issuemod_creator']    = 'Creator:   ';
$lang['issuemod_title']      = 'Title:   ';
$lang['issuemod_cmntauthor'] = 'Comment by:  ';
$lang['issuemod_date']       = 'submitted on: ';
$lang['issuemod_cmnt']       = 'Comment:  ';
$lang['issuemod_see']        = 'see details:  ';
$lang['issuemod_br']         = 'best regards';
$lang['issuemod_end']        = ' Issue Tracker';    // project name placed before this
$lang['issuedescrmod_subject'] = 'Initial Description of issue %s on %s was modified';

/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject']    = 'Service Request %s on %s was resolved';
$lang['issue_resolved_intro']      = 'Your reported issue was resolved.';
$lang['issue_resolved_status']     = 'Solved';
$lang['issue_resolved_text']       = 'Solution:   ';
$lang['msg_resolution_true']       = 'Your Resolution was added sucessfully to ID';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']    = '%s issue reported for %s on Product: %s (%s)';
$lang['issuenew_head']       = 'Dear admin,';
$lang['issuenew_intro']      = 'a new issue was created for the project:';
$lang['issuenew_descr']      = 'Description:  ';

/******************************************************************************/
/* deviations from before for sending an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']    = ' issue %s assigned to you';
$lang['issueassigned_head']       = 'Hi,';
$lang['issueassigned_intro']      = 'you are assigned to following service request:';

/******************************************************************************/
