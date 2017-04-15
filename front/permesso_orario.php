<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/19/17
 * Time: 7:18 PM
 */
require_once "../../../lib/start.php";

ini_set('display_errors', 1);

check_session();
check_permission(DOC_PERM);

$_SESSION['__path_to_root__'] = "../../../";
$_SESSION['__path_to_reg_home__'] = "../";

$user = $_SESSION['__user__']->getUid();

$sel = "SELECT * FROM rb_w_richieste, rb_w_workflow WHERE id_richiesta = {$_GET['idr']} AND workflow = id_workflow";
$res = $db->executeQuery($sel);
$request = $res->fetch_assoc();

$req_data = null;
$sel_data = "SELECT intero1, DATE(data1) AS data1 FROM rb_w_dati_richiesta WHERE id_richiesta = ".$request['id_richiesta'];
$res_data = $db->executeQuery($sel_data);
if ($res_data->num_rows > 0) {
	$req_data = $res_data->fetch_assoc();
}

$sel_t = "SELECT * FROM rb_w_motivi_permesso ORDER BY id";
$res_t = $db->executeQuery($sel_t);

$navigation_label = "registro elettronico ";
$drawer_label = $request['richiesta'];

include "permesso_orario.html.php";