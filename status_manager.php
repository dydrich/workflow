<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

$nome = $db->real_escape_string($_POST['nome_status']);
$permessi = 0;
foreach($_POST['permessi'] as $a) {
	$permessi += $a;
}
switch($_POST['action']){
	case 1:     // inserimento
		$statement = "INSERT INTO rb_w_status (nome, permessi) VALUES ('{$nome}', $permessi)";
		$msg = "Stato inserito correttamente";
		break;
	case 2:     // cancellazione
		$statement = "DELETE FROM rb_w_status WHERE id_status = ".$_POST['id'];
		//print $statement;
		$msg = "Stato cancellato correttamente";
		break;
	case 3:     // modifica
		$statement = "UPDATE rb_w_status SET nome = '$nome', permessi = $permessi WHERE id_status = ".$_POST['id'];
		//print $statement;
		$msg = "Stato aggiornato correttamente";
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