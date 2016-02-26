<?Php
/**
 * Italian language file for IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Taggic <taggic@t-online.de>
 * /
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue'] = 'La tua segnalazione &egrave; stata memorizzata con successo con ID #';
$lang['wmsg1'] = 'Inserisci un indirizzo email valido, preferibilmente il tuo, per chiarimenti e/o commenti riguardanti la segnalazione.';
$lang['wmsg2'] = 'Inserisci una versione di prodotto valida da mettere in relazione a questa segnalazione.';
$lang['wmsg3'] = 'Dai una descrizione più accurata della segnalazione.';
$lang['wmsg4'] = '&nbsp;Prego <a href="?do=login&amp" class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Login">Accedi</a> se vuoi fare una segnalazione.';
$lang['wmsg5'] = 'Dai un titolo descrittivo alla segnalazione.';
$lang['wmsg6'] = 'Errore di caricamento: La dimensione massima di %s byte per i file &egrave; stata superata';
$lang['wmsg7'] = 'Errore di caricamento: L\'estensione del file non &egrave; valida.';
$lang['wmsg8'] = 'Errore di caricamento: Il Mime-Type del file &egrave; difettoso o non supportato.';
$lang['wmsg9'] = 'Sicurezza anti spam: Il caricamento dal tuo IP &egrave; bloccato per almeno altri %s minuti.';
$lang['lbl_symptomupload'] = 'Caricamento del file che descrive il sintomo:';
$lang['btn_reportsave'] = 'Invia';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']           = 'Segnalazioni:&nbsp;';
$lang['lbl_scroll']             = 'Scorri la lista delle segnalazioni: &nbsp;';
$lang['lbl_filtersev']          = 'Filtra per gravit&agrave;: &nbsp;';
$lang['lbl_filterstat']         = 'Filtra per stato: &nbsp;';
$lang['lbl_filterprod']         = 'Filtra per prodotto: &nbsp;';
$lang['lbl_filtervers']         = 'Filtra per versione: &nbsp;';
$lang['lbl_filtercomp']         = 'Filtra per componente: &nbsp;';
$lang['lbl_filterblock']        = 'Filtra per l\'attributo - Bloccante per il rilascio - : &nbsp;';
$lang['lbl_filterassi']         = 'Filtra per assegnatario: &nbsp;';
$lang['lbl_filterreporter']     = 'Filtra per segnalatore: &nbsp;';
$lang['cbx_myissues']           = 'Le mie segnalazioni:&nbsp;';
$lang['btn_go']                 = 'Applica filtro';

$lang['btn_previuos']           = '<<<';
$lang['btn_previuos_title']     = 'segnalazioni precedenti';
$lang['btn_next']               = '>>>';
$lang['btn_next_title']         = 'segnalazioni successive';

$lang['lbl_showid']             = 'Mostra i dettagli della segnalazione:';
$lang['btn_showid']             = 'Mostra';
$lang['btn_showid_title']       = 'Mostra';

$lang['lbl_sort']               = 'Ordina per:';
$lang['btn_sort']               = 'Ordina';
$lang['btn_sort_title']         = 'Ordina l\'elenco delle segnalazioni globalmente per lista di chiavi.';

$lang['msg_commentfalse']       = 'Questo commento esiste gi&agrave; e non &egrave; stato aggiunto di nuovo.';
$lang['msg_commenttrue']        = 'Il tuo commento &egrave; stato salvato con successo con ID #';
$lang['msg_wroundtrue']         = 'La tua soluzione alternativa &egrave; stata memorizzata con successo';
$lang['msg_commentmodtrue']     = 'Il tuo commento &egrave; stato modificato con successo con ID #';
$lang['msg_commentdeltrue']     = 'Il tuo commento #%s &egrave; stato eliminato con successo.';
$lang['msg_commentmodfalse']    = 'Nessuna modifica al commento con ID #';
$lang['msg_pfilemissing']       = 'Il file di progetto non esiste: %s.issues. ';
$lang['msg_issuemissing']       = 'Non esiste alcuna segnalazione con ID ';
$lang['msg_inotexisting1']      = 'La segnalazione non esiste nel progetto specificato. <br /> Progetto = %s <br /> ID segnalazione = %s <br /> <a href="%sdoku.php?id=%s"> << back</a>';
$lang['msg_captchawrong']       = 'Risposta sbagliata alla domanda antispam.';
$lang['msg_descrmodtrue']       = 'Descrizione modificata con successo.';
$lang['msg_slinkmodtrue']       = 'I link ai file che descrivono i sintomi sono stati modificati con successo.';
$lang['msg_severitymodtrue']    = 'Gravit&agrave; modificata con successo.';
$lang['msg_statusmodtrue']      = 'Stato modificato con successo.';
$lang['msg_addFollower_true']   = 'ID: %s -> Follower aggiunto: ';
$lang['msg_rmvFollower_true']   = 'ID: %s -> Follower eliminato: ';
$lang['msg_addFollower_failed'] = 'ID: %s -> Impossibile aggiornare il Follower: ';
$lang['itd_follower']           = '(Follower: %s)';
$lang['msg_showCase']           = 'Questo ID di segnalazione esiste in diversi progetti. Selezionarne uno:';

$lang['lbl_project']            = 'Progetto:';
$lang['lbl_issueid']            = 'ID:';
$lang['lbl_reporter']           = 'Segnalato da:';
$lang['lbl_reporterdtls']       = 'Dettagli del segnalatore';
$lang['lbl_initdescr']          = 'Descrizione iniziale';
$lang['lbl_reportername']       = 'Nome:';
$lang['lbl_reportermail']       = 'eMail:';
$lang['lbl_reporterphone']      = 'Telefono:';
$lang['lbl_reporteradcontact']  = 'Aggiungi contatto:';
$lang['lbl_symptlinks']         = 'Link ai file che descrivono i sintomi';
$lang['lbl_cmts_wlog']          = 'Commenti (registro lavori)';
$lang['lbl_cmts_adcmt']         = 'Aggiungi un nuovo commento';
$lang['lbl_cmts_edtres']        = 'Soluzione';
$lang['btn_add']                = 'Aggiungi';
$lang['btn_add_title']          = 'Aggiungi';
$lang['btn_mod']                = 'Aggiorna'; // to submit comment modification
$lang['btn_mod_title']          = 'Aggiorna';
$lang['del_title']              = 'Elimina questo commento';
$lang['lbl_signin']             = 'Accedi</a> se vuoi aggiungere un commento o soluzione.'; // </a>  necessary to close the link tag
$lang['lbl_please']             = 'Prego ';
$lang['lbl_lessPermission']     = 'Il tuo livello di autorizzazione &egrave; troppo basso. Contatta l\'amministratore. ';
$lang['lbl_workaround']         = 'Soluzione alternativa';

$lang['th_project']             = 'Progetto';
$lang['th_id']                  = 'Id';
$lang['th_created']             = 'Creato';
$lang['th_product']             = 'Prodotto';
$lang['th_components']          = 'Componente';
$lang['th_tblock']              = 'Bloccante per il rilascio';
$lang['th_tversion']            = 'Versione obiettivo';
$lang['th_begin']               = 'Inizio';
$lang['th_deadline']            = 'Termine';
$lang['th_progress']            = 'Avanzamento in %';
$lang['th_version']             = 'Versione';
$lang['th_severity']            = 'Gravit&agrave;';
$lang['th_status']              = 'Stato';
$lang['th_user_name']           = 'Segnalato da';
$lang['th_usermail']            = 'Email del segnalatore';
$lang['th_userphone']           = 'Telefono del segnalatore';
$lang['th_reporteradcontact']   = 'Aggiungi contatto';
$lang['th_title']               = 'Titolo';
$lang['th_description']         = 'Descrizione della segnalazione';
$lang['th_sympt']               = 'Link a file che descrive il sintomo # ';
$lang['th_assigned']            = 'Assegnato a';
$lang['th_resolution']          = 'Soluzione';
$lang['th_modified']            = 'Modificato';
$lang['th_showmodlog']          = 'Cronologia stato';
$lang['h_modlog']               = 'Cronologia delle modifiche di stato di # ';
$lang['mod_valempty']           = '[eliminato]';
$lang['back']                   = 'indietro';
$lang['gen_tab_open']           = 'Dettagli';
$lang['descr_tab_mod']          = 'Modifica';
$lang['cmt_tab_open']           = 'Aggiungi un commento';
$lang['cmt_tab_mod']            = 'Modifica un commento';
$lang['rsl_tab_open']           = 'Aggiungi / modifica Soluzione';
$lang['dtls_usr_hidden']        = 'dettagli utente';
$lang['dtls_reporter_hidden']   = 'dettagli segnalatore';
$lang['dtls_follower_hidden']   = 'dettagli follower';
$lang['dtls_assignee_hidden']   = 'dettagli assegnatario';
$lang['dtls_foreigner_hidden']  = 'dettagli estraneo';
$lang['minor_mod']              = 'Modifica marginale';
$lang['minor_mod_cbx_title']    = 'Evita l\'invio di email per cambiamenti non sostanziali';


/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/  
$lang['cmnt_new_subject']       = 'Segnalazione #%s su %s: nuovo commento aggiunto'; // $issue['id'], $project
$lang['cmnt_mod_subject']       = 'Segnalazione #%s su %s: commento modificato'; // $issue['id'], $project
$lang['cmnt_del_subject']       = 'Segnalazione #%s su %s: commento eliminato'; // $issue['id'], $project
$lang['cmnt_wa_subject']        = 'Segnalazione #%s su %s: soluzione alternativa fornita';   // $issue['id'], $project
$lang['issuemod_subject']       = 'Segnalazione #%s su %s: %s'; //$issue['id'], $project, $column
$lang['issuemod_head']          = 'Gentile utente,';
$lang['issuemod_intro']         = 'La tua segnalazione &egrave; stata modificata.';
$lang['cmt_del_intro']          = 'Un commento &egrave; stato eliminato.';
$lang['issuemod_issueid']       = 'ID:           ';
$lang['issuemod_status']        = 'Stato:        ';
$lang['issuemod_product']       = 'Prodotto:     ';
$lang['issuemod_version']       = 'Versione:     ';
$lang['issuemod_severity']      = 'Gravit&agrave;:      ';
$lang['issuemod_creator']       = 'Segnalato da:       ';
$lang['issuemod_assignee']      = 'Assegnato a: ';
$lang['issuemod_title']         = 'Titolo:       ';
$lang['issuemod_cmntauthor']    = 'Commento di:  ';
$lang['issuemod_date']          = 'inviato il:   ';
$lang['issuemod_cmnt']          = 'Commento:     ';
$lang['issuemod_see']           = 'vedi dettagli:';
$lang['issuemod_br']            = 'saluti';
$lang['issuemod_end']           = ' Issue Tracker';    // project name placed before this
$lang['issuedescrmod_subject']  = 'Segnalazione #%s su %s: descrizione iniziale modificata'; // $issue['id'], $project
$lang['issuemod_changes']       = 'La segnalazione &egrave; cambiata il %s da %s a %s.'; //$column, $old_value, $new_value
$lang['btn_upd_addinfo']        = 'Salva';

/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/  
$lang['issue_resolved_subject'] = 'La segnalazione #%s su %s &egrave; stata risolta';
$lang['issue_resolved_intro']   = 'La tua segnalazione &egrave; stata risolta.';
$lang['issue_resolved_status']  = 'Risolta';
$lang['issue_resolved_text']    = 'Soluzione:  ';
$lang['msg_resolution_true']    = 'La tua soluzione &egrave; stata aggiunta con successo all\'ID';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']    = 'segnalazione %s inviata per %s per il prodotto: %s (%s)';
$lang['issuenew_head']       = 'Gentile admin,';
$lang['issuenew_intro']      = 'una nuova segnalazione &egrave; stata creata per il progetto:';
$lang['issuenew_descr']      = 'Descrizione:  ';

/******************************************************************************/
/* deviations from before for sending an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']  = 'Segnalazione #%s su %s assegnata a te'; //$issue['id'], $project
$lang['issueassigned_head']     = 'Ciao,';
$lang['issueassigned_intro']    = 'ti &egrave; stata assegnata la seguente segnalazione:';
$lang['it__none']               = '--';
/******************************************************************************/
/* following text is related to search feature                                
*/
$lang['lbl_search']          = 'Cerca le seguenti parole:&nbsp;&nbsp;';
$lang['btn_search']          = 'Inizia ricerca';
$lang['btn_search_title']    = 'Cerca';
$lang['search_hl1']          = '<h1>Cerca</h1>';
$lang['search_txt1']         = "Puoi trovare i risultati della tua ricerca qui sotto. Ricorda che si tratta di una semplice ricerca che individua singole parole e non capisce le logiche di query.";
$lang['search_hl2']          = '<h2>Risultato</h2>';
$lang['search_Issue']        = 'Segnalazione';
$lang['search_Comment']      = 'Commento';
$lang['search_Type']         = 'Tipo';
$lang['search_ID']           = 'ID';
$lang['search_Subject']      = 'Oggetto';

/******************************************************************************/
$lang['table_kit_OK']        = 'OK';
$lang['table_kit_Cancel']    = ' Annulla';
$lang['yes']                 = ' S&igrave';
$lang['no']                  = ' No';

/******************************************************************************/
// Report Manager
$lang['it_btn_rprt_mngr']    = 'Crea Report';