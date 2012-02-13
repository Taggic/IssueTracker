<?php
/**
 * German language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Strunck, ITC Live Network, www.itclive.de
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue']	= 'Ticket wurde gespeichert #';
$lang['wmsg1']					= 'Bitte geben Sie eine g&uuml;ltige E-Mail Adresse ein.';
$lang['wmsg2']					= 'Bitte geben Sie eine g&uuml;ltige Produkt-Version ein.';
$lang['wmsg3']					= 'Bitte geben Sie einen beschreibenden Text ein.';
$lang['wmsg4']					= '&nbsp;Bitte erst <a href="?do=login&amp class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">einloggen</a> um ein Ticket zu speichern.';
$lang['wmsg5']          = 'Die Beschreibung ist nicht aussagekräftig.';
$lang['btn_reportsave']	= 'Absenden';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']			= 'Anzahl der Tickets:&nbsp;';
$lang['lbl_scroll']				= 'Anzeigen: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']			= 'Schweregrad:&nbsp;';
$lang['lbl_filterstat']			= 'Status:&nbsp;';
$lang['btn_go']					= 'Go';

$lang['btn_previuos']			= '<<<';
$lang['btn_previuos_title']		= 'vorheriges Ticket';
$lang['btn_next']				= '>>>';
$lang['btn_next_title']			= 'n&auml;chstes Ticket';

$lang['lbl_showid']				= 'Zeige Details der Tickets:';
$lang['btn_showid']				= 'Zeigen';
$lang['btn_showid_title']		= 'Zeigen';

$lang['msg_commentfalse']		= 'Dieser Kommentar existiert bereits und wurde daher nicht noch einmal gespeichert.';
$lang['msg_commenttrue']		= 'Ihr Kommentar wurde gespeichert mit der ID #';
$lang['msg_commentmodtrue'] = 'Ihr Kommentar wurde erfolgreich aktualisiert unter der ID #';
$lang['msg_commentdeltrue'] = 'Ihr Kommentar #%s wurde erfolgreich gel&ouml;scht.';
$lang['msg_commentmodfalse'] = 'Eswurde kein Unterschied festgestellt zum bereits existierenden Kommentar #';
$lang['msg_pfilemissing']		= 'Projektdatei existiert nicht: %s .issues. ';
$lang['msg_issuemissing']		= 'Es gibt keine Tickets mit der ID ';
$lang['msg_captchawrong']		= 'Falsche Antwort zur Anti-Spam Frage.';
$lang['msg_descrmodtrue']   = 'Die Beschreibung wurde erfolgreich aktualisiert.';
$lang['msg_slinkmodtrue']   = 'Die Links zu den Indizien wurden erfolgreich aktualisiert.';

$lang['lbl_project']			= 'Projekt:';
$lang['lbl_issueid']			= 'ID:';
$lang['lbl_reporter']			= 'Angelegt von:';
$lang['lbl_reporterdtls']		= 'Anleger-Details';
$lang['lbl_initdescr']			= 'Beschreibung';
$lang['lbl_reportername']		= 'Name:';
$lang['lbl_reportermail']		= 'E-Mail:';
$lang['lbl_reporterphone']		= 'Telefon:';
$lang['lbl_reporteradcontact']	= 'Kontakt hinzuf&uuml;gen:';
$lang['lbl_symptlinks']			= 'Link ';
$lang['lbl_cmts_wlog']			= 'Kommentare';
$lang['lbl_cmts_adcmt']			= 'Kommentar hinzuf&uuml;gen';
$lang['lbl_cmts_edtres']		= 'L&ouml;sung';
$lang['btn_add']				    = 'Speichern';
$lang['btn_add_title']			= 'Speichern';
$lang['btn_mod']            = 'Aktualisieren'; // to submit comment modification
$lang['btn_mod_title']      = 'Aktualisieren';
$lang['del_title']          = 'Kommentar l&ouml;schen';
$lang['lbl_signin']				  = 'loggen Sie sich ein</a> wenn Sie einen Kommentar oder eine L&ouml;sung hinzuf&uuml;gen m&ouml;chten.';       // </a> necessary to close the link tag
$lang['lbl_please']         = 'Bitte ';

$lang['th_project']				= 'Projekt';
$lang['th_id']					  = 'ID';
$lang['th_created']				= 'Angelegt';
$lang['th_product']				= 'Produkt';
$lang['th_version']				= 'Version';
$lang['th_severity']			= 'Schweregrad';
$lang['th_status']				= 'Status';
$lang['th_username']			= 'Benutzername';
$lang['th_usermail']			= 'E-Mail';
$lang['th_userphone']			= 'Telefon';
$lang['th_reporteradcontact']	= 'Neuer Kontakt';
$lang['th_title']				  = 'Titel';
$lang['th_description']		= 'Beschreibung';
$lang['th_sympt']				  = 'Link ';
$lang['th_assigned']			= 'Zugewiesen'; 
$lang['th_resolution']		= 'L&ouml;sung';
$lang['th_modified']			= 'Ge&auml;ndert';        
$lang['th_showmodlog']    = 'Status log';
$lang['h_modlog']         = 'Status &auml;nderungen von #';
$lang['back']             = 'zur&uuml;ck';
$lang['gen_tab_open']			= 'Details';
$lang['descr_tab_mod']    = '&auml;ndern';
$lang['cmt_tab_open']			= 'Kommentar hinzuf&uuml;gen';
$lang['cmt_tab_mod']      = 'Kommentar &auml;ndern';
$lang['rsl_tab_open']			= 'L&ouml;sung hinzuf&uuml;gen/&auml;ndern';
$lang['dtls_usr_hidden']  = 'Benutzerdaten ausgeblendet';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['issuemod_subject']		= 'Wiki Issue Tracker - Ticket %s wurde ge&auml;ndert';
$lang['cmnt_new_subject']   = 'Es wurde ein neuer Beitrag zum Ticket %s von %s erstellt.'; 
$lang['cmnt_mod_subject']   = 'Ein Kommentar zum Ticket %s von %s wurde ge&auml;ndert';
$lang['cmnt_del_subject']   = 'Ein Kommentar zum Ticket %s von %s wurde gel&ouml;scht';
$lang['issuemod_head']			= 'Hallo!';
$lang['issuemod_intro']			= 'Ihr Ticket wurde ge&auml;ndert.';
$lang['cmt_del_intro']      = 'Es wurde ein Kommentar gel&ouml;scht.';
$lang['issuemod_issueid']		= 'ID:           ';
$lang['issuemod_status']		= 'Status:       ';
$lang['issuemod_product']		= 'Produkt:      ';
$lang['issuemod_version']		= 'Version:      ';
$lang['issuemod_severity']	= 'Schweregrad:  ';
$lang['issuemod_creator']		= 'Erzeuger:     ';
$lang['issuemod_title']			= 'Titel:        ';
$lang['issuemod_cmntauthor']	= 'Kommentierer: ';
$lang['issuemod_date']			= 'Gesendet am:  ';
$lang['issuemod_cmnt']			= 'Kommentar:    ';
$lang['issuemod_see']			  = 'Details:      ';
$lang['issuemod_br']		  	= 'Mit freundlichen Gr&uuml;ssen,';
$lang['issuemod_end']			= 'Wiki Issue Tracker';    // project name placed before this
$lang['issuedescrmod_subject'] = 'Die urspr&uuml;ngliche Beschreibung des Tickets %s von %s wurde verändert.';
 
/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject']	= 'Wiki Issue Tracker - Das Ticket %s von %s wurde gel&ouml;st';
$lang['issue_resolved_intro']	= 'Ihr Ticket wurde gel&ouml;st!';
$lang['issue_resolved_status']	= 'Gel&ouml;st';
$lang['issue_resolved_text']	= 'L&ouml;sung:';
$lang['msg_resolution_true']	= 'Ihre L&ouml;sung wurde hinzugef&uuml;gt.';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']		= 'Wiki Issue Tracker - Neues Ticket mit Schweregrad %s zum Projekt %s';
$lang['issuenew_head']			= 'Hallo Administrator!';
$lang['issuenew_intro']			= 'Ein neues Ticket wurde f&uuml;r folgendes Projekt angelegt:';
$lang['issuenew_descr']			= 'Beschreibung:  ';

/******************************************************************************/
/* deviations from before for send an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']	= ' - Wiki Issue Tracker - Ticket %s wurde Ihnen zugewiesen';
$lang['issueassigned_head']		= 'Hallo!';
$lang['issueassigned_intro']	= 'Ihnen wurde folgendes Ticket zugewiesen:';

/******************************************************************************/
