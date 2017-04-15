<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/23/17
 * Time: 1:03 PM
 */
require_once "../../lib/start.php";
require_once "lib/AdimistrativeRequest.php";
require_once "lib/DailyPermitAdministrativeRequest.php";
require_once "lib/LeaveAdministrativeRequest.php";

check_session();
check_permission(SEG_PERM|DIR_PERM|DSG_PERM);

ini_set('display_errors', '1');

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

switch ($_POST['action']) {
	case 'advance':
		if ($_POST['requestType'] == 1 || $_POST['requestType'] == 3) {
			$req = new \eschool\DailyPermitAdministrativeRequest($_POST['idw'], null, null, new MySQLDataLoader($db), null);
		}
		else if ($_POST['requestType'] == 2 || $_POST['requestType'] == 4) {
			$req = new \eschool\LeaveAdministrativeRequest($_POST['idw'], null, null, new MySQLDataLoader($db), null);
		}
		$req->advanceRequest();
		break;
	case 'close':
		$req = new \eschool\DailyPermitAdministrativeRequest($_POST['idw'], null, null, new MySQLDataLoader($db), null);
		$status = $_POST['status'];
		$req->closeRequest($status);
		break;
	case 'protocol':
		$req = new \eschool\DailyPermitAdministrativeRequest($_POST['idw'], null, null, new MySQLDataLoader($db), null);
		$protocol = $_POST['protocol'];
		$req->setProtocol($protocol);
		$req->advanceRequest();
		$response['protocollo'] = $protocol;
		$response['message'] = 'Protocollo: '.$protocol;
		break;
}

$res = json_encode($response);
echo $res;
exit;