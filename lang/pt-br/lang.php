<?php
/**
 * Arquivo de linguagem em Português do Brasil para IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Benedito Carneiro <ctsbc@yahoo.com.br>
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue'] = 'Seu relat&oacute;rio foi armazenado com sucesso como ocorr&ecirc;ncia #';
$lang['wmsg1'] = 'Informe um endere&ccedil;o de email v&aacute;lido, de prefer&ecirc;ncia o seu, para esclarecimentos e/ou retorno sobre a ocorr&ecirc;ncia relatada.';
$lang['wmsg2'] = 'Informe uma vers&atilde;o de produto v&aacute;lida para relatar adequadamente esta ocorr&ecirc;ncia.';
$lang['wmsg3'] = 'Informe uma descri&ccedil;&atilde;o melhor da sua ocorr&ecirc;ncia.';
$lang['wmsg4'] = '&nbsp;Por favor, <a href="?do=login&amp" class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Autentica&ccedil;&atilde;o">Autentique-se</a> se quer relatar uma ocorr&ecirc;ncia.';
$lang['wmsg5'] = 'Informe um t&iacute;tulo adequadamente descritivo para a ocorr&ecirc;ncia.';
$lang['wmsg6'] = 'Erro de Upload: O tamanho m&aacute;ximo do arquivo (%s bytes) foi ultrapassado.';
$lang['wmsg7'] = 'Erro de Upload: A extens&atilde;o do arquivo &eacute; inv&aacute;lida.';
$lang['wmsg8'] = 'Erro de Upload: O Mime-Type do arquivo n&atilde;o existe ou n&atilde;o &eacute; suportado.';
$lang['wmsg9'] = 'Seguran&ccedil;a Antispam: O upload do seu IP est&aacute; bloqueado por mais %s minutos, pelo menos.';
$lang['lbl_symptomupload'] = 'Upload de arquivo de sintomas:';
$lang['btn_reportsave'] = 'Enviar';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']           = 'Quantidade de %s ocorr&ecirc;ncias:&nbsp;';
$lang['lbl_scroll']             = 'Rolar a lista de ocorr&ecirc;ncias: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']          = 'Filtrar Severidade:&nbsp;';
$lang['lbl_filterstat']         = 'Filtrar Status:&nbsp;';
$lang['lbl_filterprod']         = 'Filtrar Produto:&nbsp;';
$lang['cbx_myissues']           = 'Meus incidentes:&nbsp;';
$lang['btn_go']                 = 'OK';

$lang['btn_previuos']           = '<<<';
$lang['btn_previuos_title']     = 'Ocorr&ecirc;ncias anteriores';
$lang['btn_next']               = '>>>';
$lang['btn_next_title']         = 'próximas Ocorr&ecirc;ncias';

$lang['lbl_showid']             = 'Mostrar detalhes da ocorr&ecirc;ncia:';
$lang['btn_showid']             = 'Mostrar';
$lang['btn_showid_title']       = 'Mostrar';

$lang['msg_commentfalse']       = 'Este coment&aacute;rio j&aacute; existe e n&atilde;o foi adicionado novamente.';
$lang['msg_commenttrue']        = 'Seu coment&aacute;rio foi armazenado com sucesso com ID #';
$lang['msg_commentmodtrue']     = 'Seu coment&aacute;rio foi modificado com sucesso com ID #';
$lang['msg_commentdeltrue']     = 'Seu coment&aacute;rio #%s foi exclu&iacute;do com sucesso.';
$lang['msg_commentmodfalse']    = 'N&atilde;o tem diferen&ccedil;a do coment&aacute;rio ID #';
$lang['msg_pfilemissing']       = 'O arquivo do projeto n&atilde;o existe: %s .ocorr&ecirc;ncias. ';
$lang['msg_issuemissing']       = 'N&atilde;o existe ocorr&ecirc;ncia com ID ';
$lang['msg_captchawrong']       = 'Resposta errada para a pergunta antispam.';
$lang['msg_descrmodtrue']       = 'Descri&ccedil;&atilde;o modificada com sucesso.';
$lang['msg_slinkmodtrue']       = 'Links do sintoma modificados com sucesso.';
$lang['msg_severitymodtrue']    = 'Severidade modificado com sucesso.';
$lang['msg_statusmodtrue']      = 'Status modificado com sucesso.';
$lang['msg_addFollower_true']   = 'ID: %s -> Seguidor adicionado: ';
$lang['msg_rmvFollower_true']   = 'ID: %s -> Seguidor removido: ';
$lang['msg_addFollower_failed'] = 'ID: %s -> falhou em atualizar o Seguidor: ';
$lang['itd_follower']           = '(Seguidor: %s)';

$lang['lbl_project']            = 'Projeto:';
$lang['lbl_issueid']            = 'ID:';
$lang['lbl_reporter']           = 'Relatado por:';
$lang['lbl_reporterdtls']       = 'Detalhes do Relator';
$lang['lbl_initdescr']          = 'Descri&ccedil;&atilde;o inicial';
$lang['lbl_reportername']       = 'Nome:';
$lang['lbl_reportermail']       = 'Email:';
$lang['lbl_reporterphone']      = 'Telefone:';
$lang['lbl_reporteradcontact']  = 'Adicionar contato:';
$lang['lbl_symptlinks']         = 'Links para arquivos de sintomas';
$lang['lbl_cmts_wlog']          = 'Coment&aacute;rios (registro de trabalho)';
$lang['lbl_cmts_adcmt']         = 'Adicionar um novo coment&aacute;rio';
$lang['lbl_cmts_edtres']        = 'Solu&ccedil;&atilde;o';
$lang['btn_add']                = 'Adicionar';
$lang['btn_add_title']          = 'Adicionar';
$lang['btn_mod']                = 'Atualizar'; // to submit comment modification
$lang['btn_mod_title']          = 'Atualizar';
$lang['del_title']              = 'Excluir este coment&aacute;rio';
$lang['lbl_signin']             = 'Autentique-se</a> se quer adicionar um coment&aacute;rio ou nota de solu&ccedil;&atilde;o.'; // </a> necessary to close the link tag
$lang['lbl_please']             = 'Por favor ';
$lang['lbl_lessPermission']     = 'Seu n&iacute;vel de permiss&atilde;o &eacute; muito baixo. Contate o administrador do wiki.';

$lang['th_project']             = 'Projeto';
$lang['th_id']                  = 'Id';
$lang['th_created']             = 'Criado';
$lang['th_product']             = 'Produto';
$lang['th_version']             = 'Vers&atilde;o';
$lang['th_severity']            = 'Severidade';
$lang['th_status']              = 'Status';
$lang['th_username']            = 'Nome do usu&aacute;rio';
$lang['th_usermail']            = 'Email do usu&aacute;rio';
$lang['th_userphone']           = 'Telefone do usu&aacute;rio';
$lang['th_reporteradcontact']   = 'Adicionar contato';
$lang['th_title']               = 'T&iacute;tulo';
$lang['th_description']         = 'Descri&ccedil;&atilde;o da Ocorr&ecirc;ncia';
$lang['th_sympt']               = 'Link do sintoma ';
$lang['th_assigned']            = 'Atribu&iacute;do a'; 
$lang['th_resolution']          = 'Solu&ccedil;&atilde;o';
$lang['th_modified']            = 'Modificado';
$lang['th_showmodlog']          = 'Hist&oacute;rico de Status';
$lang['h_modlog']               = 'Hist&oacute;rico de modifica&ccedil;&atilde;o do Status #';
$lang['mod_valempty']           = '[exclu&iacute;do]';
$lang['back']                   = 'voltar';
$lang['gen_tab_open']           = 'Detalhes';
$lang['descr_tab_mod']          = 'Modificar';
$lang['cmt_tab_open']           = 'adicionar Coment&aacute;rio';
$lang['cmt_tab_mod']            = 'modificar Coment&aacute;rio';
$lang['rsl_tab_open']           = 'adicionar / modificar Solu&ccedil;&atilde;o';
$lang['dtls_usr_hidden']        = 'detalhes do usu&aacute;rio escondidos';
$lang['minor_mod']              = 'Altera&ccedil;&atilde;o menor';
$lang['minor_mod_cbx_title']    = 'evitar enviar emails depois de altera&ccedil;&otilde;es cosm&eacute;ticas';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['cmnt_new_subject']       = 'Ocorr&ecirc;ncia #%s em %s: novo coment&aacute;rio adicionado'; // $issue['id'], $project
$lang['cmnt_mod_subject']       = 'Ocorr&ecirc;ncia #%s em %s: coment&aacute;rio modificado';  // $issue['id'], $project
$lang['cmnt_del_subject']       = 'Ocorr&ecirc;ncia #%s on %s: coment&aacute;rio exclu&iacute;do';   // $issue['id'], $project
$lang['cmt_del_intro']          = 'Um coment&aacute;rio foi exclu&iacute;do.';
$lang['issuemod_subject']       = 'Ocorr&ecirc;ncia #%s em %s: %s'; //$issue['id'], $project, $column
$lang['issuemod_head']          = 'Caro usu&aacute;rio,';
$lang['issuemod_intro']         = 'A ocorr&ecirc;ncia relatada foi modificada.';
$lang['issuemod_issueid']       = 'ID:     ';
$lang['issuemod_status']        = 'Status:   ';
$lang['issuemod_product']       = 'Produto:   ';
$lang['issuemod_version']       = 'Vers&atilde;o:   ';
$lang['issuemod_severity']      = 'Severidade:   ';
$lang['issuemod_creator']       = 'Criador:   ';
$lang['issuemod_title']         = 'T&iacute;tulo:   ';
$lang['issuemod_cmntauthor']    = 'Coment&aacute;rio por:  ';
$lang['issuemod_date']          = 'submetido em: ';
$lang['issuemod_cmnt']          = 'Coment&aacute;rio:  ';
$lang['issuemod_see']           = 'veja detalhes:  ';
$lang['issuemod_br']            = 'atenciosamente';
$lang['issuemod_end']           = ' Analisador de Ocorr&ecirc;ncias';    // project name placed before this
$lang['issuedescrmod_subject']  = 'Ocorr&ecirc;ncia #%s em %s: descri&ccedil;&atilde;o inicial modificada'; // $issue['id'], $project
$lang['issuemod_changes']       = 'A ocorr&ecirc;ncia mudou em %s de %s para %s.'; //$column, $old_value, $new_value
/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject'] = 'A ocorr&ecirc;ncia %s em %s foi resolvida';
$lang['issue_resolved_intro']   = 'A ocorr&ecirc;ncia relatada foi resolvida.';
$lang['issue_resolved_status']  = 'Resolvido';
$lang['issue_resolved_text']    = 'Solu&ccedil;&atilde;o:   ';
$lang['msg_resolution_true']    = 'Sua Solu&ccedil;&atilde;o foi adicionada com sucesso ao ID';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']       = '%s ocorr&ecirc;ncia relatada para %s no Produto: %s (%s)';
$lang['issuenew_head']          = 'Caro administrador,';
$lang['issuenew_intro']         = 'uma nova ocorr&ecirc;ncia foi criada para o projeto:';
$lang['issuenew_descr']         = 'Descri&ccedil;&atilde;o:  ';

/******************************************************************************/
/* deviations from before for sending an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']  = ' Ocorr&ecirc;ncia %s atribu&iacute;da para voc&ecirc;';
$lang['issueassigned_head']     = 'Ol&aacute;,';
$lang['issueassigned_intro']    = 'foi-lhe atribu&iacute;da a seguinte Ocorr&ecirc;ncia:';
$lang['it__none']               = '--';
/******************************************************************************/
/* following text is related to search feature                                
*/
$lang['lbl_search']             = 'Pesquisar pelo seguinte:&nbsp;&nbsp;';
$lang['btn_search']             = 'Iniciar';
$lang['btn_search_title']       = 'Pesquisar';
$lang['search_hl1']             = '<h1>Pesquisar</h1>';
$lang['search_txt1']            = "Voc&ecirc; pode ver abaixo os resultados da sua pesquisa. Lembre-se que esta &eacute; uma pesquisa simples que procura apenas por palavras e n&atilde;o entende l&oacute;gica de pesquisas.";
$lang['search_hl2']             = '<h2>Resultado</h2>';
$lang['search_Issue']           = 'Ocorr&ecirc;ncia';
$lang['search_Comment']         = 'Coment&aacute;rio';
$lang['search_Type']            = 'Tipo';
$lang['search_ID']              = 'ID';
$lang['search_Subject']         = 'Assunto';

/******************************************************************************/
$lang['table_kit_OK']           = 'OK';
$lang['table_kit_Cancel']       = ' Cancelar';