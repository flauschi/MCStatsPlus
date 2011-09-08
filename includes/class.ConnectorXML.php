<?php
final class ConnectorXML extends Connector {
	protected $type = 'xml';

	protected $curl;

	public function __construct() {
		parent::__construct();
		
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_ENCODING , "gzip");
	}

	public function __destruct() {
		curl_close($this->curl);
	
		parent::__destruct();
	}


	protected function loadDriver($request) {
		curl_setopt($this->curl, CURLOPT_URL, $request);
		$data = curl_exec($this->curl);
		
		return simplexml_load_string($data);
	}
}

?>