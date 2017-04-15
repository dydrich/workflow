<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/23/17
 * Time: 9:23 AM
 */

namespace eschool;

require_once "Request.php";
require_once "Workflow.php";

abstract class AdimistrativeRequest extends Request
{
	protected $workflow;
	protected $steps;

	/**
	 * @inheritDoc
	 */
	public function __construct($requestID, $requestCode, $requestType, \MySQLDataLoader $datasource) {
		parent::__construct($requestID, $requestCode, $requestType, $datasource);
		$this->steps = [];
		$this->loadData();
		$this->loadSteps();
	}

	private function loadSteps() {
		$stps = $this->datasource->executeQuery("SELECT * FROM rb_w_step_richieste WHERE id_richiesta = {$this->requestID} ORDER BY id DESC");
		if ($stps) {
			$this->steps = $stps;
		}
	}

	/**
	 * @return mixed
	 */
	public function getWorkflow() {
		return $this->workflow;
	}

	/**
	 * @param mixed $workflow
	 */
	public function setWorkflow(Workflow $workflow) {
		$this->workflow = $workflow;
	}

	public function getCurrentStep() {
		if (count($this->steps) > 0) {
			return array_shift($this->steps);
		}
		else {
			return false;
		}
	}

	public function getAdvanceText($index) {
		$step = $this->workflow->getNextStep($index);
		$text = "";
		if (!$step) {
			return "Pratica chiusa";
		}
		else {
			switch ($step['step_type']) {
				case "6":
					$text = "Segna come ricevuta";
					break;
				case "3":
					$text = "Prendi in carico la pratica";
					break;
				case "4":
					$text = "Invia pratica a ";
					$ns = $this->workflow->getNextStep($index+1);
					$text .= $ns['office_name'];
					break;
				case "7":
					$text = "Chiudi la pratica";
					break;
				case "9":
					$text = "Protocolla la pratica";
					break;
			}
			return $text;
		}
	}

	public function advanceRequest() {
		/*
		 * first: insert step in rb_w_step_richieste
		 */
		if (count($this->steps) == 0) {
			$index = 1;
		}
		else {
			$st = $this->getCurrentStep();
			$index = $st['step'] + 1;
		}

		$this->datasource->executeUpdate("INSERT INTO rb_w_step_richieste (id_richiesta, step, data_inoltro, note, id_operatore) 
								VALUES ({$this->requestID}, {$index}, NOW(), '', {$_SESSION['__user__']->getUid()})");
		$step_data = $this->datasource->executeQuery("SELECT ufficio, rb_w_status_step_workflow.status FROM rb_w_step_workflow, rb_w_status_step_workflow 
													WHERE rb_w_step_workflow.id_workflow = {$this->requestType} 
													AND rb_w_step_workflow.id = rb_w_status_step_workflow.id_step 
													AND ordine = {$index}");
		$this->datasource->executeUpdate("UPDATE rb_w_richieste 
										SET operatore = {$_SESSION['__user__']->getUid()}, 
										stato = {$step_data[0]['status']}, 
										ufficio = {$step_data[0]['ufficio']} 
										WHERE id_richiesta = {$this->requestID}");
	}

	public  function closeRequest($status) {
		$st = $this->getCurrentStep();
		$index = $st['step'] + 1;
		$this->datasource->executeUpdate("INSERT INTO rb_w_step_richieste (id_richiesta, step, data_inoltro, note, id_operatore) 
								VALUES ({$this->requestID}, {$index}, NOW(), '', {$_SESSION['__user__']->getUid()})");
		$step_data = $this->datasource->executeQuery("SELECT ufficio, rb_w_status_step_workflow.status FROM rb_w_step_workflow, rb_w_status_step_workflow 
													WHERE rb_w_step_workflow.id_workflow = {$this->requestType} 
													AND rb_w_step_workflow.id = rb_w_status_step_workflow.id_step 
													AND ordine = {$index}");
		$this->datasource->executeUpdate("UPDATE rb_w_richieste 
										SET operatore = {$_SESSION['__user__']->getUid()}, 
										stato = {$status}, 
										ufficio = {$step_data[0]['ufficio']} 
										WHERE id_richiesta = {$this->requestID}");
	}
}