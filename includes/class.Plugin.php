<?php
abstract class Plugin {
	protected $reg;
	protected $c;

	public function __construct() {
		$this->reg = Registry::instance();
		
		$this->c = $this->reg->connector;
	}

	public function __destruct() {
	
	}

	abstract public function get($id = '');
}

?>