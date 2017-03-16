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
	private $wflofID;
	private $requestName;
	private $steps = [];
	private $groups = [];
	private $datasource;

	/**
	 * Workflow constructor.
	 * @param $wflofID
	 * @param $requestName
	 * @param array $steps
	 * @param array $groups
	 * @param \MySQLDataLoader $datasource
	 */
	public function __construct($wflofID, $requestName, array $steps, array $groups, \MySQLDataLoader $datasource) {
		$this->wflofID = $wflofID;
		$this->requestName = $requestName;
		$this->steps = $steps;
		$this->groups = $groups;
		$this->datasource = $datasource;
	}

	/**
	 * @return mixed
	 */
	public function getWflofID() {
		return $this->wflofID;
	}

	/**
	 * @param mixed $wflofID
	 */
	public function setWflofID($wflofID) {
		$this->wflofID = $wflofID;
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

	public function insert() {
		$gruppi = $this->groupsToNumeric();
		$this->wflofID = $this->datasource->executeUpdate("INSERT INTO rb_w_workflow (richiesta, num_step, gruppi) VALUES ('{$this->requestName}', ".count($this->steps).", $gruppi)");
	}

	public function update() {
		$gruppi = $this->groupsToNumeric();
		$this->datasource->executeUpdate("UPDATE rb_w_workflow SET richiesta = '{$this->requestName}', num_step = ".count($this->steps).", gruppi = $gruppi WHERE id_workflow = {$this->wflofID}");
	}

	public function delete() {
		$this->datasource->executeUpdate("DELETE FROM rb_w_workflow WHERE id_workflow = {$this->wflofID}");
	}

}