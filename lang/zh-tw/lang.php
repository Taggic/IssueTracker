<?php
/**
 * Chinese language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     http://forum.dokuwiki.org/user/8556 = onwe - "IssueTracker works great even on Chinese" 
 */
/******************************************************************************/
 // Issue Report controls
 $lang['msg_reporttrue']     = '?????????,???? #';
 $lang['wmsg1'] = '???????????????,??????email';
 $lang['wmsg2'] = '????????';
 $lang['wmsg3'] = '?????????????';
 $lang['wmsg4'] = '&nbsp;???????? <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">??</a>';
 $lang['btn_reportsave'] = '??';
 // further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
 // Issue List controls
 $lang['lbl_issueqty']       = '????:&nbsp;';
 $lang['lbl_scroll']         = '??????: &nbsp;&nbsp;&nbsp;';
 $lang['lbl_filtersev']      = '???:&nbsp;';
 $lang['lbl_filterstat']     = '??:&nbsp;';
 $lang['btn_go']             = '??';
 
$lang['btn_previuos']       = '<<<';
 $lang['btn_previuos_title'] = '?????';
 $lang['btn_next']           = '>>>';
 $lang['btn_next_title']     = '?????';
 
$lang['lbl_showid']         = '????????????:';
 $lang['btn_showid']         = '??';
 $lang['btn_showid_title']   = '??';
 
$lang['msg_commentfalse']   = '???????,??????';
 $lang['msg_commenttrue']    = '?????????? ID #';
 $lang['msg_commentmodtrue'] = '?????????? ID #';
 $lang['msg_commentmodfalse'] = '?????? ID #';
 $lang['msg_pfilemissing']   = '?????: %s ???';
 $lang['msg_issuemissing']   = '??????? ';
 $lang['msg_captchawrong']   = '?????';
 $lang['msg_descrmodtrue']   = '??????.';
 
$lang['lbl_project']        = '??:';
 $lang['lbl_issueid']        = 'ID:';
 $lang['lbl_reporter']       = '??:';
 $lang['lbl_reporterdtls']   = '?????';
 $lang['lbl_initdescr']      = '??????';
 $lang['lbl_reportername']   = '??:';
 $lang['lbl_reportermail']   = 'eMail:';
 $lang['lbl_reporterphone']  = '????:';
 $lang['lbl_reporteradcontact']  = '??:';
 $lang['lbl_symptlinks']     = '??????';
 $lang['lbl_cmts_wlog']      = '????';
 $lang['lbl_cmts_adcmt']     = '????';
 $lang['lbl_cmts_edtres']    = '????';
 $lang['btn_add']            = '??';
 $lang['btn_add_title']      = '????';
 $lang['btn_mod']            = '??'; // to submit comment modification
 $lang['btn_mod_title']      = '????';
 $lang['del_title']          = '????';
 $lang['lbl_signin']         = '???</a> ????????';       // </a> necessary to close the link tag
 
$lang['th_project']         = '??';
 $lang['th_id']              = '???';
 $lang['th_created']         = '????';
 $lang['th_product']         = '??';
 $lang['th_version']         = '??';
 $lang['th_severity']        = '???';
 $lang['th_status']          = '??';
 $lang['th_username']        = '?? ';
 $lang['th_usermail']        = 'eMail';
 $lang['th_userphone']       = '??';
 $lang['th_reporteradcontact']  = '????';
 $lang['th_title']           = '??';
 $lang['th_descr']           = '??';
 $lang['th_sympt']           = '?????? ';
 $lang['th_assigned']        = '???'; 
$lang['th_resolution']      = '????';
 $lang['th_modified']        = '??';        
$lang['gen_tab_open']       = '??';
 $lang['descr_tab_mod']      = '????';
 $lang['cmt_tab_open']       = '??';
 $lang['cmt_tab_mod']        = '????';
 $lang['rsl_tab_open']       = '??????';
 /******************************************************************************/
 /* send an e-mail to user due to issue modificaion
 /* _emailForIssueMod
 */                            
$lang['issuemod_subject']    = '??? %s ? %s ????';
 $lang['issuemod_head']       = '?????,';
 $lang['issuemod_intro']      = '?????????';
 $lang['issuemod_issueid']    = '???   :';
 $lang['issuemod_status']     = '??    :';
 $lang['issuemod_product']    = '??    :';
 $lang['issuemod_version']    = '??    :';
 $lang['issuemod_severity']   = '???   :';
 $lang['issuemod_creator']    = '??    :';
 $lang['issuemod_title']      = '??    :';
 $lang['issuemod_cmntauthor'] = '??    :';
 $lang['issuemod_date']       = '???   :';
 $lang['issuemod_cmnt']       = '??    :';
 $lang['issuemod_see']        = '????  : ';
 $lang['issuemod_br']         = '????????';
 $lang['issuemod_end']        = '????';    // project name placed before this
 
/******************************************************************************/
 /* send an e-mail to user due to issue set to resolved on details
 /* _emailForResolutionMod
 */                            
$lang['issue_resolved_subject']    = '??? %s ? %s ?????';
 $lang['issue_resolved_intro']      = '??????????';
 $lang['issue_resolved_status']     = '???';
 $lang['issue_resolved_text']       = '????  :';
 $lang['msg_resolution_true']       = '??????????';
 
/******************************************************************************/
 /* deviations from before for send an e-mail to admin due to new issue created
 /* _emailForNewIssue
 */
 $lang['issuenew_subject']    = '??? %s ??? %s ? : %s (%s)';
 $lang['issuenew_head']       = '????,';
 $lang['issuenew_intro']      = '????????,???????';
 $lang['issuenew_descr']      = '??:  ';
 
/******************************************************************************/
 /* deviations from before for send an e-mail to assignee
 /* _emailForNewIssue
 */
 $lang['issueassigned_subject']    = '??? %s ??????';
 $lang['issueassigned_head']       = '??,';
 $lang['issueassigned_intro']      = '??????????????:';
 
/******************************************************************************/ 

