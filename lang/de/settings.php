<?php
/**
 * German language file for issuetracker plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Strunck, ITC Live Network, www.itclive.de
 */
 
// for the configuration manager
$lang['d_format']			    = 'G:i:s d-m-Y';
$lang['it_data']          = 'IssueTracker Verzeichnis im Data Ordner (leer = meta)<img title="Warnung: Es wird empfohlen den Pfad zu &auml;ndern. Bei existierenden Installationen sind die Datein entsprechend zu verschieben. Bitte f&uuml;gen Sie den Pfad hier entsprechend ein (z.B. \'data/it_store/\')." alt="Warnung" src="./lib/plugins/config/images/warning.png" style="float:right;"> ';
$lang['upload']           = 'Indizien upload erlauben?';
$lang['max_fsize']        = 'Maximale Dateigr&ouml;sse in Byte';
$lang['ip_blocked']       = 'Anti-Spam einschalten';
$lang['ip_blockd_time']   = 'IP für x Minuten nach Upload blocken';
$lang['send_email']			  = '&Uuml;ber neue Aufgaben per E-Mail informieren?'; 
$lang['mail_templates']   = 'HTML E-Mail Templates nutzen?';
$lang['email_address']		= 'Wer wird informiert?';
$lang['registered_users']	= 'Nur registrierten Benutzern mit passenden Zugangsrechten ist es erlaubt im Issue Tracker neue Aufgaben und Kommentare anzulegen';
$lang['auth_ad_overflow'] = 'Kompensierung AUTH:AD Fehler (>1.000 user Objekte)';
$lang['assgnee_list']     = 'eindeutige Dateierweiterung für Bearbeiterliste (z.B. assignees)';                        // needs unique file extension like assignees
$lang['profile_updt']     = 'Sync mit Anwender-Profiländerungen';
$lang['validate_mail_addr']= 'Validiere Reporter E-Mail Adresse mit DNS';
$lang['userinfo_email']		= 'Den Benutzer &uuml;ber &Auml;nderungen per E-Mail informieren?';
$lang['mail_add_comment']         = 'Mailinfo bei neuen Kommentaren';
$lang['mail_modify_comment']      = 'Mailinfo bei Kommentar&auml;nderung';
$lang['mail_add_resolution']      = 'Mailinfo bei L&ouml;sungseintrag';
$lang['mail_modify_resolution']   = 'Mailinfo bei L&ouml;sungs&auml;nderung';
$lang['mail_modify__description'] = 'Mailinfo bei &Auml;nderung der Beschreibung';
$lang['shw_mail_addr']    = 'E-Mail Adresse statt Namen (sichtbar nur f&uuml;r registrierte Benutzer)';
$lang['shw_assignee_as']  = 'Beauftragten anzeigen mit Login, Name oder E-Mail Adresse';
$lang['shwtbl_usr']			  = 'Folgende Spalten sollen dem Benutzer angezeigt werden';
$lang['use_captcha']		  = 'CAPTCHA benutzen?'; 
$lang['severity']			    = 'Schweregrade die benutzt werden sollen<br>(kommagetrennt, Namen mit passenden Bilddateien)';
$lang['status']				    = 'Status-Levels die benutzt werden sollen<br>(kommagetrennt, Namen mit passenden Bilddateien)';
$lang['status_special']   = 'Ausgeblendet (gel&ouml;scht), nur Einzelwert zul&auml;ssig !';
$lang['projects']			    = 'Projektnamen die benutzt werden sollen<br>(kommagetrennt)';
$lang['products']			    = 'Produktnamen die benutzt werden sollen<br>(kommagetrennt)';
$lang['components']       = 'Liste der Komponenten <br>(kommagetrennt)';
$lang['assign']				    = 'Wiki Benutzergruppe denen Tickets zugewiesen werden k&ouml;nnen<br>(senkrechter Strich "|" als Trennzeichen)';
$lang['noStatIMG']			  = 'Statustext statt Symbole anzeigen';
$lang['noSevIMG']			    = 'Schweregradtext statt Symbole anzeigen';
$lang['ltdReport']			  = 'Ticket Formular - Ausgabe um folgende Auswahl reduzieren';
$lang['ltdListFilters']   = 'Ticket Liste - Filter um folgende Auswahl reduzieren';
$lang['multi_projects']	  = 'alle Projekte anzeigen';
$lang['shw_project_col']  = 'Projektspalte anzeigen';
$lang['global_sort']	    = 'Globale Sortierung der Ticket';
$lang['listview_sort']    = 'Sortierung der Ticketliste nach ID';