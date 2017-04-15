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
	protected $reason;

	protected function loadData() {
		/*
		 * load data from database
		 */
		$d1 = $this->datasource->executeQuery("SELECT richiedente, operatore, data_ora, codice_pratica, COALESCE(stato, 0) AS stato, COALESCE(ufficio, 1) AS ufficio, intero1, DATE(data1) AS data1, motivo
											  FROM rb_w_richieste, rb_w_dati_richiesta, rb_w_motivi_permesso
											  WHERE rb_w_dati_richiesta.id_richiesta = rb_w_richieste.id_richiesta 
											  AND id = intero1 
											  AND rb_w_richieste.id_richiesta = {$this->requestID}");
		$this->dateTime = $d1[0]['data_ora'];
		$this->day = $d1[0]['data1'];
		$this->reason = ['id' => $d1[0]['intero1'], 'desc' => $d1[0]['motivo']];
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
		$this->datasource->executeUpdate("REPLACE INTO rb_w_dati_richiesta (id_richiesta, data1, intero1) VALUES ({$this->requestID}, '{$this->day}', {$this->reason})");
	}

	public function delete() {
		$this->datasource->executeUpdate("UPDATE rb_w_richieste SET stato = 5 WHERE id_richiesta = {$this->requestID}");
	}

}