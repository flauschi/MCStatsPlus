<?php
final class PluginController {
	protected static $instance;
	protected $reg;
	
	protected $methods = array();

	protected function __construct() {
		$this->reg = Registry::instance();
		
		$this->init();
	}

	public function __destruct() {

	}

	public function __clone() {
		trigger_error('Not allowed to clone class', E_USER_ERROR);
	}
	
	public static function instance() {
		if (! isset(self::$instance)) {
			self::$instance = new PluginController;
		}

		return self::$instance;
	}
	
	protected function init() {
		$configPlugins = $this->reg->config->getConfig('MCSPActivePlugins');
		$plugins = preg_split("/[,]+/", $configPlugins);
		
		foreach ($plugins as $plugin) {
			$filename = 'plugins/plugin.' . $plugin . '.php';
			$pluginname = 'Plugin' . $plugin;
			
			if (file_exists($filename) === true) {
				require_once($filename);
				$p = new $pluginname();
				
				if (is_subclass_of($p, 'Plugin')) {					
					$this->registerMethods($p);
				} else {
					unset($p);
				}
				
			}
		}
	
		//$dirPlugins = new DirectoryIterator('plugins/');
		//foreach ($dirPlugins as $fileinfo) {
		//	if ($fileinfo->isFile() && substr($fileinfo->getFilename(), 0, 7) == 'plugin.') {
		//		
		//	}
		//}
	}
	
	protected function registerMethods($plugin, $methods = array()) {
		$methods = $plugin->register();
	
		foreach ($methods as $methodId => $value) {
			$this->methods[$methodId] = array('plugin' => $plugin, 'method' => $value);
		}
	}
	
	public function get($methodId, $params = array()) {
		if (! isset($this->methods[$methodId])) {
			trigger_error('Undefined pluginmethod: ' . $methodId, E_USER_NOTICE);
		} else {
			$plugin = $this->methods[$methodId]['plugin'];
			$method = $this->methods[$methodId]['method'];
			
			$return = call_user_func(array($plugin, $method), $params);
			
			return $return;
		}
	}

	public function __isset($methodId) {
		return isset($this->methods[$methodId]);
	}
}

?>