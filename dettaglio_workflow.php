<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

$sel_gruppi = "SELECT * FROM rb_gruppi WHERE gid BETWEEN 2 AND 5";
$res_gruppi = $db->executeQuery($sel_gruppi);

$sel_step = "SELECT * FROM rb_w_step";
$res_step = $db->executeQuery($sel_step);

if(isset($_REQUEST['id']) && ($_REQUEST['id'] != 0)){
	$sel_flow = "SELECT * FROM rb_w_workflow WHERE id_workflow = ".$_REQUEST['id'];
	//print $sel_flow;
	try{
		$res_flow = $db->executeQuery($sel_flow);
	} catch (MySQLException $ex){
		$ex->alert();
		exit;
	}
	$flow = $res_flow->fetch_assoc();

    $_i = $_REQUEST['id'];
    $sel_count_groups = "SELECT COUNT(*) AS ct FROM rb_gruppi WHERE permessi&".$flow['gruppi'];
    try{
    	$ct = $db->executeCount($sel_count_groups);
    } catch (MySQLException $ex){
		$ex->alert();
		exit;
	}
	$action = 3;
}
else{
	$action = 1;
	$_i = 0;
}

$drawer_label = "Flusso di lavorazione richiesta";

include "dettaglio_workflow.html.php";