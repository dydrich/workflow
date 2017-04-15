<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/16/17
 * Time: 10:06 AM
 */

namespace eschool;


class Workflow
{
	private $wflowID;
	private $requestName;
	private $steps = [];
	private $groups = [];
	private $datasource;
	private $needProtocol;

	/**
	 * Workflow constructor.
	 * @param $wflofID
	 * @param $requestName
	 * @param array $steps
	 * @param array $groups
	 * @param \MySQLDataLoader $datasource
	 */
	public function __construct($wflofID, $requestName, $steps, $groups, \MySQLDataLoader $datasource) {
		$this->wflowID = $wflofID;
		$this->datasource = $datasource;
		$this->needProtocol = true;
		if (is_null($requestName) && is_null($steps) && is_null($groups) && $wflofID != 0) {
			$this->loadValues($wflofID);
		}
		else {
			$this->requestName = $requestName;
			$this->groups = $groups;
			$this->steps = $steps;
		}
		$this->loadSteps();
	}

	/**
	 * @return mixed
	 */
	public function getNeedProtocol() {
		return $this->needProtocol;
	}

	/**
	 * @param mixed $needProtocol
	 */
	public function setNeedProtocol($needProtocol) {
		$this->needProtocol = $needProtocol;
	}

	/**
	 * @return mixed
	 */
	public function getWflowID() {
		return $this->wflowID;
	}

	/**
	 * @param mixed $wflowID
	 */
	public function setWflowID($wflowID) {
		$this->wflowID = $wflowID;
	}

	/**
	 * @return mixed
	 */
	public function getRequestName() {
		return $this->requestName;
	}

	/**
	 * @param mixed $requestName
	 */
	public function setRequestName($requestName) {
		$this->requestName = $requestName;
	}

	/**
	 * @return array
	 */
	public function getSteps() {
		return $this->steps;
	}

	/**
	 * @param array $steps
	 */
	public function setSteps($steps) {
		$this->steps = $steps;
	}

	/**
	 * @return array
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @param array $groups
	 */
	public function setGroups($groups) {
		$this->groups = $groups;
	}

	/**
	 * @return mixed
	 */
	public function getDatasource() {
		return $this->datasource;
	}

	/**
	 * @param mixed $datasource
	 */
	public function setDatasource(\MySQLDataLoader $datasource) {
		$this->datasource = $datasource;
	}

	/**
	 * convert groups to numeric value for insert and update
	 */
	private function groupsToNumeric() {
		$gruppi = 0;
		foreach($this->groups as $a) {
			$gruppi += $a;
		}
		return $gruppi;
	}

	/**
	 * load value using id
	 */
	public function loadValues($id) {
		$this->wflowID = $id;
		$sel_wflow = "SELECT richiesta, gruppi, protocollo FROM rb_w_workflow WHERE id_workflow = {$id}";
		$res_wflow = $this->datasource->executeQuery($sel_wflow);
		$this->requestName = $res_wflow[0]['richiesta'];
		$this->needProtocol = $res_wflow[0]['protocollo'];
		$this->steps = [];
		$grps = $this->datasource->executeQuery("SELECT gid FROM rb_gruppi WHERE gid BETWEEN 2 AND 5");
		$this->groups = [];
		$val_grps = $res_wflow[0]['gruppi'];
		foreach ($grps as $grp) {
			if ($val_grps&$grp) {
				$this->groups[] = $grp;
			}
		}
	}

	/**
	 * add step
	 */
	public function addStep($step, $index) {
		$newSteps = [];
		for ($i = 1; $i < count($this->steps); $i++) {
			if ($index == $i) {
				$newSteps[$i] = $step;
			}
			else {
				$newSteps[$i] = $this->steps[$i];
			}
		}
		$this->steps = $newSteps;
		/*
		 * insert step in rb_w_step_workflow
		 */
		$stepID = $this->datasource->executeUpdate("INSERT INTO rb_w_step_workflow (step, ufficio, ordine, id_workflow, status) VALUES ({$step['step_type']}, {$step['office']}, {$step['order']}, {$this->wflowID}, {$step['status']})");
		foreach ($step['statuses'] as $status) {
			$this->datasource->executeUpdate("INSERT INTO rb_w_status_step_workflow (id_workflow, status, id_step) VALUES ({$this->wflowID}, {$status}, {$stepID})");
		}
		$this->steps[$index]['id_step'] = $stepID;
	}

	/**
	 * load steps
	 */
	private function loadSteps() {
		$st = $this->datasource->executeQuery("SELECT rb_w_step_workflow.*, nome 
											  FROM rb_w_step_workflow, rb_w_uffici 
											  WHERE id_workflow = {$this->wflowID} 
											  AND ufficio = id_ufficio
											  ORDER BY ordine");
		if ($st && count($st) > 0) {
			foreach ($st as $item) {
				$this->steps[$item['ordine']] = array('id_step' => $item['id'], 'order' => $item['ordine'], 'office' => $item['ufficio'], 'office_name' => $item['nome'], 'step_type' => $item['step'], 'status' => $item['status'], 'statuses' => []);
				$sts = $this->datasource->executeQuery("SELECT status FROM rb_w_status_step_workflow WHERE id_step = {$item['id']}");
				$this->steps[$item['ordine']]['statuses'] = $sts;
			}
		}
	}

	public function insert() {
		$gruppi = $this->groupsToNumeric();
		$this->wflowID = $this->datasource->executeUpdate("INSERT INTO rb_w_workflow (richiesta, num_step, gruppi) VALUES ('{$this->requestName}', ".count($this->steps).", $gruppi)");
	}

	public function update() {
		$gruppi = $this->groupsToNumeric();
		$this->datasource->executeUpdate("UPDATE rb_w_workflow SET richiesta = '{$this->requestName}', num_step = ".count($this->steps).", gruppi = $gruppi WHERE id_workflow = {$this->wflowID}");
	}

	public function delete() {
		$this->datasource->executeUpdate("DELETE FROM rb_w_workflow WHERE id_workflow = {$this->wflowID}");
	}

	/*
	 * get the step next to $step
	 */
	public function getNextStep($step) {
		if (isset($this->steps[$step + 1])) {
			return $this->steps[$step + 1];
		}
		else {
			return false;
		}
	}

}