<?php
final class MCStatsPlus {
	private static $instance;
	private $reg;

	public function __construct() {
		$this->reg = Registry::instance();
		
		$this->reg->app = $this;
		
		$this->init();
	}
		
	public function __destruct() {
		unset($this->reg->db);
	}
	
	public function __clone() {
		trigger_error('Not allowed to clone class', E_USER_ERROR);
	}

	public static function instance() {
		if ( ! isset(self::$instance) ) {
			self::$instance = new MCStatsPlus;
		}

		return self::$instance;
	}
	
	protected function setup() {
		$setupConfig = $this->reg->config->getConfig('MCSPSetup', 'false');
		
		if ($setupConfig != 'true') {
			$this->reg->config->setConfig('MCSPConnectorHost', 'localhost');
			$this->reg->config->setConfig('MCSPConnectorPort', '9123');
			$this->reg->config->setConfig('MCSPConnectorType', 'XML');
			
			$this->reg->config->setConfig('MCSPSetup', 'true');
		}
	}
	
	protected function init() {
		$this->reg->dbPrefix = '';
		$this->reg->db = new DatabaseStandalone;
		$this->reg->config = ConfigStandalone::instance();
		
		$this->setup();
		
		date_default_timezone_set('Europe/Berlin');
		setlocale(LC_ALL, "de_DE");
		
		if (MCSP_DEBUG) {
			error_reporting (E_ALL);
		}
		
		
		if ($this->reg->config->getConfig('MCSPConnectorType') == 'XML') {
			$this->reg->connector = new ConnectorXML;
		}
	}

	public function main() {
		echo "Hallo Welt";
		
		$plugin = new PluginUsers;
		$users = $plugin->get('users');
		var_dump($users);
	}
}

?>