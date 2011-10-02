<?php
class MCTextures {
	protected $reg;

	public function __construct() {
		$this->reg = Registry::instance();
		
		if (! isset($this->reg->mcVersions)) {
			$this->reg->mcVersions = new MCVersions;
		}
	}
	
	public function __destruct() {
	
	}
	
	public function deleteTexturePack($name) {
		$sql = "DELETE FROM `" . $this->reg->dbPrefix . "MCSPTexturePacks` WHERE `name` = '" . $name . "';";
		$results = $this->reg->db->dbQuery($sql);
		
		rmdir('textures/' . $name);
	}
	
	public function importTexturePack($path, $name, $version = 'latest') {
		$this->deleteTexturePack($name);
	
		mkdir('textures/' . $name);
		$this->importTexturePackFile($path . 'terrain.png', $name, 'terrain', $version);
		$this->importTexturePackFile($path . 'gui/items.png', $name, 'items', $version);
	}
	
	public function exportTexturePack($name, $version = 'latest', $size = 16) {
		$this->exportTexturePackFile($name, 'terrain', $version, $size);
		$this->exportTexturePackFile($name, 'items', $version, $size);
	}	
	
	protected function importTexturePackFile($path, $name, $type = 'terrain', $version = 'latest') {
		$map = $this->getMapping($type, $version);
		
		$maxX = count($map[0]);
		$maxY = count($map);
		
		$im = imagecreatefrompng($path);
		imagealphablending($im, true);
		
		$width = imagesx($im) / $maxX;
		$height = imagesy($im) / $maxY;
		
		for ($y = 0; $y < $maxY; $y++) {
			for ($x = 0; $x < $maxX; $x++) {
				if ($map[$y][$x] != 'na') {
					$imTex = imagecreatetruecolor($width, $height);
				
					imagealphablending($imTex, false);
					$colorAlphaAlloc = imagecolorallocatealpha($imTex, 0, 0, 0, 127);
					imagefill($imTex, 0, 0, $colorAlphaAlloc);
					imagesavealpha($imTex, true);
					imagealphablending($imTex, true);

					imagecopy($imTex, $im, 0, 0, $x * $width, $y * $height, $width, $height);
					
					imagepng($imTex, 'textures/' . $name . '/' . $type . '_' . $map[$y][$x] . '.png');
					imagedestroy($imTex);
				
echo "<img src='" . 'textures/' . $name . '/' . $type . '_' . $map[$y][$x] . '.png' . "' />";

					$sql = "INSERT INTO `" . $this->reg->dbPrefix . "MCSPTexturePacks` (`name`, `mappingType`, `mappingId`) VALUES ('" . $name . "', '" . $type . "', '" . $map[$y][$x] . "');";
					$results = $this->reg->db->dbQuery($sql);
				}
			}
		}
	}

	protected function exportTexturePackFile($name, $type = 'terrain', $version = 'latest', $size = 16) {
		$map = $this->getMapping($type, $version);
		
		$maxX = count($map[0]);
		$maxY = count($map);
		
		$im = imagecreatetruecolor($size * $maxX, $size * $maxY);
		
		imagealphablending($im, false);
		$colorAlphaAlloc = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagefill($im, 0, 0, $colorAlphaAlloc);
		imagesavealpha($im, true);
		imagealphablending($im, true);
		
		$mapPath = array();
		
		$sql = "SELECT * FROM `" . $this->reg->dbPrefix . "MCSPTexturePacks` WHERE `name` = '" . $name . "' AND `mappingType` = '" . $type . "';";
		$results = $this->reg->db->dbResult($sql);
		
		foreach($results as $result) {
			if (is_null($result->destType)) {
				$mapPath[(string)$result->mappingId] = 'textures/' . $name . '/' . $type . '_' . (string)$result->mappingId . '.png';
			} else {
				$mapPath[(string)$result->mappingId] = 'textures/' . (string)$result->destName . '/' . (string)$result->destType . '_' . (string)$result->destId . '.png';
			}
			
		}
		
		for ($y = 0; $y < $maxY; $y++) {
			for ($x = 0; $x < $maxX; $x++) {
				if ($map[$y][$x] != 'na') {
					if (isset($mapPath[$map[$y][$x]])) {
						$imTex = imagecreatefrompng($mapPath[$map[$y][$x]]);
						imagealphablending($imTex, true);
					
						$width = imagesx($imTex);
						$height = imagesy($imTex);
										
						imagecopyresampled($im, $imTex, $x * $size, $y * $size, 0, 0, $size, $size, $width, $height);
					
						imagedestroy($imTex);
					} else {
						echo "Missing mapping: " . $type . ":" . $tm[$y][$x] . "<br />";
					}
				}
			}
		}
		
		imagepng($im, 'textures/' . $name . '/' . $type . '_' . $size . '.png');
echo "<img src='" . 'textures/' . $name . '/' . $type . '_' . $size . '.png' . "' />";
	}

	protected function getMapping($type = 'terrain', $version = 'latest') {
		$version = $this->reg->mcVersions->getLastTextureMappingVersion($version);
	
		$map = array();

		for ($y= 0; $y < 16; $y++) {
			$map[$y] = array();
			
			for ($x = 0; $x < 16; $x++) {
				$map[$y][$x] = 'na';
			}
		}

		$sql = "SELECT * FROM `" . $this->reg->dbPrefix . "MCSPMCMapping_" . $type . "` WHERE `version` = '" . $version . "';";
		$results = $this->reg->db->dbResult($sql);

		foreach($results as $result) {
			$map[(integer)$result->posY][(integer)$result->posX] = (string)$result->mapping;
		}
		
		return $map;
	}
}
?>