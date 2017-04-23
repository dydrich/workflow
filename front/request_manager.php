<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/22/17
 * Time: 7:31 PM
 */
require_once "../../../lib/start.php";
require_once "../lib/DailyLeave.php";
require_once "../lib/Leave.php";

check_session();
check_permission(DOC_PERM|ATA_PERM|SEG_PERM|DSG_PERM);

header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

switch ($_POST['action']) {
	case 'save_data':
		$req = new \eschool\DailyLeave($_POST['idr'], $_POST['code'], $_POST['req_type'], new MySQLDataLoader($db));
		$req->setReason($_POST['reason']);
		$req->setDay(format_date($_POST['date'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"));
		$req->saveData();
		break;
	case 'del_request':
		$req = new \eschool\DailyLeave($_POST['idr'], '', 0, new MySQLDataLoader($db));
		$req->delete();
		break;
	case 'save_data_h':
		$req = new \eschool\Leave($_POST['idr'], $_POST['code'], $_POST['req_type'], new MySQLDataLoader($db));
		$req->setDay(format_date($_POST['date'], IT_DATE_STYLE, SQL_DATE_STYLE, "-"));
		$req->setEnter($_POST['start']);
		$req->setExit($_POST['end']);
		$req->setNumberOfHours($_POST['noh']);
		$req->saveData();
		break;
}

$res = json_encode($response);
echo $res;
exit;