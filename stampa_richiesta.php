<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 26/05/17
 * Time: 19.09
 */
ini_set("display_errors", 1);
require_once "../../lib/start.php";
require_once "../../lib/SchoolPDF.php";
require_once "../../lib/RBUtilities.php";
require_once "lib/AdministrativeRequest.php";
require_once "lib/DailyPermitAdministrativeRequest.php";
require_once "lib/LeaveAdministrativeRequest.php";

check_session();
check_permission(DOC_PERM|ATA_PERM|SEG_PERM|DSG_PERM);

$_SESSION['__path_to_root__'] = "../../";
$_SESSION['__path_to_reg_home__'] = "./";

$author = $_SESSION['__user__']->getFullName();

$idw = $_REQUEST['req'];
$wtype = $_REQUEST['wtype'];

if ($wtype == 1) {
	$req = new \eschool\DailyPermitAdministrativeRequest($idw, null, null, new MySQLDataLoader($db), null);
}
else {
	$req = new \eschool\LeaveAdministrativeRequest($idw, null, null, new MySQLDataLoader($db), null);
}

class MYPDF extends SchoolPDF {

	public function printBody($adminReq, $db) {

		$rb = RBUtilities::getInstance($db);
		setlocale(LC_TIME, 'it_IT.utf8');

		$this->SetFont('', 'B');
		$reqT = $adminReq->getRequestType();
		$label1 = 'Ricevuta di richiesta di ';
		if ($reqT == 1) {
			$label1 .= "permesso giornaliero";
		}
		else {
			$label1 .= "permesso orario";
		}
		$this->Cell(200, 5, $label1, 0, 1, 'C', 0);
		$this->SetFont('', '');

		$uid = $adminReq->getUser();
		$user = $rb->loadUserFromUid($uid, 'school');
		$this->Cell(200, 15, "", 0, 1, 'C', 0);
		$this->SetFont('', 'B');
		$this->Cell(200, 20, "Protocollo: ".$adminReq->getProtocol(), 0, 1, 'L', 0);
		$this->SetFont('', '');
		$this->Cell(200, 10, "Richiedente: ".$user->getFullName(), 0, 1, 'L', 0);
		$this->Cell(200, 10, "Richiesto in data: ".format_date(substr($adminReq->getDateTime(), 0, 10), SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ore ".substr($adminReq->getDateTime(), 11, 5), 0, 1, 'L', 0);
		$starting_day = strftime("%A %d %B %Y", strtotime($adminReq->getDay()));

		if ($reqT == 1) {
			$ending_day = strftime("%A %d %B %Y", strtotime($adminReq->getEndDay()));
			if ($adminReq->getNumberOfDays() == 1) {
				$this->Cell(200, 10, "Giorno richiesto: ".$starting_day, 0, 1, 'L', 0);
			}
			else {
				$this->Cell(200, 10, "Giorni richiesti: ".$adminReq->getNumberOfDays().", da ".$starting_day." a ".$ending_day, 0, 1, 'L', 0);
			}
			$this->Cell(200, 10, "Motivo della richiesta: ".$adminReq->getReason()['desc'], 0, 1, 'L', 0);
			if ($adminReq->getReason()['id'] == 4) {
				$this->Cell(200, 10, "Note: ".$adminReq->getReason()['note'], 0, 1, 'L', 0);
			}
		}
		else {
			$this->Cell(200, 10, "Giorno della richiesta: ".$starting_day, 0, 1, 'L', 0);
			$this->Cell(200, 10, "Ore richieste: ".$adminReq->getNumberOfHours().", dalle ".substr($adminReq->getEnter(), 0, 5)." alle ".substr($adminReq->getExit(), 0, 5), 0, 1, 'L', 0);
		}

	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($author);
$pdf->SetTitle('Ricevuta di richiesta permesso');

// set default header data
$pdf->SetHeaderData("", 0, $_SESSION['__config__']['intestazione_scuola'], $_SESSION['__config__']['indirizzo_scuola']);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8.0));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 8.0));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage("P");

//Column titles
$header = array('Ora', 'Lun', 'Mar', 'Mer');

// print colored table
$pdf->printBody($req, $db);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('ricevuta.pdf', 'D');