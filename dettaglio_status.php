<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM);

$sel_uffici = "SELECT * FROM w_uffici";
$res_uffici = $db->executeQuery($sel_uffici);

if(isset($_REQUEST['id'])){
	$sel = "SELECT id_status, w_status.nome, permessi, w_uffici.nome AS uff, id_ufficio, codice_permessi FROM w_status, w_uffici WHERE permessi&codice_permessi AND id_status =".$_REQUEST['id'];
	//print $sel;
	try{
		$res = $db->executeQuery($sel);
	} catch (MySQLException $ex){
		$ex->alert();
		exit;
	}
	$_i = $_REQUEST['id'];
	$perms = 0;
	while($s = $res->fetch_assoc()){
		// carico in un array id uffici
		$perms += $s['codice_permessi'];
		$status = $s['nome'];
		$id_status = $s['id_status'];
	}
}
else{
	$_i = 0;
}

include "dettaglio_status.html.php";
