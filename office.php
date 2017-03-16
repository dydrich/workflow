<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/12/17
 * Time: 11:01 AM
 */
include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

if ($_REQUEST['id'] != 0) {
	$sel = "SELECT * FROM rb_w_uffici WHERE id_ufficio = ".$_REQUEST['id'];
	$res = $db->executeQuery($sel);
	$ufficio = $res->fetch_assoc();
	$action = 3;
}
else {
	$action = 1;
}

$drawer_label = "Gestione ufficio";

include "office.html.php";