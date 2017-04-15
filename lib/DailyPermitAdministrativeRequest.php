<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/23/17
 * Time: 9:27 AM
 */

namespace eschool;

require_once "AdimistrativeRequest.php";

class DailyPermitAdministrativeRequest extends AdimistrativeRequest
{

	protected function loadData() {
		/*
		 * load data from database
		 */
		$basic_data = $this->datasource->executeQuery("SELECT * FROM rb_w_richieste WHERE id_richiesta = {$this->requestID}");
		$this->requestCode = $basic_data[0]['codice_pratica'];
		$this->requestType = $basic_data[0]['workflow'];
		$this->workflow = new Workflow($this->requestType, null, null, null, $this->datasource);

		$d1 = $this->datasource->executeQuery("SELECT richiedente, operatore, data_ora, codice_pratica, COALESCE(stato, 0), COALESCE(ufficio, 0), intero1, DATE(data1) AS data1, motivo
											  FROM rb_w_richieste, rb_w_dati_richiesta, rb_w_motivi_permesso
											  WHERE rb_w_dati_richiesta.id_richiesta = rb_w_richieste.id_richiesta 
											  AND id = intero1 
											  AND rb_w_richieste.id_richiesta = {$this->requestID}");
		$this->dateTime = $d1[0]['data_ora'];
		$this->day = $d1[0]['data1'];
		$this->reason = ['id' => $d1[0]['intero1'], 'desc' => $d1[0]['motivo']];
	}

	public function delete() {
		// TODO: Implement delete() method.
	}
}