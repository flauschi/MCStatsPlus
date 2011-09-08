<?php
abstract class Database {
	protected $link;

	final public function __construct() {
		$this->dbConnect();
	}

	public function __destruct() {
	
	}

	abstract protected function dbDriverConnect();
	abstract protected function dbDriverSetup();
	
	public function dbConnect() {
		try {
			$this->link = $this->dbDriverConnect();
		} catch (exception $e) {	
			trigger_error('Connection failed: ' . $e->getMessage(), E_USER_ERROR);
		}
		
		$this->dbDriverSetup();
	}
	
	
	abstract protected function dbDriverQuery($query);
	
	public function dbQuery($query) {
		return $this->dbDriverQuery($query);
	}


	abstract protected function dbDriverResult($query);
	
	public function dbResult($query) {
		$return = $this->dbDriverResult($query);
		
		return $return;
	}	

	
	public function dbEscape($data) {
	
	}
}

?>