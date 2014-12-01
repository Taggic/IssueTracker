<?php
/**
 * Arquivo de linguagem em Português do Brasil para IssueTracker
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Benedito Carneiro <ctsbc@yahoo.com.br>
 */
/******************************************************************************/
// Issue Report controls
$lang['msg_reporttrue'] = 'Seu relatório foi armazenado com sucesso como ocorrência #';
$lang['wmsg1'] = 'Informe um endereço de email válido, de preferência o seu, para esclarecimentos e/ou retorno sobre a ocorrência relatada.';
$lang['wmsg2'] = 'Informe uma versão de produto válida para relatar adequadamente esta ocorrência.';
$lang['wmsg3'] = 'Informe uma descrição melhor da sua ocorrência.';
$lang['wmsg4'] = '&nbsp;Por favor, <a href="?do=login&amp" class="action login" accesskey="" rel="nofollow" style="color:blue;text-decoration:underline;" title="Autenticação">Autentique-se</a> se quer relatar uma ocorrência.';
$lang['wmsg5'] = 'Informe um título adequadamente descritivo para a ocorrência.';
$lang['wmsg6'] = 'Erro de Upload: O tamanho máximo do arquivo (%s bytes) foi ultrapassado.';
$lang['wmsg7'] = 'Erro de Upload: A extensão do arquivo é inválida.';
$lang['wmsg8'] = 'Erro de Upload: O Mime-Type do arquivo não existe ou não é suportado.';
$lang['wmsg9'] = 'Segurança Antispam: O upload do seu IP está bloqueado por mais %s minutos, pelo menos.';
$lang['lbl_symptomupload'] = 'Upload de arquivo de sintomas:';
$lang['btn_reportsave'] = 'Enviar';
// further settings see 'th_...' options at Issue List controls section below 

/******************************************************************************/
// Issue List controls
$lang['lbl_issueqty']           = 'Quantidade de ocorrências:&nbsp;';
$lang['lbl_scroll']             = 'Rolar a lista de ocorrências: &nbsp;&nbsp;&nbsp;';
$lang['lbl_filtersev']          = 'Filtrar Severidade:&nbsp;';
$lang['lbl_filterstat']         = 'Filtrar Status:&nbsp;';
$lang['lbl_filterprod']         = 'Filtrar Produto:&nbsp;';
$lang['lbl_filtervers']         = 'Filtrar Versão:&nbsp;';
$lang['lbl_filtercomp']         = 'Filtrar Módulo:&nbsp;';
$lang['lbl_filterblock']        = 'Filtrar Test blocking:&nbsp;';
$lang['lbl_filterassi']         = 'Filtrar Delegado:&nbsp;';
$lang['lbl_filterreporter']     = 'Filtrar Repórter:&nbsp;';
$lang['cbx_myissues']           = 'Minha assunto:&nbsp;';
$lang['btn_go']                 = 'OK';

$lang['btn_previuos']           = '<<<';
$lang['btn_previuos_title']     = 'Ocorrências anteriores';
$lang['btn_next']               = '>>>';
$lang['btn_next_title']         = 'próximas Ocorrências';

$lang['lbl_showid']             = 'Mostrar detalhes da ocorrência:';
$lang['btn_showid']             = 'Mostrar';
$lang['btn_showid_title']       = 'Mostrar';

$lang['lbl_sort']               = 'Ordenar por:';
$lang['btn_sort']               = 'Ordenar';
$lang['btn_sort_title']         = 'Ordenar por sua chave global.';

