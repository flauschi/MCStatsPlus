<?php
class PluginStats extends Plugin {
	public function register() {
		return array(
			'user_stats' => 'getUserStats'
		);
	}
	
	public function getUserStats($params) {
		$data = $this->c->load('userstats');
		
		$stats = array();
		
		if(isset($data->player)) {
			$dataUser = $data->player[0];
		
			foreach($dataUser as $dataCat) {
				$catName = (string)$dataCat['name'];
				$stats[$catName] = array();

				// TODO: translate to name to id			
				foreach($dataCat as $dataStat) {
					$stats[$catName][(string)$xmlStat['name']] = (integer)$xmlStat['value'];
				}
			}
		}
		
		return $stats;
	}
}

?>