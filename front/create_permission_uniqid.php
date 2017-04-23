<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/12/17
 * Time: 9:40 AM
 */
require_once "../../../lib/start.php";

check_session();
check_permission(DOC_PERM|ATA_PERM|SEG_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

$uniqid = uniqid('PRMD', false);
$uniqid = strtoupper($uniqid).date('Y');
$user = $_SESSION['__user__']->getUid();
$idw = $_POST['idw'];

$id_req = $db->executeUpdate("INSERT INTO rb_w_richieste (richiedente, operatore, data_ora, workflow, codice_pratica, area) VALUES ($user, 0, NOW(), {$idw}, '', '{$_SESSION['__mod_area__']}')");
$uniqid .= "-".$id_req;
$db->executeUpdate("UPDATE rb_w_richieste SET codice_pratica = '{$uniqid}' WHERE id_richiesta = $id_req");
$db->executeUpdate("INSERT INTO rb_w_dati_richiesta (id_richiesta) VALUES ({$id_req})");

$response['message'] = 'Il codice della tua pratica è: '.$uniqid;
$response['requestID'] = $id_req;

$res = json_encode($response);
echo $res;
exit;
