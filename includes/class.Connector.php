<?php
abstract class Connector {
	protected $reg;
	
	protected $type = 'undef';
	protected $host;
	protected $port;
	
	public function __construct() {
		$this->reg = Registry::instance();
		
		$this->host = $this->reg->config->getConfig('MCSPConnectorHost');
		$this->port = $this->reg->config->getConfig('MCSPConnectorPort');
	}

	public function __destruct() {
	
	}
	
	abstract protected function loadDriver($request);
	
	public function load($action, $params = array()) {
		$url = $this->host . ':' . $this->port . '/' . $action . '.' . $this->type;
		
		if (count($params) > 0) {
			$url .= '?';
			
			foreach ($params as $key => $value) {
				$url .= $key . '=' . $value . '&';
			}
			
			$url = rtrim($url, '&');
		}
		
		$return = $this->loadDriver($url);
		
		return $return;
	}
}

?>