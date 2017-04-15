<?php

/**
 * load the requested module
 */

require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$module_code = $_REQUEST['module'];

$sel_module = "SELECT * FROM rb_modules WHERE code_name = '{$module_code}'";
$res_module = $db->execute($sel_module);
$module = $res_module->fetch_assoc();

$_SESSION['__modules__'][$module_code]['home'] = $module['home'];
$_SESSION['__modules__'][$module_code]['lib_home'] = $module['lib_home'];
$_SESSION['__modules__'][$module_code]['front_page'] = $module['front_page'];
$_SESSION['__modules__'][$module_code]['path_to_root'] = $module['path_to_root'];

$_SESSION['__mod_area__'] = $_REQUEST['area'];

$user_type = "";
if ($_SESSION['__user__'] instanceof SchoolUserBean){
	$user_type = "school";
}
else if ($_SESSION['__user__'] instanceof ParentBean){
	$user_type = "parent";
}
else {
	$user_type = "student";
}
$_SESSION['user_type'] = $user_type;

/*
 * load data
 */
$steps = [];
$statuses[0] = ['id_status' =>0, 'nome' => 'In attesa'];
$offices = [];
$wflows = [];
$res_steps = $db->executeQuery("SELECT * FROM rb_w_step ORDER BY id_step");
while ($step = $res_steps->fetch_assoc()) {
	$steps[$step['id_step']] = $step;
}
$res_statuses = $db->executeQuery("SELECT * FROM rb_w_status ORDER BY id_status");
while ($status = $res_statuses->fetch_assoc()) {
	$statuses[$status['id_status']] = $status;
}
$res_offices = $db->executeQuery("SELECT * FROM rb_w_uffici ORDER BY id_ufficio");
while ($office = $res_offices->fetch_assoc()) {
	$offices[$office['id_ufficio']] = $office;
}
$res_wflows = $db->executeQuery("SELECT * FROM rb_w_workflow ORDER BY id_workflow");
while ($wflow = $res_wflows->fetch_assoc()) {
	$wflows[$wflow['id_workflow']] = $wflow;
}
$_SESSION['steps'] = $steps;
$_SESSION['statuses'] = $statuses;
$_SESSION['offices'] = $offices;
$_SESSION['wflows'] = $wflows;

if (isset($_REQUEST['page'])){
	if ($_REQUEST['page'] == 'admin') {
		$header = "../../../intranet/{$_SESSION['__mod_area__']}/header.php";
		$footer = "../../../intranet/{$_SESSION['__mod_area__']}/footer.php";
		if ($_REQUEST['area'] == 'admin') {
			$header = "../../../admin/header.php";
			$footer = "../../../admin/footer.php";
		}
		$_SESSION['header'] = $header;
		$_SESSION['footer'] = $footer;
		header("Location: admin/index.php");
	}
	else if ($_REQUEST['page'] == 'front') {
		header("Location: front/front.php");
	}
	else {
		header("Location: {$_REQUEST['page']}.php");
	}
}
else {
	header("Location: {$module['front_page']}");
}
