<?php
final class MCStatsPlus {
	private static $instance;
	private $reg;

	public function __construct() {
		$this->reg = Registry::instance();
		
		$this->reg->app = $this;
		
		$this->reg->dbPrefix = '';
		$this->reg->db = new DatabaseStandalone;
		$this->reg->config = ConfigStandalone::instance();
		
		$this->setupInit();
		$this->setupSystem();
		
		if ($this->reg->config->getConfig('MCSPConnectorType') == 'XML') {
			$this->reg->connector = new ConnectorXML;
		}
		
		$this->reg->plugin = PluginController::instance();
	}
		
	public function __destruct() {
		unset($this->reg->db);
	}
	
	public function __clone() {
		trigger_error('Not allowed to clone class', E_USER_ERROR);
	}

	public static function instance() {
		if (! isset(self::$instance)) {
			self::$instance = new MCStatsPlus;
		}

		return self::$instance;
	}
	
	protected function setupInit() {
		$setupConfig = $this->reg->config->getConfig('MCSPSetup', 'false');
		
		if ($setupConfig != 'true') {
			$this->reg->config->setConfig('MCSPConnectorHost', 'localhost');
			$this->reg->config->setConfig('MCSPConnectorPort', '9123');
			$this->reg->config->setConfig('MCSPConnectorType', 'XML');
			
			$this->reg->config->setConfig('MCSPActivePlugins', 'Users,Economy,Stats,Achievements');
			
			$this->reg->config->setConfig('MCSPTimezone', '');
			$this->reg->config->setConfig('MCSPLocale', '');
			
			$this->reg->config->setConfig('MCSPDebug', 'false');
			
			$this->reg->config->setConfig('MCSPSetup', 'true');
		}
	}
	
	protected function setupSystem() {
		$timezone = $this->reg->config->getConfig('MCSPTimezone');
		if (! empty($timezone)) {
			date_default_timezone_set($timezone);
		}
		
		$locale = $this->reg->config->getConfig('MCSPLocale');
		if (! empty($locale)) {
			setlocale(LC_ALL, $locale);
		}
		
		if ($this->reg->config->getConfig('MCSPLocale') == 'true') {
			error_reporting (E_ALL);
		}
	} 
	
	public function main() {
		//echo $_SERVER['REQUEST_URI'];
		//$users = $this->reg->plugin->get('user_list_all');
		//var_dump($users);
		
		$t = new MCTextures;
		//$t->importTexturePack('temp/', 'honey');
		$t->exportTexturePack('honey', 'latest');
	}
}

?>