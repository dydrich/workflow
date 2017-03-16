<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 2/6/17
 * Time: 7:23 PM
 */
include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

switch ($_REQUEST['action']) {
	case '3':
		$nome = $_POST['nome'];
		$id_ufficio = $_POST['id'];
		$update = "UPDATE rb_w_uffici SET nome = '{$nome}' WHERE id_ufficio = ".$id_ufficio;
		try{
			$r_upd = $db->executeUpdate($update);
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['message'] = "Operazione UPDATE non completata a causa di un errore";
			$response['dbg_message'] = $ex->getMessage();
			$response['query'] = $ex->getQuery();
			echo json_encode($response);
			exit;
		}
		$msg = 'Ufficio modificato';
		break;
	case '1':
		$sel_max = "SELECT MAX(codice_permessi) AS max FROM rb_w_uffici";
		$_max = $db->executeCount($sel_max);
		$max = $_max*2;
		if($max == 0)
			$max = 1;
		$nome = $_REQUEST['nome'];
		$insert = "INSERT INTO rb_w_uffici (nome, codice_permessi) VALUES ('".$nome."', $max)";
		try{
			$r_ins = $db->executeUpdate($insert);
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['message'] = "Operazione INSERT non completata a causa di un errore";
			$response['dbg_message'] = $ex->getMessage();
			$response['query'] = $ex->getQuery();
			echo json_encode($response);
			exit;
		}
		$msg = 'Ufficio inserito';
		break;
	case '2':
		$id = $_REQUEST['id'];
		$delete = "DELETE FROM rb_w_uffici WHERE id_ufficio = $id";
		try{
			$r_del = $db->executeUpdate($delete);
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['message'] = "Operazione non completata a causa di un errore";
			$response['dbg_message'] = $ex->getMessage();
			$response['query'] = $ex->getQuery();
			echo json_encode($response);
			exit;
		}
		$msg = "Ufficio cancellato";
		break;
}

$response['message'] = $msg;
echo json_encode($response);
exit;