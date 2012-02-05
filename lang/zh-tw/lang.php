<?php
/******************************************************************************
** Chinese language file for IssueTracker
**
** @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
** @author     http://forum.dokuwiki.org/user/8556 = onwe - "IssueTracker works great even on Chinese" 
*******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue']     = '您的需求已提交成功，查詢碼為 #';
$lang['wmsg1'] = '為使認證或需求回報能有效通知您，請輸入有效的email';
$lang['wmsg2'] = '請輸入有效的版次';
$lang['wmsg3'] = '請更好的描述您所遇到的問題';
$lang['wmsg4'] = '&nbsp;若您想提出需求請 <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">登入</a>';
$lang['btn_reportsave'] = '提交';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']       = '需求總數:&nbsp;';
$lang['lbl_scroll']         = '滾動需求清單: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']      = '重要度:&nbsp;';
$lang['lbl_filterstat']     = '進度:&nbsp;';
$lang['btn_go']             = '查詢';
 
$lang['btn_previuos']       = '<<<';
$lang['btn_previuos_title'] = '上一筆需求';
$lang['btn_next']           = '>>>';
$lang['btn_next_title']     = '下一筆需求';

$lang['lbl_showid']         = '直接跳轉指定需求碼的頁面:';
$lang['btn_showid']         = '查詢';
$lang['btn_showid_title']   = '查詢';

$lang['msg_commentfalse']   = '這則帖子已存在，請勿重複提出';
$lang['msg_commenttrue']    = '您的帖子已成功提交為 ID #';
$lang['msg_commentmodtrue'] = '您的帖子已成功修改為 ID #';
$lang['msg_commentmodfalse'] = '帖子沒有改變 ID #';
$lang['msg_pfilemissing']   = '專案不存在: %s 。需求';
$lang['msg_issuemissing']   = '這個需求不存在 ';
$lang['msg_captchawrong']   = '輸入不正確';
$lang['msg_descrmodtrue']   = '描述修改成功.';

$lang['lbl_project']        = '專案:';
$lang['lbl_issueid']        = 'ID:';
$lang['lbl_reporter']       = '提出:';
$lang['lbl_reporterdtls']   = '提出者資料';
$lang['lbl_initdescr']      = '原始需求描述';
$lang['lbl_reportername']   = '提出:';
$lang['lbl_reportermail']   = 'eMail:';
$lang['lbl_reporterphone']  = '手機號碼:';
$lang['lbl_reporteradcontact']  = '聯繫:';
$lang['lbl_symptlinks']     = '症狀檔案連結';
$lang['lbl_cmts_wlog']      = '工作紀錄';
$lang['lbl_cmts_adcmt']     = '新增帖子';
$lang['lbl_cmts_edtres']    = '解決方案';
$lang['btn_add']            = '新增';
$lang['btn_add_title']      = '新增標題';
$lang['btn_mod']            = '更新'; // to submit comment modification
$lang['btn_mod_title']      = '更新標題';
$lang['del_title']          = '刪除帖子';
$lang['lbl_signin']         = '請登入</a> 若您想要帖子的話';       // </a> necessary to close the link tag

$lang['th_project']         = '專案';
$lang['th_id']              = '需求碼';
$lang['th_created']         = '提出時間';
$lang['th_product']         = '項目';
$lang['th_version']         = '版次';
$lang['th_severity']        = '重要度';
$lang['th_status']          = '進度';
$lang['th_username']        = '提出 ';
$lang['th_usermail']        = 'eMail';
$lang['th_userphone']       = '手機';
$lang['th_reporteradcontact']  = '聯絡方式';
$lang['th_title']           = '標題';
$lang['th_descr']           = '描述';
$lang['th_sympt']           = '症狀檔案連結 ';
$lang['th_assigned']        = '指派至'; 
$lang['th_resolution']      = '解決方案';
$lang['th_modified']        = '修改';        
$lang['gen_tab_open']       = '細節';
$lang['descr_tab_mod']      = '修改描述';
$lang['cmt_tab_open']       = '新增';
$lang['cmt_tab_mod']        = '修改帖子';
$lang['rsl_tab_open']       = '創建解決方案';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['issuemod_subject']    = '需求碼 %s 在 %s 有更新喔';
$lang['issuemod_head']       = '親愛的用戶,';
$lang['issuemod_intro']      = '請注意查收以下訊息';
$lang['issuemod_issueid']    = '需求碼　　　:';
$lang['issuemod_status']     = '進度　　　　:';
$lang['issuemod_product']    = '項目　　　　:';
$lang['issuemod_version']    = '版本　　　　:';
$lang['issuemod_severity']   = '重要度　　　:';
$lang['issuemod_creator']    = '提出　　　　:';
$lang['issuemod_title']      = '標題　　　　:';
$lang['issuemod_cmntauthor'] = '發帖　　　　:';
$lang['issuemod_date']       = '提交日　　　:';
$lang['issuemod_cmnt']       = '帖子　　　　:';
$lang['issuemod_see']        = '打開網頁　　: ';
$lang['issuemod_br']         = '祝您有美好的一天';
$lang['issuemod_end']        = '需求追蹤';    // project name placed before this
 
/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject']    = '需求碼 %s 在 %s 已經解決了';
$lang['issue_resolved_intro']      = '您所提出的需求已解決';
$lang['issue_resolved_status']     = '已解決';
$lang['issue_resolved_text']       = '解決方案　　:';
$lang['msg_resolution_true']       = '您的解決方案提交成功';
 
/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']    = '有新的 %s 需求在 %s 上 : %s (%s)';
$lang['issuenew_head']       = '主管您好,';
$lang['issuenew_intro']      = '使用者有新的需求，請指派對應人員';
$lang['issuenew_descr']      = '描述:  ';

/******************************************************************************/
/* deviations from before for send an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']    = '需求碼 %s 被指派給您了';
$lang['issueassigned_head']       = '您好,';
$lang['issueassigned_intro']      = '您被指派對應下列的使用者需求:';

/******************************************************************************/ 