<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 4/13/17
 * Time: 10:00 AM
 */

namespace eschool;


class LeaveAdministrativeRequest extends AdimistrativeRequest
{
	protected $enter;
	protected $exit;
	protected $numberOfHours;

	protected function loadData() {
		/*
		 * load data from database
		 */
		$basic_data = $this->datasource->executeQuery("SELECT * FROM rb_w_richieste WHERE id_richiesta = {$this->requestID}");
		$this->requestCode = $basic_data[0]['codice_pratica'];
		$this->requestType = $basic_data[0]['workflow'];
		$this->workflow = new Workflow($this->requestType, null, null, null, $this->datasource);
		$d1 = $this->datasource->executeQuery("SELECT richiedente, operatore, data_ora, codice_pratica, COALESCE(stato, 0) AS stato, COALESCE(ufficio, 1) AS ufficio, intero1, intero2, DATE(data1) AS data1, TIME (data2) as enter_time, TIME (data3) as exit_time
											  FROM rb_w_richieste, rb_w_dati_richiesta, rb_w_motivi_permesso
											  WHERE rb_w_dati_richiesta.id_richiesta = rb_w_richieste.id_richiesta 
											  AND id = intero1 
											  AND rb_w_richieste.id_richiesta = {$this->requestID}");
		$this->day = $d1[0]['data1'];
		$this->enter = $d1[0]['enter_time'];
		$this->exit = $d1[0]['exit_time'];
		$this->numberOfHours = $d1[0]['intero2'];
	}

	public function delete() {
		// TODO: Implement delete() method.
	}

}