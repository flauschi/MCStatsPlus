<?php
class PluginUsers extends Plugin {
	public function get($id = '') {
		return $this->getUsers();
	}
	
	protected function getUsers() {
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
		
		return $users;
	}
}

?>