<?php
class PluginUsers extends Plugin {
	protected $users;
	protected $info;


	public function register() {
		return array(
			'user_list_all' => 'getUserListAll'
		);
	}
	
	public function getUserListAll() {
		if (isset($this->users)) {
			return $this->users;
		}
	
		$data = $this->c->load('user_list');
		
		$users = array();
		
		if (isset($data->users)) {
			foreach($data->user as $dataUser) {
				$users[] = $this->parseUser($dataUser);
			}
	
			usort($users, create_function('$a,$b', 'return strcasecmp($a["name"],$b["name"]);'));
		}
		
		$this->users = $users;
		
		return $users;
	}
	
	public function parseUser($data) {
		$user = array();
		
		if (! isset($data->name)) {
			// Exception
		}
		
		$user['name'] = (string)$data->name;
		
		$user['status'] = (isset($data['status'])) ? (string)$data['status']: 'unknown';
		
		return $user;
	}
}

?>