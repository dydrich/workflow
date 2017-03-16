<?php

include "../../lib/start.php";
require_once "lib/Workflow.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

$nome = trim($_POST['nome_flusso']);
$num_step = $_POST['num_step'];

$steps = [];
for ($i = 0; $i < $num_step; $i++) {
	$steps[] = "";
}

$gruppi = 0;
foreach($_POST['gruppi'] as $a) {
	$gruppi += $a;
}

if ($_POST['action'] == 2) {
	$nome = "";
	$_POST['gruppi'] = [];
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
