<?php
class PluginEconomy extends Plugin {
	public function register() {
		return array(
			'user_balance_all' => 'getUserBalanceAll'
		);
	}
	
	public function getUserBalanceAll() {
		$data = $this->c->load('money');
		
		$balances = array();
		
		if (isset($data->player)) {
			foreach($data->player as $dataUser) {
				$balances[(string)$dataUser['name']] = (float)$dataUser['balance'];
			}
		}
		
		return $balances;
	}
}

?>