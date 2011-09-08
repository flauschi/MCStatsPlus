<?php
final class DatabaseStandalone extends Database {
	protected function dbDriverConnect() {
		if (MCSP_DB_TYPE == 'mysql') {
			return new PDO('mysql:dbname=' . MCSP_DB_NAME . ';host=' . MCSP_DB_HOST, MCSP_DB_USER, MCSP_DB_PASSWORD);
		} else {
			return new PDO('sqlite:mcstatsplus.sq3');
		}
	}
	
	protected function dbDriverSetup() {
		$this->dbDriverSetCharset(MCSP_DB_CHARSET);
	}

	protected function dbDriverSetCharset($charset = 'utf8') {
		$sql = "SET charset " . $charset; 
		$this->dbQuery($sql);
	}

	protected function dbDriverQuery($query) {
		return $this->link->query($query);
	}
	
	protected function dbDriverResult($query) {
		$stm = $this->link->prepare($query);

		$result = $stm->execute();
		
		$return = array();
		
		while ($obj = $stm->fetchObject()) {
			$return[] = $obj;
		}
		
		return $return;
	}	
}

?>