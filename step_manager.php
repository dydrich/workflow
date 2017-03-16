<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

$nome = $_POST['nome_step'];
switch($_POST['action']){
	case 1:     // inserimento
		$statement = "INSERT INTO rb_w_step (descrizione) VALUES ('{$nome}')";
		$msg = "Step inserito";
		break;
	case 2:     // cancellazione
		$statement = "DELETE FROM rb_w_step WHERE id_step = ".$_POST['id'];
		//print $statement;
		$msg = "Step cancellato";
		break;
	case 3:     // modifica
		$statement = "UPDATE rb_w_step SET descrizione = '$nome' WHERE id_step = ".$_POST['id'];
		//print $statement;
		$msg = "Step aggiornato";
		break;
}

try{
	$recordset = $db->executeUpdate($statement);
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