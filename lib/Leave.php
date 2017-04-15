<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 4/5/17
 * Time: 6:02 PM
 */

namespace eschool;

require_once "DailyLeave.php";


class Leave extends Request
{
	protected $day;
	protected $enter;
	protected $exit;
	protected $numberOfHours;

	protected function loadData() {
		/*
		 * load data from database
		 */
		$d1 = $this->datasource->executeQuery("SELECT richiedente, operatore, data_ora, codice_pratica, COALESCE(stato, 0) AS stato, COALESCE(ufficio, 1) AS ufficio, intero1, DATE(data1) AS data1, TIME (data2) as enter_time, TIME (data3) as exit_time
											  FROM rb_w_richieste, rb_w_dati_richiesta
											  WHERE rb_w_dati_richiesta.id_richiesta = rb_w_richieste.id_richiesta 
											  AND rb_w_richieste.id_richiesta = {$this->requestID}");
		$this->dateTime = $d1[0]['data_ora'];
		$this->day = $d1[0]['data1'];
		$this->enter = $d1[0]['enter_time'];
		$this->exit = $d1[0]['exit_time'];
		$this->numberOfHours = $d1[0]['intero1'];
	}

	public function saveData() {
		$d2 =$this->day." ".$this->enter;
		$d3 =$this->day." ".$this->exit;
		$this->requestID = $this->datasource->executeUpdate("REPLACE INTO rb_w_dati_richiesta (id_richiesta, data1, data2, data3, intero1) VALUES ({$this->requestID}, '{$this->day}', '{$d2}', '{$d3}', {$this->numberOfHours})");
	}

	public function delete() {
		// TODO: Implement delete() method.
	}

	/**
	 * @return mixed
	 */
	public function getEnter() {
		return $this->enter;
	}

	/**
	 * @param mixed $enter
	 */
	public function setEnter($enter) {
		$this->enter = $enter;
	}

	/**
	 * @return mixed
	 */
	public function getExit() {
		return $this->exit;
	}

	/**
	 * @param mixed $exit
	 */
	public function setExit($exit) {
		$this->exit = $exit;
	}

	/**
	 * @return mixed
	 */
	public function getNumberOfHours() {
		return $this->numberOfHours;
	}

	/**
	 * @param mixed $numberOfHours
	 */
	public function setNumberOfHours($numberOfHours) {
		$this->numberOfHours = $numberOfHours;
	}
}