$lang['msg_commentfalse']       = 'Este comentário já existe e não foi adicionado novamente.';
$lang['msg_commenttrue']        = 'Seu comentário foi armazenado com sucesso com ID #';
$lang['msg_wroundtrue']         = 'Minha solução foi salvo com sucesso.';
$lang['msg_commentmodtrue']     = 'Seu comentário foi modificado com sucesso com ID #';
$lang['msg_commentdeltrue']     = 'Seu comentário #%s foi excluído com sucesso.';
$lang['msg_commentmodfalse']    = 'Não tem diferença do comentário ID #';
$lang['msg_pfilemissing']       = 'O arquivo do projeto não existe: %s.issues. ';
$lang['msg_issuemissing']       = 'Não existe ocorrência com ID ';
$lang['msg_captchawrong']       = 'Resposta errada para a pergunta antispam.';
$lang['msg_descrmodtrue']       = 'Descrição modificada com sucesso.';
$lang['msg_slinkmodtrue']       = 'Links do sintoma modificados com sucesso.';
$lang['msg_severitymodtrue']    = 'Gravidade alterada com sucesso.';
$lang['msg_statusmodtrue']      = 'Status alterado com sucesso.';
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
$lang['lbl_initdescr']          = 'Descrição inicial';
$lang['lbl_reportername']       = 'Nome:';
$lang['lbl_reportermail']       = 'Email:';
$lang['lbl_reporterphone']      = 'Telefone:';
$lang['lbl_reporteradcontact']  = 'Adicionar contato:';
$lang['lbl_symptlinks']         = 'Links para arquivos de sintomas';
$lang['lbl_cmts_wlog']          = 'Comentários (registro de trabalho)';
$lang['lbl_cmts_adcmt']         = 'Adicionar um novo comentário';
$lang['lbl_cmts_edtres']        = 'Solução';
$lang['btn_add']                = 'Adicionar';
$lang['btn_add_title']          = 'Adicionar';
$lang['btn_mod']                = 'Atualizar'; // to submit comment modification
$lang['btn_mod_title']          = 'Atualizar';
$lang['del_title']              = 'Excluir este comentário';
$lang['lbl_signin']             = 'Autentique-se</a> se quer adicionar um comentário ou nota de solução.'; // </a> necessary to close the link tag
$lang['lbl_please']             = 'Por favor ';
$lang['lbl_lessPermission']     = 'Seu nível de permissão é muito baixo. Contate o administrador do wiki.';
$lang['lbl_workaround']         = 'Solução';

