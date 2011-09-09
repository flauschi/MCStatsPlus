<?php
class PluginAchievements extends Plugin {
	public function register() {
		return array(
			'achievement_list_all' => 'getAchievementListAll'
		);
	}
	
	function getAchievementListAll() {
		$data = $this->c->load('achievements');
		
		$achievements = array();
		
		if (isset($data->achievement)) {
			foreach($data->achievement as $dataAchievement) {
				$achievements[] = array(
					'name' => (string)$dataAchievement->name,
					'enabled' => ((string)$dataAchievement['enabled'] == "true") ? true: false,
					'description' => (string)$dataAchievement->description,
					'category' => (string)$dataAchievement->category,
					'stat' => (string)$dataAchievement->stat,
					'value' => (integer)$dataAchievement->value,
					'maxawards' => (integer)$dataAchievement->maxawards,
					'commands' => (integer)$dataAchievement->commands
				);
			}
	
			usort($achievements, create_function('$a,$b', 'return strcasecmp($a["name"],$b["name"]);'));
		}
		
		return $achievements;
	}
}

?>