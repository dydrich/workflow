<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/22/17
 * Time: 7:08 PM
 */

namespace eschool;

require_once "Request.php";


class DailyLeave extends Request
{
	protected $day;
	protected $endDay;
	protected $reason;
	protected $numberOfDays;

	protected function loadData() {
		/*
		 * load data from database
		 */
		$d1 = $this->datasource->executeQuery("SELECT richiedente, operatore, data_ora, codice_pratica, COALESCE(stato, 0) AS stato, COALESCE(ufficio, 1) AS ufficio, intero1, COALESCE(intero2, 1), DATE(data1) AS data1, DATE(COALESCE(data2, data1)) AS data2, motivo, testo1
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

	public function saveData() {
		$this->datasource->executeUpdate("REPLACE INTO rb_w_dati_richiesta (id_richiesta, data1, data2, intero1, intero2, testo1) VALUES ({$this->requestID}, '{$this->day}', '{$this->endDay}', {$this->reason['id']}, {$this->numberOfDays}, ".field_null($this->reason['note'], true).")");
	}

	public function delete() {
		$this->datasource->executeUpdate("UPDATE rb_w_richieste SET stato = 5 WHERE id_richiesta = {$this->requestID}");
	}

}