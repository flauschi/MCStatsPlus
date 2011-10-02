<?php
class MCVersions {
	protected $reg;
	
	protected $init;
	protected $versions;

	public function __construct() {
		$this->reg = Registry::instance();
		
		$this->init = false;
	}
	
	public function __destruct() {
	
	}
	
	protected function initVersions() {
		if (! $this->init) {
			$this->versions = array();
	
			$sql = "SELECT * FROM `" . $this->reg->dbPrefix . "MCSPMCVersions` ORDER BY `date` DESC;";
			$results = $this->reg->db->dbResult($sql);
	
			foreach($results as $result) {
				$this->versions[] = array(
					'versionId' => (string)$result->versionId,
					'date' => (integer)$result->date,
	
					'newTextureMapping' => ((integer)$result->newTextureMapping == 1) ? true: false
				);
			}
			
			$this->init = true;
		}
	}
	
	public function getLastTextureMappingVersion($sinceVersion = 'latest') {
		$this->initVersions();
	
		$found = ($sinceVersion == 'latest') ? true: false;
		foreach ($this->versions as $version) {
			if ((! $found) && ($version['versionId'] == $sinceVersion)) {
				$found = true;
			}
			
			if ($found && $version['newTextureMapping']) {
				return $version['versionId'];
			}
		}
		
		return false;
	}
}

?>