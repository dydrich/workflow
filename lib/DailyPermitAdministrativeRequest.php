<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/23/17
 * Time: 9:27 AM
 */

namespace eschool;

require_once "AdministrativeRequest.php";

class DailyPermitAdministrativeRequest extends AdministrativeRequest
{

	protected $day;
	protected $endDay;
	protected $reason;
	protected $numberOfDays;

	protected function loadData() {
		/*
		 * load data from database
		 */
		$basic_data = $this->datasource->executeQuery("SELECT * FROM rb_w_richieste WHERE id_richiesta = {$this->requestID}");
		$this->requestCode = $basic_data[0]['codice_pratica'];
		$this->requestType = $basic_data[0]['workflow'];
		$this->workflow = new Workflow($this->requestType, null, null, null, $this->datasource);
		$this->user = $basic_data[0]['richiedente'];
		$this->protocol = $basic_data[0]['protocollo'];

		$d1 = $this->datasource->executeQuery("SELECT richiedente, operatore, data_ora, codice_pratica, COALESCE(stato, 0), COALESCE(ufficio, 0), intero1, DATE(data1) AS data1, DATE(COALESCE(data2, data1)) AS data2, COALESCE(intero2, 1) AS intero2, motivo, testo1
											  FROM rb_w_richieste, rb_w_dati_richiesta, rb_w_motivi_permesso
											  WHERE rb_w_dati_richiesta.id_richiesta = rb_w_richieste.id_richiesta 
											  AND id = intero1 
											  AND rb_w_richieste.id_richiesta = {$this->requestID}");
		$this->dateTime = $d1[0]['data_ora'];
		$this->day = $d1[0]['data1'];
		$this->reason = ['id' => $d1[0]['intero1'], 'desc' => $d1[0]['motivo'], 'note' => $d1[0]['testo1']];
		$this->endDay = $d1[0]['data2'];
		$this->numberOfDays = $d1[0]['intero2'];
	}

	public function delete() {
		// TODO: Implement delete() method.
	}

	/**
	 * @return mixed
	 */
	public function getDay() {
		return $this->day;
	}

	/**
	 * @param mixed $day
	 */
	public function setDay($day) {
		$this->day = $day;
	}

	/**
	 * @return mixed
	 */
	public function getEndDay() {
		return $this->endDay;
	}

	/**
	 * @param mixed $endDay
	 */
	public function setEndDay($endDay) {
		$this->endDay = $endDay;
	}

	/**
	 * @return mixed
	 */
	public function getNumberOfDays() {
		return $this->numberOfDays;
	}

	/**
	 * @param mixed $numberOfDays
	 */
	public function setNumberOfDays($numberOfDays) {
		$this->numberOfDays = $numberOfDays;
	}

	/**
	 * @return mixed
	 */
	public function getReason() {
		return $this->reason;
	}

	/**
	 * @param mixed $reason
	 */
	public function setReason($reason) {
		$this->reason = $reason;
	}
}