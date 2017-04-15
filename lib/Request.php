<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/22/17
 * Time: 7:04 PM
 */

namespace eschool;


abstract class Request
{
	protected $requestID;
	protected $requestCode;
	protected $requestType;
	protected $datasource;
	protected $status;
	protected $office;
	protected $user;
	protected $officer;
	protected $dateTime;
	protected $protocol;

	/**
	 * Request constructor.
	 * @param $requestID
	 * @param $requestCode
	 * @param $requestType
	 * @param $datasource
	 */
	public function __construct($requestID, $requestCode, $requestType, \MySQLDataLoader $datasource) {
		$this->requestID = $requestID;
		$this->requestCode = $requestCode;
		$this->requestType = $requestType;
		$this->datasource = $datasource;
		$this->loadData();
	}

	/**
	 * @return mixed
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @param mixed $protocol
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
		$this->datasource->executeUpdate("UPDATE rb_w_richieste SET protocollo = '{$this->protocol}' WHERE id_richiesta = {$this->requestID}");
	}

	/**
	 * @return mixed
	 */
	public function getRequestType() {
		return $this->requestType;
	}

	/**
	 * @param mixed $requestType
	 */
	public function setRequestType($requestType) {
		$this->requestType = $requestType;
	}

	/**
	 * @return \MySQLDataLoader
	 */
	public function getDatasource() {
		return $this->datasource;
	}

	/**
	 * @param \MySQLDataLoader $datasource
	 */
	public function setDatasource($datasource) {
		$this->datasource = $datasource;
	}

	/**
	 * @return mixed
	 */
	public function getRequestID() {
		return $this->requestID;
	}

	/**
	 * @return mixed
	 */
	public function getRequestCode() {
		return $this->requestCode;
	}

	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param mixed $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return mixed
	 */
	public function getOffice() {
		return $this->office;
	}

	/**
	 * @param mixed $office
	 */
	public function setOffice($office) {
		$this->office = $office;
	}

	/*
	 * load status and office
	 */
	protected abstract function loadData();

	/**
	 * @return mixed
	 */
	public function getOfficer() {
		return $this->officer;
	}

	/**
	 * @param mixed $officer
	 */
	public function setOfficer($officer) {
		$this->officer = $officer;
	}

	/**
	 * @return mixed
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @return mixed
	 */
	public function getDateTime() {
		return $this->dateTime;
	}

	/**
	 * @param mixed $dateTime
	 */
	public function setDateTime($dateTime) {
		$this->dateTime = $dateTime;
	}

	public abstract function delete();
}