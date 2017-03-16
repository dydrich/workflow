<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0){
	$sel = "SELECT id_step, descrizione
			FROM rb_w_step 
			WHERE id_step = ".$_REQUEST['id'];
	try{
		$res = $db->executeQuery($sel);
	} catch (MySQLException $ex){
    	$ex->alert();
		exit;
	}
	$step = $res->fetch_assoc();
	$_i = $_REQUEST['id'];
	$action = 3;
}
else{
	$_i = 0;
	$action = 1;
}

$drawer_label = "Step workflow";

include "dettaglio_step.html.php";