<?php
/**
 * Italian language file for issuetracker plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Taggic@t-online.de
 */
 
// for the configuration manager
$lang['d_format']         = 'Y-m-d G:i:s';
$lang['it_data']          = 'Directory di IssueTracker all\'interno della cartella dati ("" = meta)<img title="Attenzione: &egrave fortemente consigliato memorizzare le segnalazioni al di fuori della cartella meta. Quindi, inserire un percorso qui (p.es. \'data/it_store/\')." alt="warning" src="./lib/plugins/config/images/warning.png" style="float:right;"> ';
$lang['upload']           = 'Consentire il caricamento di file che descrivono il sintomo';
$lang['max_fsize']        = 'Dimensione massima in Byte del file da caricare';
$lang['ip_blocked']       = 'Attivare la funzione anti-spam';
$lang['ip_blockd_time']   = 'Bloccare l\'ip per x minuti prima che un altro caricamento possa essere avviato';
$lang['send_email']       = 'Notificare per mail le nuove segnalazioni ?';
$lang['mail_templates']   = 'Utilizzare modelli html di posta elettronica ?';
$lang['email_address']    = 'Chi deve essere informato ?';
$lang['registered_users'] = 'Solo gli utenti registrati con autorizzazione di modifica alla pagina <br> di issue tracker possono creare report e aggiungere commenti';
$lang['auth_ad_overflow'] = 'Impedire AUTH:AD overflow';
$lang['assgnee_list']     = 'Estensione del file univoca per la lista degli assegnatari (p.es. assignees)';
$lang['profile_updt']     = 'Sincronizzazione dei dati utente inclusi nelle segnalazioni esistenti a seguito della modifica dei dati del profilo utente';
$lang['validate_mail_addr']= 'Validare l\'indirizzo e-mail con DNS dei segnalatori';
$lang['userinfo_email']   = 'Informare l\'utente per posta su modifiche alla segnalazione? (Scelta globale)';
$lang['mail_add_comment']         = 'Informare sui nuovi commenti';
$lang['mail_modify_comment']      = 'Informare su modifiche al commento';
$lang['mail_add_resolution']      = 'Informare sull\'inserimento della soluzione';
$lang['mail_modify_resolution']   = 'Informare sulla modifica della soluzione';
$lang['mail_modify__description'] = 'Informare sulla modifica della descrizione iniziale';
$lang['shw_mail_addr']    = 'eMail visibile al posto del nome utente (solo per utenti registrati)';
$lang['shw_assignee_as']  = 'Mostrare assegnatario per login, nome o indirizzo mail';
$lang['shwtbl_usr']       = 'Configurare le colonne da mostrare all\'utente come panoramica';
$lang['use_captcha']      = 'Usare captcha';
$lang['severity']         = 'Definire livelli di gravit&agrave da utilizzare <br> (separati da virgola, devono corrispondere al nome del file icona)';
$lang['status']           = 'Definire i livelli di stato della segnalazione da utilizzare <br> (separati da virgola, devono corrispondere al nome del file icona)';
$lang['status_special']   = 'Segnalazioni nascoste (eliminate), &egrave consentito solo un unico valore di stato!';
$lang['projects']         = 'Definire Progetti <br> (separati da virgola)';
$lang['products']         = 'Definire prodotti <br> (separati da virgola)';
$lang['components']       = 'Definire Componenti <br> (separati da virgola)';
$lang['assign']           = 'Selezionare gruppi di utenti wiki pre-selezionati per l\'assegnazione delle segnalazioni <br> (separati da pipe "|")';
$lang['noStatIMG']        = 'Testo di stato invece di icone nella lista segnalazioni';
$lang['noSevIMG']         = 'Testo di gravit&agrave invece di icone nella lista segnalazioni';
$lang['ltdReport']        = 'Escludere questi controlli dal form del report';
$lang['ltdListFilters']   = 'Escludere questi controlli filtro dalla lista delle segnalazioni';
$lang['multi_projects']   = 'Abilitare per attivare la gestione Multi-Progetto';
$lang['shw_project_col']  = 'Mostrare la colonna di progetto';
$lang['global_sort']	  = 'Ordinamento globale per le segnalazioni';
$lang['listview_sort']    = 'Ordinamento di default della lista per ID';