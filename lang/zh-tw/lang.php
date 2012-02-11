<?php
/**
 * English language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Taggic <taggic@t-online.de>
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue']     = '�z���ݨD�w���榨�\�A�d�߽X�� #';
$lang['wmsg1'] = '���ϻ{�ҩλݨD�^���঳�ĳq���z�A�п�J���Ī�email';
$lang['wmsg2'] = '�п�J���Ī�����';
$lang['wmsg3'] = '�Ч�n���y�z�z�ҹJ�쪺���D';
$lang['wmsg4'] = '&nbsp;�Y�z�Q���X�ݨD�� <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">�n�J</a>';
$lang['wmsg5'] = '�д��Ѥ@�Ӧ��Ī����D';
$lang['btn_reportsave'] = '����';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']       = '�ݨD�`��:&nbsp;';
$lang['lbl_scroll']         = '�u�ʻݨD�M��: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']      = '���n��:&nbsp;';
$lang['lbl_filterstat']     = '�i��:&nbsp;';
$lang['btn_go']             = '�d��';

$lang['btn_previuos']       = '<<<';
$lang['btn_previuos_title'] = '�W�@���ݨD';
$lang['btn_next']           = '>>>';
$lang['btn_next_title']     = '�U�@���ݨD';

$lang['lbl_showid']         = '����������w�ݨD�X������:';
$lang['btn_showid']         = '�d��';
$lang['btn_showid_title']   = '�d��';

$lang['msg_commentfalse']   = '�o�h���l�w�s�b�A�Фŭ��ƴ��X';
$lang['msg_commenttrue']    = '�z�����l�w���\���欰 ID #';
$lang['msg_commentmodtrue'] = '�z�����l�w���\�קאּ ID #';
$lang['msg_commentdeltrue'] = '�z�����l #%s �w�g���\�R���F';
$lang['msg_commentmodfalse'] = '���l�S������ ID #';
$lang['msg_pfilemissing']   = '�M�פ��s�b: %s �C�ݨD';
$lang['msg_issuemissing']   = '�o�ӻݨD���s�b ';
$lang['msg_captchawrong']   = '��J�����T';
$lang['msg_descrmodtrue']   = '�y�z�ק令�\';
$lang['msg_slinkmodtrue']   = '�g���ɮ׳s���ק令�\';

$lang['lbl_project']        = '�M��:';
$lang['lbl_issueid']        = 'ID:';
$lang['lbl_reporter']       = '���X:';
$lang['lbl_reporterdtls']   = '���X�̸��';
$lang['lbl_initdescr']      = '��l�ݨD�y�z';
$lang['lbl_reportername']   = '���X:';
$lang['lbl_reportermail']   = 'eMail:';
$lang['lbl_reporterphone']  = '������X:';
$lang['lbl_reporteradcontact']  = '�pô:';
$lang['lbl_symptlinks']     = '�g���ɮ׳s��';
$lang['lbl_cmts_wlog']      = '�u�@����';
$lang['lbl_cmts_adcmt']     = '�s�W���l';
$lang['lbl_cmts_edtres']    = '�ѨM���';
$lang['btn_add']            = '�s�W';
$lang['btn_add_title']      = '�s�W���D';
$lang['btn_mod']            = '��s'; // to submit comment modification
$lang['btn_mod_title']      = '��s���D';
$lang['del_title']          = '�R�����l';
$lang['lbl_signin']         = '�еn�J</a> �Y�z�Q�n�^������';       // </a> necessary to close the link tag
$lang['lbl_please']         = '�� ';

$lang['th_project']         = '�M��';
$lang['th_id']              = '�ݨD�X';
$lang['th_created']         = '���X�ɶ�';
$lang['th_product']         = '����';
$lang['th_version']         = '����';
$lang['th_severity']        = '���n��';
$lang['th_status']          = '�i��';
$lang['th_username']        = '���X ';
$lang['th_usermail']        = 'eMail';
$lang['th_userphone']       = '���';
$lang['th_reporteradcontact']  = '�p���覡';
$lang['th_title']           = '���D';
$lang['th_descr']           = '�y�z';
$lang['th_sympt']           = '�g���ɮ׳s�� ';
$lang['th_assigned']        = '������'; 
$lang['th_resolution']      = '�ѨM���';
$lang['th_modified']        = '�ק�';        
$lang['gen_tab_open']       = '�Ӹ`';
$lang['descr_tab_mod']      = '�ק�y�z';
$lang['cmt_tab_open']       = '�s�W';
$lang['cmt_tab_mod']        = '�ק恵�l';
$lang['rsl_tab_open']       = '�ЫظѨM���';
$lang['dtls_usr_hidden']       = '���åӽФH���';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['issuemod_subject']    = '�ݨD�X %s �b %s ����s��';
$lang['cmnt_new_subject']    = '�o�ӻݨD�� %s ���s���l %s ';
$lang['cmnt_mod_subject']    = '�o�ӻݨD�� %s ���l�b %s �Q�ק�F';
$lang['cmnt_del_subject']    = '�o�ӻݨD�� %s ���l�b %s �Q�R���F';
$lang['issuemod_head']       = '�˷R���Τ�,';
$lang['issuemod_intro']      = '�z���ݨD���s�����l';
$lang['cmt_del_intro']       = '�@�h���l�Q�R���F';
$lang['issuemod_issueid']    = '�ݨD�X�@�@�@:';
$lang['issuemod_status']     = '�i�ס@�@�@�@:';
$lang['issuemod_product']    = '���ء@�@�@�@:';
$lang['issuemod_version']    = '�����@�@�@�@:';
$lang['issuemod_severity']   = '���n�ס@�@�@:';
$lang['issuemod_creator']    = '���X�@�@�@�@:';
$lang['issuemod_title']      = '���D�@�@�@�@:';
$lang['issuemod_cmntauthor'] = '�o���@�@�@�@:';
$lang['issuemod_date']       = '�����@�@�@:';
$lang['issuemod_cmnt']       = '���l�@�@�@�@:';
$lang['issuemod_see']        = '���}�����@�@: ';
$lang['issuemod_br']         = '���z�����n���@��';
$lang['issuemod_end']        = '�ݨD�l��';    // project name placed before this
$lang['issuedescrmod_subject'] = '��l�y�z�b�ݨD %s �b %s �Q�ק�F';

/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject']    = '�ݨD�X %s �b %s �w�g�ѨM�F';
$lang['issue_resolved_intro']      = '�z�Ҵ��X���ݨD�w�ѨM';
$lang['issue_resolved_status']     = '�w�ѨM';
$lang['issue_resolved_text']       = '�ѨM��ס@�@:';
$lang['msg_resolution_true']       = '�z���ѨM��״��榨�\';
 
/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']    = '���s�� %s �ݨD�b %s �W : %s (%s)';
$lang['issuenew_head']       = '�D�ޱz�n,';
$lang['issuenew_intro']      = '�ϥΪ̦��s���ݨD�A�Ы��������H��';
$lang['issuenew_descr']      = '�y�z:  ';

/******************************************************************************/
/* deviations from before for send an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']    = '�ݨD�X %s �Q�������z�F';
$lang['issueassigned_head']       = '�z�n,';
$lang['issueassigned_intro']      = '�z�Q���������U�C���ϥΪ̻ݨD:';

/******************************************************************************/
