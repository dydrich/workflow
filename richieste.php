<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/22/17
 * Time: 8:59 PM
 */
ini_set('display_errors', '1');
require_once "../../lib/start.php";
require_once "lib/DailyPermitAdministrativeRequest.php";
require_once "lib/LeaveAdministrativeRequest.php";
require_once "lib/Leave.php";

check_session();
check_permission(DSG_PERM|DIR_PERM|SEG_PERM);

$_SESSION['__path_to_root__'] = "../../";
$_SESSION['__path_to_reg_home__'] = "./";
$req_area = "";
if(isset($_REQUEST['area']) && $_REQUEST['area'] != "all") {
	$req_area = "AND (area = '{$_REQUEST['area']}'";
	if ($_REQUEST['area'] == 'ata') {
		$req_area .= " OR area = 'manager'";
	}
	$req_area .= ")";
}

$user = $_SESSION['__user__']->getUid();

$sel_perms = "SELECT rb_w_dati_richiesta.id_richiesta, id_workflow, richiedente, area, COALESCE(rb_w_richieste.protocollo, 'non presente') AS protocollo, operatore, data_ora, codice_pratica, COALESCE(stato, 0) AS stato, ufficio, richiesta, motivo, DATE(data1) AS data1, DATE(COALESCE(data2, data1)) AS data2, testo1, intero1, COALESCE(intero2, 1) AS intero2
			  FROM rb_w_richieste, rb_w_workflow, rb_w_motivi_permesso, rb_w_dati_richiesta 
			  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
			  AND intero1 = id 
			  AND id_workflow = workflow 
			  AND (stato IS NULL OR stato <> 5) 
			  AND data1 >= NOW()
			  AND id_workflow = {$_REQUEST['idw']} 
			  $req_area
			  ORDER BY data_ora DESC, data1 DESC";
$res_perms = $db->executeQuery($sel_perms);

$navigation_label = "registro elettronico ";
$drawer_label = "Richieste di permesso";
if (($_REQUEST['idw']|2) == 0) {
	$drawer_label .= " orario";
}
else {
	$drawer_label .= " giornaliero";
}

include "richieste.html.php";