$lang['th_project']             = 'Projeto';
$lang['th_id']                  = 'Id';
$lang['th_created']             = 'Criado';
$lang['th_product']             = 'Produto';
$lang['th_components']          = 'Módulo';
$lang['th_tblock']              = 'Teste bloqueado';
$lang['th_tversion']            = 'Versão alvo';
$lang['th_begin']               = 'Começo';
$lang['th_deadline']            = 'Prazo de entrega';
$lang['th_progress']            = 'Progresso em %';
$lang['th_version']             = 'Versão';
$lang['th_severity']            = 'Severidade';
$lang['th_status']              = 'Status';
$lang['th_user_name']            = 'Nome do usuário';
$lang['th_usermail']            = 'Email do usuário';
$lang['th_userphone']           = 'Telefone do usuário';
$lang['th_reporteradcontact']   = 'Adicionar contato';
$lang['th_title']               = 'Título';
$lang['th_description']         = 'Descrição da Ocorrência';
$lang['th_sympt']               = 'Link do sintoma ';
$lang['th_assigned']            = 'Atribuído a'; 
$lang['th_resolution']          = 'Solução';
$lang['th_modified']            = 'Modificado';
$lang['th_showmodlog']          = 'Histórico de Status';
$lang['h_modlog']               = 'Histórico de modificação do Status #';
$lang['mod_valempty']           = '[excluído]';
$lang['back']                   = 'voltar';
$lang['gen_tab_open']           = 'Detalhes';
$lang['descr_tab_mod']          = 'Modificar';
$lang['cmt_tab_open']           = 'adicionar Comentário';
$lang['cmt_tab_mod']            = 'modificar Comentário';
$lang['rsl_tab_open']           = 'adicionar / modificar Solução';
$lang['dtls_usr_hidden']        = 'detalhes do usuário escondidos';
$lang['minor_mod']              = 'Alteração menor';
$lang['minor_mod_cbx_title']    = 'evitar enviar emails depois de alterações cosméticas';
/******************************************************************************/
/* send an e-mail to user due to issue modificaion
/* _emailForIssueMod
*/                            
$lang['cmnt_new_subject']       = 'Ocorrência #%s em %s: novo comentário adicionado'; // $issue['id'], $project
$lang['cmnt_mod_subject']       = 'Ocorrência #%s em %s: comentário modificado';  // $issue['id'], $project
$lang['cmnt_del_subject']       = 'Ocorrência #%s on %s: comentário excluído';   // $issue['id'], $project
$lang['cmt_del_intro']          = 'Um comentário foi excluído.';
$lang['issuemod_subject']       = 'Ocorrência #%s em %s: %s'; //$issue['id'], $project, $column
$lang['issuemod_head']          = 'Caro usuário,';
$lang['issuemod_intro']         = 'A ocorrência relatada foi modificada.';
$lang['issuemod_issueid']       = 'ID:              ';
$lang['issuemod_status']        = 'Status:          ';
$lang['issuemod_product']       = 'Produto:         ';
$lang['issuemod_version']       = 'Versão:          ';
$lang['issuemod_severity']      = 'Severidade:      ';
$lang['issuemod_creator']       = 'Criador:         ';
$lang['issuemod_assignee']      = 'Atribuído a:     ';
$lang['issuemod_title']         = 'Título:          ';
$lang['issuemod_cmntauthor']    = 'Comentário por:  ';
$lang['issuemod_date']          = 'submetido em:    ';
$lang['issuemod_cmnt']          = 'Comentário:      ';
$lang['issuemod_see']           = 'veja detalhes:   ';
$lang['issuemod_br']            = 'atenciosamente';
$lang['issuemod_end']           = ' Analisador de Ocorrências';    // project name placed before this
$lang['issuedescrmod_subject']  = 'Ocorrência #%s em %s: descrição inicial modificada'; // $issue['id'], $project
$lang['issuemod_changes']       = 'A ocorrência mudou em %s de %s para %s.'; //$column, $old_value, $new_value
$lang['btn_upd_addinfo']        = 'Enviar';
/******************************************************************************/
/* send an e-mail to user due to issue set to resolved on details
/* _emailForResolutionMod
*/                            
$lang['issue_resolved_subject'] = 'A ocorrência %s em %s foi resolvida';
$lang['issue_resolved_intro']   = 'A ocorrência relatada foi resolvida.';
$lang['issue_resolved_status']  = 'Resolvido';
$lang['issue_resolved_text']    = 'Solução:   ';
$lang['msg_resolution_true']    = 'Sua Solução foi adicionada com sucesso ao ID';

/******************************************************************************/
/* deviations from before for send an e-mail to admin due to new issue created
/* _emailForNewIssue
*/
$lang['issuenew_subject']       = '%s ocorrência relatada para %s no Produto: %s (%s)';
$lang['issuenew_head']          = 'Caro administrador,';
$lang['issuenew_intro']         = 'uma nova ocorrência foi criada para o projeto:';
$lang['issuenew_descr']         = 'Descrição:  ';

/******************************************************************************/
/* deviations from before for sending an e-mail to assignee
/* _emailForNewIssue
*/
$lang['issueassigned_subject']  = 'Ocorrência %s em %s atribuída para você';
$lang['issueassigned_head']     = 'Olá,';
$lang['issueassigned_intro']    = 'foi-lhe atribuída a seguinte Ocorrência:';
$lang['it__none']               = '--';
/******************************************************************************/
/* following text is related to search feature                                
*/
$lang['lbl_search']             = 'Pesquisar pelo seguinte:&nbsp;&nbsp;';
$lang['btn_search']             = 'Iniciar';
$lang['btn_search_title']       = 'Pesquisar';
$lang['search_hl1']             = '<h1>Pesquisar</h1>';
$lang['search_txt1']            = "Você pode ver abaixo os resultados da sua pesquisa. Lembre-se que esta é uma pesquisa simples que procura apenas por palavras e não entende lógica de pesquisas.";
$lang['search_hl2']             = '<h2>Resultado</h2>';
$lang['search_Issue']           = 'Ocorrência';
$lang['search_Comment']         = 'Comentário';
$lang['search_Type']            = 'Tipo';
$lang['search_ID']              = 'ID';
$lang['search_Subject']         = 'Assunto';

/******************************************************************************/
$lang['table_kit_OK']           = 'OK';
$lang['table_kit_Cancel']       = ' Cancelar';