<?php
class PluginUsers extends Plugin {
	protected $users;

	public function register() {
		return array(
			'user_list_all' => 'getUserListAll'
		);
	}
	
	public function getUserListAll() {
		if (isset($this->users)) {
			return $this->users;
		}
	
		$data = $this->c->load('users');
		
		$users = array();
		
		if (isset($data->player)) {
			foreach($data->player as $dataUser) {
				$users[] = array(
					'name' => (string)$dataUser['name'],
					'status' => (string)$dataUser['status']
				);
			}
	
			usort($users, create_function('$a,$b', 'return strcasecmp($a["name"],$b["name"]);'));
		}
		
		$this->users = $users;
		
		return $users;
	}
}

?>