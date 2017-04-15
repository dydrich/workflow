<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/13/17
 * Time: 6:13 PM
 */
include "../../lib/start.php";
require_once "lib/Workflow.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

$id_workflow = $_REQUEST['id_workflow'];

$sel_wflow = "SELECT * FROM rb_w_workflow WHERE id_workflow = {$id_workflow}";
$res_wflow = $db->executeQuery($sel_wflow);
$wflow = $res_wflow->fetch_assoc();

$sel_gruppi = "SELECT * FROM rb_gruppi WHERE gid BETWEEN 2 AND 5";
$res_gruppi = $db->executeQuery($sel_gruppi);
$gruppi = [];
while ($row = $res_gruppi->fetch_assoc()) {
	if ($row['permessi']&$wflow['gruppi']) {
		$gruppi[] = $row['gid'];
	}
}

$workflow = new \eschool\Workflow($wflow['id_workflow'], $wflow['richiesta'], array(), $gruppi, new MySQLDataLoader($db));

$sel_wsteps = "SELECT * FROM rb_w_step_workflow WHERE id_workflow = {$id_workflow} ORDER BY ordine";
$res_wsteps = $db->executeCount($sel_wsteps);

$sel_steps = "SELECT * FROM rb_w_step ORDER BY id_step";
$res_steps = $db->executeQuery($sel_steps);

$sel_offices = "SELECT * FROM rb_w_uffici ORDER BY id_ufficio";
$res_offices = $db->executeQuery($sel_offices);

$sel_status = "SELECT * FROM rb_w_status ORDER BY id_status";
$res_status = $db->executeQuery($sel_status);

$drawer_label = "Gestione flusso: ".strtolower($wflow['richiesta']);

include "workflow_steps.html.php";