<?php
final class Registry {
	protected static $instance;
	protected $vars = array();
	
	protected function __construct() {

	}

	public function __destruct() {

	}

	public function __clone() {
		trigger_error('Not allowed to clone class', E_USER_ERROR);
	}
	
	public static function instance() {
		if (! isset(self::$instance)) {
			self::$instance = new Registry;
		}

		return self::$instance;
	}

	public function __set($registryId, $value) {
		$this->vars[$registryId] = $value;
	}

	public function __get($registryId) {
		if (! isset($this->vars[$registryId])) {
			trigger_error('Undefined registry index: ' . $registryId, E_USER_NOTICE);
		} else {
			return $this->vars[$registryId];
		}
	}

	public function __isset($registryId) {
		return isset($this->vars[$registryId]);
	}
}

?>