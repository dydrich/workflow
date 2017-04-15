<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/11/17
 * Time: 10:11 PM
 */
require_once "../../../lib/start.php";
require_once "../lib/define.php";

ini_set('display_errors', 1);

check_session();
check_permission(DOC_PERM|ATA_PERM);

$_SESSION['__path_to_root__'] = "../../../";
$_SESSION['__path_to_reg_home__'] = "../";

$user = $_SESSION['__user__']->getUid();

$id_workflow = DAILY_LEAVE_TEACHER;
if ($_SESSION['__area__'] == 'ata') {
	$id_workflow = DAILY_LEAVE_ATA;
}
$_SESSION['wflow_type'] = $id_workflow;

$sel_perms = "SELECT rb_w_richieste.id_richiesta, richiedente, COALESCE(rb_w_richieste.protocollo, 'non presente') AS protocollo, operatore, data_ora, codice_pratica, COALESCE(stato, 0) AS stato, COALESCE(ufficio, 1) AS ufficio, richiesta, motivo, DATE(data1) AS data1 
			  FROM rb_w_richieste, rb_w_workflow, rb_w_motivi_permesso, rb_w_dati_richiesta 
			  WHERE id_workflow = {$id_workflow}
			  AND richiedente = {$user} 
			  AND rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
			  AND intero1 = id 
			  AND id_workflow = workflow 
			  AND (stato IS NULL OR stato <> 5) 
			  ORDER BY data_ora DESC";
$res_perms = $db->executeQuery($sel_perms);

$navigation_label = "registro elettronico ";
$drawer_label = "Richieste di permesso giornaliero";

include "permessi_giornalieri.html.php";