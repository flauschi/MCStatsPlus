<?php
final class ConfigStandalone extends Config {
	protected $config = array();

	protected function __construct() {
		parent::__construct();
	
		$this->configDriverInit();
	}


	public static function instance() {
		if (! isset(self::$instance)) {
			self::$instance = new ConfigStandalone;
		}

		return self::$instance;
	}

	protected function configDriverInit() {
		$sql = "SELECT `configId`, `value` FROM `" . $this->reg->dbPrefix . "MCSPConfig`;";
		$results = $this->reg->db->dbResult($sql);

		foreach( $results as $result ) {
			$this->config[$result->configId] = (string)$result->value;
		}
	}
	
	protected function configDriverGet($configId) {
		if ( isset($this->config[$configId]) ) {
			return $this->config[$configId];
		} else {
			return null;
		}
	}
	
	protected function configDriverNew($configId, $value) {
		$sql = "INSERT INTO `" . $this->reg->dbPrefix . "MCSPConfig` "
		     . "(`configId`, `value`) "
			 . "VALUES ('" . $configId . "', '" . $value . "');";

		$result = $this->reg->db->dbQuery($sql);
		
		$this->config[$configId] = $value;
	}

	protected function configDriverUpdate($configId, $value) {
		$sql = "UPDATE `" . $this->reg->dbPrefix . "MCSPConfig` "
		     . "SET `value` = '" . $value . "' "
			 . "WHERE `configId` = '" . $configId . "';";

		$result = $this->reg->db->dbQuery($sql);
		
		$this->config[$configId] = $value;
	}
		
	protected function configDriverRemove($configId) {
		$sql = "DELETE FROM `" . $this->reg->dbPrefix . "MCSPConfig` "
			 . "WHERE `configId` = '" . $configId . "';";

		$result = $this->reg->db->dbQuery($sql);
		
		unset($this->config[$configId]);
	}
}

?>