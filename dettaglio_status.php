<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

$sel_uffici = "SELECT * FROM rb_w_uffici";
$res_uffici = $db->executeQuery($sel_uffici);

if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0){
	$sel = "SELECT id_status, rb_w_status.nome, permessi, rb_w_uffici.nome AS uff, id_ufficio, codice_permessi 
			FROM rb_w_status, rb_w_uffici 
			WHERE permessi&codice_permessi 
			AND id_status =".$_REQUEST['id'];
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
	$action = 3;
}
else{
	$_i = 0;
	$action = 1;
}

$drawer_label = "Dettaglio stato pratica";

include "dettaglio_status.html.php";
