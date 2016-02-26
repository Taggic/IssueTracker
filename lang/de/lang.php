<?php
/**
 * German language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Strunck, ITC Live Network, www.itclive.de
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue']   = 'Ticket wurde gespeichert #';
$lang['wmsg1']     = 'Bitte geben Sie eine gültige E-Mail Adresse ein!';
$lang['wmsg2']     = 'Bitte geben Sie eine gültige Produkt-Version ein!';
$lang['wmsg3']     = 'Bitte geben Sie einen beschreibenden Text ein!';
$lang['wmsg4']     = '&nbsp;Bitte erst <a href="?do=login&amp" class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">einloggen</a> um ein Ticket zu speichern!';
$lang['wmsg5']     = 'Der Titel/Betreff ist nicht aussagekräftig!';
$lang['wmsg6']     = 'Upload Fehler: Die maximal zul&auml;ssige Dateigr&ouml;sse von %s bytes wurde &uuml;berschritten';
$lang['wmsg7']     = 'Upload Fehler: Die Dateierweiterung ist unzul&auml;ssig.';
$lang['wmsg8']     = 'Upload Fehler: Der Mime-Type wird nicht unterst&uuml;tzt.';
$lang['wmsg9']     = 'Spam Schutz: Ihre IP is f&uuml;r mindestens %s Minuten von weiteren Uploads ausgeschlossen..';
$lang['lbl_symptomupload'] = 'Indizien hochladen:';
$lang['btn_reportsave']   = 'Absenden';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']        = 'Anzahl der Tickets:&nbsp;';
$lang['lbl_scroll']          = 'Anzeigen: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']       = 'Schweregrad:&nbsp;';
$lang['lbl_filterstat']      = 'Status:&nbsp;';
$lang['lbl_filterprod']      = 'Produkt:&nbsp;';
$lang['lbl_filtervers']      = 'Filter Version:&nbsp;';
$lang['lbl_filtercomp']      = 'Filter Komponente:&nbsp;';
$lang['lbl_filterblock']     = 'Filter Test blockiert:&nbsp;';
$lang['lbl_filterassi']      = 'Filter Bearbeiter:&nbsp;';
$lang['lbl_filterreporter']  = 'Filter Anleger:&nbsp;';
$lang['cbx_myissues']        = 'Meine Tickets';
$lang['btn_go']              = 'Go';

$lang['btn_previuos']        = '<<<';
$lang['btn_previuos_title']  = 'vorheriges Ticket';
$lang['btn_next']            = '>>>';
$lang['btn_next_title']      = 'naechstes Ticket';

$lang['lbl_showid']          = 'Zeige Details der Tickets:';
$lang['btn_showid']          = 'Zeigen';
$lang['btn_showid_title']    = 'Zeigen';

$lang['lbl_sort']            = 'Sortieren nach:';
$lang['btn_sort']            = 'Sortieren';
$lang['btn_sort_title']      = 'Issues global nach ihrem Schlüssel sortieren.';

$lang['msg_commentfalse']    = 'Dieser Kommentar existiert bereits und wurde daher nicht noch einmal gespeichert.';
$lang['msg_commenttrue']     = 'Ihr Kommentar wurde gespeichert mit der ID #';
$lang['msg_wroundtrue']      = 'Ihr Workaround wurde erfolgreich gespeichert';
$lang['msg_commentmodtrue']  = 'Ihr Kommentar wurde erfolgreich aktualisiert unter der ID #';
$lang['msg_commentdeltrue']  = 'Ihr Kommentar #%s wurde erfolgreich gelöscht.';
$lang['msg_commentmodfalse'] = 'Es wurde kein Unterschied festgestellt zum bereits existierenden Kommentar #';
$lang['msg_pfilemissing']    = 'Projektdatei existiert nicht: %s.issues. ';
$lang['msg_issuemissing']    = 'Es gibt keine Tickets mit der ID ';
$lang['msg_inotexisting1']   = 'Ein Ticket mit dieser ID existiert nicht im angegebenen Projekt. <br /> Project = %s <br /> Issue ID = %s <br /> <a href="%sdoku.php?id=%s"> << back</a>';
$lang['msg_captchawrong']    = 'Falsche Antwort zur Anti-Spam Frage.';
$lang['msg_descrmodtrue']    = 'Die Beschreibung wurde erfolgreich aktualisiert.';
$lang['msg_slinkmodtrue']    = 'Die Links zu den Indizien wurden erfolgreich aktualisiert.';
$lang['msg_severitymodtrue'] = 'Schweregrad erfolgreich geändert.';
$lang['msg_statusmodtrue']   = 'Status erfolgreich geändert.';
$lang['msg_addFollower_true']   = 'ID: %s -> Interessenten hinzugefügt: ';
$lang['msg_rmvFollower_true']   = 'ID: %s -> Interessenten gelöscht: ';
$lang['msg_addFollower_failed'] = 'ID: %s -> Fehler bei der Aktualisierung des Interessenten: ';
$lang['itd_follower']        = '(Interessenten: %s)';
$lang['msg_showCase']        = 'Diese Ticket ID existiert in mehreren Projekten. Bitte w&aamp;hlen Sie:';

$lang['lbl_project']         = 'Projekt:';
$lang['lbl_issueid']         = 'ID:';
$lang['lbl_reporter']        = 'Angelegt von:';
$lang['lbl_reporterdtls']    = 'Anleger-Details';
$lang['lbl_initdescr']       = 'Beschreibung';
$lang['lbl_reportername']    = 'Name:';
$lang['lbl_reportermail']    = 'E-Mail:';
$lang['lbl_reporterphone']   = 'Telefon:';
$lang['lbl_reporteradcontact'] = 'Kontakt hinzufuegen:';
$lang['lbl_symptlinks']      = 'Link ';
$lang['lbl_cmts_wlog']       = 'Kommentare';
$lang['lbl_cmts_adcmt']      = 'Kommentar hinzufuegen';
$lang['lbl_cmts_edtres']     = 'Loesung';
$lang['btn_add']             = 'Speichern';
$lang['btn_add_title']       = 'Speichern';
$lang['btn_mod']             = 'Aktualisieren'; // to submit comment modification
$lang['btn_mod_title']       = 'Aktualisieren';
$lang['del_title']           = 'Kommentar loeschen';
$lang['lbl_signin']          = 'loggen Sie sich ein</a> wenn Sie einen Kommentar oder eine L&ouml;sung hinzuf&uuml;gen m&ouml;chten.'; // </a> necessary to close the link tag
$lang['lbl_please']          = 'Bitte ';
$lang['lbl_lessPermission']  = 'Ihre Berechtigungsstufe ist zu niedrig! Bitte kontaktieren Sie den Administrator!';
$lang['lbl_workaround']      = 'Workaround';

$lang['th_project']          = 'Projekt';
$lang['th_id']               = 'ID';
$lang['th_created']          = 'Angelegt';
$lang['th_product']          = 'Produkt';
$lang['th_components']       = 'Komponente';
$lang['th_tblock']           = 'Test blockiert';
$lang['th_tversion']         = 'Zielversion';
$lang['th_begin']            = 'Beginn';
$lang['th_deadline']         = 'Abgabetermin';
$lang['th_progress']         = 'Fortschritt in %';
$lang['th_version']          = 'Version';
$lang['th_severity']         = 'Schweregrad';
$lang['th_status']           = 'Status';
$lang['th_user_name']        = 'Benutzername';
$lang['th_usermail']         = 'E-Mail';
$lang['th_userphone']        = 'Telefon';
$lang['th_reporteradcontact'] = 'Neuer Kontakt';
$lang['th_title']            = 'Titel';
$lang['th_description']      = 'Beschreibung';
$lang['th_sympt']            = 'Link ';
$lang['th_assigned']         = 'Zugewiesen'; 
$lang['th_resolution']       = 'L&ouml;sung';
$lang['th_modified']         = 'Ge&auml;ndert';        
$lang['th_showmodlog']       = 'Statusverlauf';
$lang['h_modlog']            = 'Status&auml;nderungen von #';
$lang['mod_valempty']        ='[gel&ouml;scht]';
$lang['back']                = 'zur&uuml;ck';
$lang['gen_tab_open']        = 'Details';
$lang['descr_tab_mod']       = '&auml;ndern';
$lang['cmt_tab_open']        = 'Kommentar hinzuf&uuml;gen';
$lang['cmt_tab_mod']         = 'Kommentar &auml;ndern';
$lang['rsl_tab_open']        = 'L&ouml;sung hinzuf&uuml;gen/&auml;ndern';
$lang['dtls_usr_hidden']     = 'Benutzerdaten ausgeblendet';
$lang['dtls_reporter_hidden']   = 'Reporter';
$lang['dtls_follower_hidden']   = 'Beobachter';
$lang['dtls_assignee_hidden']   = 'Bearbeiter';
$lang['dtls_foreigner_hidden']  = 'jemand anderes';
$lang['minor_mod']           = 'kleine &Auml;nderung';
$lang['minor_mod_cbx_title'] = 'keine eMails bei kleinen  &Auml;nderungen';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['issuemod_subject']    = 'Wiki IssueTracker - Ticket %s von %s wurde geändert (%s)';
$lang['cmnt_new_subject']    = 'Wiki IssueTracker - Beitrag zum Ticket %s von %s '; 
$lang['cmnt_mod_subject']    = 'Wiki IssueTracker - Kommentar zum Ticket %s von %s ';
$lang['cmnt_del_subject']    = 'Wiki IssueTracker - Kommentar zum Ticket %s von %s wurde gelöscht';
$lang['cmnt_wa_subject']     = 'Wiki IssueTracker - Workaround zum Ticket %s von %s eingetragen';   // $issue['id'], $project
$lang['issuemod_head']       = 'Hallo!';
$lang['issuemod_intro']      = 'Ihr Ticket wurde geändert.';
$lang['cmt_del_intro']       = 'Es wurde ein Kommentar gelöscht.';
$lang['issuemod_issueid']    = 'ID:           ';
$lang['issuemod_status']     = 'Status:       ';
$lang['issuemod_product']    = 'Produkt:      ';
$lang['issuemod_version']    = 'Version:      ';
$lang['issuemod_severity']   = 'Schweregrad:  ';
$lang['issuemod_creator']    = 'Erzeuger:     ';
$lang['issuemod_assignee']   = 'Zugewiesen:   ';
$lang['issuemod_title']      = 'Titel:        ';
$lang['issuemod_cmntauthor'] = 'Kommentierer: ';
$lang['issuemod_date']       = 'Gesendet am:  ';
$lang['issuemod_cmnt']       = 'Kommentar:    ';
$lang['issuemod_see']        = 'Details:      ';
$lang['issuemod_br']         = 'Mit freundlichen Grüssen,';
$lang['issuemod_end']        = 'Wiki IssueTracker'; // project name placed before this
$lang['issuedescrmod_subject'] = 'Wiki IssueTracker - Beschreibung zum Ticket %s von %s wurde verändert.';
$lang['issuemod_changes']    = 'Das Ticket wurde bei %s von %s nach %s geändert.'; //$column, $old_value, $new_value
$lang['btn_upd_addinfo']     = 'Speichern';
 
/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject'] = 'Wiki IssueTracker - Ticket %s von %s wurde gelöst';
$lang['issue_resolved_intro']   = 'Ihr Ticket wurde gelöst!';
$lang['issue_resolved_status']  = 'Gelöst';
$lang['issue_resolved_text']    = 'Lösung:';
$lang['msg_resolution_true']    = 'Ihre Lösung wurde gespeichert für Ticket: #';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']    = 'Wiki IssueTracker - Neues Ticket mit Schweregrad %s zum Projekt %s';
$lang['issuenew_head']       = 'Hallo Administrator!';
$lang['issuenew_intro']      = 'Ein neues Ticket wurde für folgendes Projekt angelegt:';
$lang['issuenew_descr']      = 'Beschreibung:  ';

/******************************************************************************/
/* deviations from before for send an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']  = 'Wiki IssueTracker - Ticket %s zum Projekt %s wurde Ihnen zugewiesen';
$lang['issueassigned_head']     = 'Hallo!';
$lang['issueassigned_intro']    = 'Ihnen wurde folgendes Ticket zugewiesen:';
$lang['it__none']               = '--';
/******************************************************************************/
/* following text is related to search feature                                
*/
$lang['lbl_search']          = 'Suche nach:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$lang['btn_search']          = 'Start';
$lang['btn_search_title']    = 'Suche';
$lang['search_hl1']          = '<h1>Suche</h1>';
$lang['search_txt1']         = "Unten sind die Ergebnisse Ihrer Suche gelistet. Bitte bedenken Sie, dass die Suche keine Suchausdrücke auflösen kann.";
$lang['search_hl2']          = '<h2>Ergebnisse</h2>';
$lang['search_Issue']        = 'Ticket';
$lang['search_Comment']      = 'Kommentar';
$lang['search_Type']         = 'Typ';
$lang['search_ID']           = 'ID';
$lang['search_Subject']      = 'Inhalt';

/******************************************************************************/
$lang['table_kit_OK']        = 'OK';
$lang['table_kit_Cancel']    = ' Abbrechen';
$lang['umlaute']             = '/Ä/,/Ö/,/Ü/,/ß/,/ä/,/ö/,/ü/';
$lang['conv_umlaute']        = 'Ae,Oe,Ue,ss,ae,oe,ue';

/******************************************************************************/	
// Report Manager	
$lang['it_btn_rprt_mngr']    = 'Report erstellen';
