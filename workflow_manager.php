<?php

include "../../lib/start.php";
require_once "lib/Workflow.php";

ini_set('display_errors', '1');

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

if (isset($_POST['nome_flusso']) && isset($_POST['num_step'])) {
	$nome = trim($_POST['nome_flusso']);
	$num_step = $_POST['num_step'];
}
else {
	$num_step = 0;
	$nome = '';
}

$steps = [];
for ($i = 0; $i < $num_step; $i++) {
	$steps[$i + 1] = [];
}

$gruppi = 0;
if ($_POST['action'] != 1 && $_POST['action'] != 3) {
	$nome = "";
	$_POST['gruppi'] = [];
}
foreach($_POST['gruppi'] as $a) {
	$gruppi += $a;
}

$wflow = new \eschool\Workflow($_POST['_i'], $nome, $steps, $_POST['gruppi'], new MySQLDataLoader($db));

try {
	switch ($_POST['action']) {
		case 1:     // inserimento
			$wflow->insert();
			$msg = "Richiesta registrata";
			break;
		case 2:     // cancellazione
			$wflow->delete();
			//print $statement;
			$msg = "Richiesta cancellata";
			break;
		case 3:     // modifica
			$wflow->update();
			$msg = "Richiesta aggiornata";
			break;
		case "add_step":
			$order = $_POST['num'];
			$step = $_POST['step'];
			$office = $_POST['office'];
			$starting_status = $_POST['status'];
			$field = "final_statuses_step".$order;
			$statuses = $_POST[$field];
			$data = array('id_step' => 0, 'order' => $order, 'office' => $office, 'step_type' => $step, 'status' => $starting_status, 'statuses' => $statuses);
			$wflow->addStep($data, $order);
			$msg = "Dato inserito";
			break;
	}
} catch (MySQLException $ex){
	$response['status'] = "kosql";
	$response['message'] = "Operazione non completata a causa di un errore";
	$response['dbg_message'] = $ex->getMessage();
	$response['query'] = $ex->getQuery();
	echo json_encode($response);
	exit;
}

$response['message'] = $msg;
echo json_encode($response);
exit;
