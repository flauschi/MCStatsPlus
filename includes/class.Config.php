<?php
abstract class Config {
	protected static $instance;
	protected $reg;
	
	protected $params = array();
	protected $system = array();

	protected function __construct() {
		$this->reg = Registry::instance();
		
		$this->configSystemInit();
	}

	public function __destruct() {
	
	}

	final public function __clone() {
		trigger_error('Not allowed to clone class', E_USER_ERROR);
	}

	abstract public static function instance();
	
	abstract protected function configDriverGet($configId);
	abstract protected function configDriverNew($configId, $value);
	abstract protected function configDriverUpdate($configId, $value);
	abstract protected function configDriverRemove($configId);

	final protected function configSystemInit() {
		$this->system['date'] = time();
		$this->system['magicquotes'] = get_magic_quotes_gpc();
	}
	
	
	final public function getConfig($configId, $default = null) {
		$config = $this->configDriverGet($configId);

		if (is_null($config)) {
			if (is_null($default)) {
				trigger_error('Undefined config index: ' . $configId, E_USER_NOTICE);
			} else {
				return $default;
			}
		} else {
			return $config;
		}
	}

	final public function setConfig($configId, $value) {
		$config = $this->configDriverGet($configId);

		if (is_null($config)) {
			$this->configDriverNew($configId, $value);
		} else {
			$this->configDriverUpdate($configId, $value);
		}
	}

	final public function unsetConfig($configId, $value) {
		$this->configDriverRemove($configId);
	}


	final public function getSystem($configId) {
		if (isset($this->system[$configId])) {
			return $this->system[$configId];
		} else {
			trigger_error('Undefined config index: ' . $configId, E_USER_NOTICE);
		}
	}
	
	
	final public function getParam($paramId, $default = null, $secure = true) {
		if (isset($this->params[$paramId])) {
			return $this->testParameter($this->params[$paramId], $secure);
		}
		
		if (isset($_POST[$paramId])) {
			$return = $_POST[$paramId];
			$this->params[$paramId] = $return;			// cache
			return $this->testParameter($return, $secure, $paramId);
		}
		
		if (isset($_GET[$paramId])) {
			$return = $_GET[$paramId];
			$this->params[$paramId] = $return;			// cache
			return $this->testParameter($return, $secure);
		}
		
		if (isset($_FILES[$paramId])) {
			$return = $_FILES[$paramId];
			$this->params[$paramId] = $return;			// cache
			return $return;
		}
		
		if (is_null($default)) {
			trigger_error('Undefined parameter index: ' . $paramId, E_USER_NOTICE);
		}
		
		return $default;
	}

	private function testParameter($parameter, $secure = true, $path = '') {
		if (is_array($parameter)) {
			if (! $secure) {
				return $parameter;
			}

			foreach ($parameter as $key => $data) {
				$parameter[$key] = $this->testParameter($data, $secure, $path . '[' . $key . ']');
			}
			
			return $parameter;
		}
		
		if ($secure) {
			if ($this->system['magicquotes']) {
				return htmlentities(stripslashes($parameter), ENT_QUOTES, 'UTF-8');
			}
			
			return htmlentities($parameter, ENT_QUOTES, 'UTF-8');
		}
		
		if ($this->system['magicquotes']) {
			return stripslashes($parameter);
		}
		
		return $parameter;
	}
}

?>