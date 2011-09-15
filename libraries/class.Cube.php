<?php
function sortGlobal($a, $b) {
	if ($a[0] == $b[0]) {
		// should never happen -> same point
		if ($a[2] == $b[2]) {
			return 0;
		}
		
		return ($a[2] < $b[2]) ? -1: 1;
	}
	
	return ($a[0] < $b[0]) ? -1: 1;
}

function sortActive($a, $b) {
	if ($a[2] == $b[2]) {
		return 0;
	}
	
	return ($a[2] < $b[2]) ? -1: 1;
}

function imagepolygontexture(&$im, $points, $color) {
	// initialize all edges
	$globalEdges = array();
	$n = count($points);
	
	$pointA = $points[0];
	
	for ($i = 1; $i <= $n; $i++) {
		$pointB = $points[$i % $n];
		
		$xA = floor($pointA[0]); $yA = floor($pointA[1]);
		$xB = floor($pointB[0]); $yB = floor($pointB[1]);
		
		if ($yA != $yB) {
			$slopeInv = ($xB - $xA) / ($yB - $yA);
			
			if ($yA < $yB ) {
				$yMin = $yA;
				$yMax = $yB;
				$xVal = $xA;
			} else {
				$yMin = $yB;
				$yMax = $yA;
				$xVal = $xB;
			}
			
			$globalEdges[] = array($yMin, $yMax, $xVal, $slopeInv);
		}
		
		$pointA = $pointB;
	}
	
	usort($globalEdges, 'sortGlobal');

	// initialize scanline
	$scanline = $globalEdges[0][0];
	
	// initialize active edge table
	$activeEdges = array();
	
	foreach ($globalEdges as $edge) {
		if ($edge[0] == $scanline) {
			$activeEdges[] = $edge;
		}
		if ($edge[0] > $scanline) {
			break;
		}
	}

	// fill polygon
	while (count($activeEdges) > 0) {
		// draw
		$n = count($activeEdges);

		for ($i = 0; $i < $n; $i = $i + 1) {
			$xA = round($activeEdges[$i][2]);
			$xB = round($activeEdges[(($i + 1) % $n)][2]);
			
			for ($xNow = $xA; $xNow <= $xB; $xNow++) {
				imagesetpixel($im, $xNow, $scanline, $color);
			}
		}
		
		// remove edges and update x
		$activeEdgesNew = array();
		
		foreach ($activeEdges as $edge) {
			if ($edge[1] != $scanline) {
				$edge[2] += $edge[3];
				$activeEdgesNew[] = $edge;
			}
		}
		
		$activeEdges = $activeEdgesNew;
		
		// increase scanline
		$scanline++;
		
		// reorder global and active edge table
		$globalEdgesNew = array();
		
		foreach ($globalEdges as $edge) {
			if ($edge[0] == $scanline) {
				$activeEdges[] = $edge;
			} else {
				$globalEdgesNew[] = $edge;
			}
		}
		
		$globalEdges = $globalEdgesNew;
		
		// reorder according x value
		usort($activeEdges, 'sortActive');
	}
}

function imagepolygonlineaa(&$im, $points, $color) {
	// initialize all edges
	$globalEdges = array();
	$n = count($points);
	
	$pointA = $points[0];
	
	for ($i = 1; $i <= $n; $i++) {
		$pointB = $points[$i % $n];
		
		$xA = floor($pointA[0]); $yA = floor($pointA[1]);
		$xB = floor($pointB[0]); $yB = floor($pointB[1]);
		
		if ($yA != $yB) {
			$slopeInv = ($xB - $xA) / ($yB - $yA);
			
			if ($yA < $yB) {
				$yMin = $yA;
				$yMax = $yB;
				$xVal = $xA;
			} else {
				$yMin = $yB;
				$yMax = $yA;
				$xVal = $xB;
			}
			
			$globalEdges[] = array($yMin, $yMax, $xVal, $slopeInv);
		}
		
		$pointA = $pointB;
	}
	
	usort($globalEdges, 'sortGlobal');

	// initialize scanline
	$scanline = $globalEdges[0][0];
	
	// initialize active edge table
	$activeEdges = array();
	
	foreach ($globalEdges as $edge) {
		if ($edge[0] == $scanline) {
			$activeEdges[] = $edge;
		}
		if ($edge[0] > $scanline) {
			break;
		}
	}

	// fill polygon
	while (count($activeEdges) > 0) {
		// draw
		$n = count($activeEdges);

		for ($i = 0; $i < $n; $i = $i + 1) {
			$xA = round($activeEdges[$i][2]);
			$xB = round($activeEdges[(($i + 1) % $n)][2]);

			//for ($xNow = $xA; $xNow <= $xB; $xNow++) {
			//	imagesetpixel($im, $xNow, $scanline, $color);
			//}
			imagesetpixel($im, $xA, $scanline, $color);
			imagesetpixel($im, $xB, $scanline, $color);
		}
		
		// remove edges and update x
		$activeEdgesNew = array();
		
		foreach ($activeEdges as $edge) {
			if ($edge[1] != $scanline) {
				$edge[2] += $edge[3];
				$activeEdgesNew[] = $edge;
			}
		}
		
		$activeEdges = $activeEdgesNew;
		
		// increase scanline
		$scanline++;
		
		// reorder global and active edge table
		$globalEdgesNew = array();
		
		foreach ($globalEdges as $edge) {
			if ($edge[0] == $scanline) {
				$activeEdges[] = $edge;
			} else {
				$globalEdgesNew[] = $edge;
			}
		}
		
		$globalEdges = $globalEdgesNew;
		
		// reorder according x value
		usort($activeEdges, 'sortActive');
	}
}

function imagecolorshade(&$im, $color, $value) {
	$oldT = ($color & 0x7F000000) >> 24;
	$oldR = ($color & 0x00FF0000) >> 16;
	$oldG = ($color & 0x0000FF00) >> 8; 
	$oldB = $color & 0x000000FF;

	$newR = min((int)($oldR * $value), 255);
	$newG = min((int)($oldG * $value), 255);
	$newB = min((int)($oldB * $value), 255);

	return imagecolorallocatealpha($im, $newR, $newG, $newB, $oldT); 
}

class Point3D {
	public $x;
	public $y;
	public $z;
 
	public function __construct($x = 0, $y = 0, $z = 0) {
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}
	
	public function set($x = 0, $y = 0, $z = 0) {
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}
 
	public function rotateX($angle) {
		$rad = $angle * M_PI / 180;
		$cosa = cos($rad);
		$sina = sin($rad);
		$y = $this->y * $cosa - $this->z * $sina;
		$z = $this->y * $sina + $this->z * $cosa;
		return new Point3D($this->x, $y, $z);
	}
 
	public function rotateY($angle) {
		$rad = $angle * M_PI / 180;
		$cosa = cos($rad);
		$sina = sin($rad);
		$z = $this->z * $cosa - $this->x * $sina;
		$x = $this->z * $sina + $this->x * $cosa;
		return new Point3D($x, $this->y, $z);
	}
 
	public function rotateZ($angle) {
		$rad = $angle * M_PI / 180;
		$cosa = cos($rad);
		$sina = sin($rad);
		$x = $this->x * $cosa - $this->y * $sina;
		$y = $this->x * $sina + $this->y * $cosa;
		return new Point3D($x, $y, $this->z);
	}
	
	public static function add(Point3D $a, Point3D $b) {
		return new Point3D($a->x + $b->x, $a->y + $b->y, $a->z + $b->z);
	}
	
	public static function add3(Point3D $a, Point3D $b, Point3D $c) {
		return new Point3D($a->x + $b->x + $c->x, $a->y + $b->y + $c->y, $a->z + $b->z + $c->z);
	}
	
	public static function sub(Point3D $a, Point3D $b) {
		return new Point3D($a->x - $b->x, $a->y - $b->y, $a->z - $b->z);
	}
	
	public static function div(Point3D $a, $div) {
		return new Point3D($a->x / $div, $a->y / $div, $a->z / $div);
	}
}

class Object {
	public $points = array();
	
	public function __construct($points) {
		$this->points = $points;
		
		$this->recalc();
	}
	
	public function rotateX($angle) {
		foreach ($this->points as $key => $value) {
			$this->points[$key] = $value->rotateX($angle);
		}
		
		$this->recalc();
	}
	
	public function rotateY($angle) {
		foreach ($this->points as $key => $value) {
			$this->points[$key] = $value->rotateY($angle);
		}
		$this->recalc();
	}
	
	public function rotateZ($angle) {
		foreach ($this->points as $key => $value) {
			$this->points[$key] = $value->rotateZ($angle);
		}
		$this->recalc();
	}
	
	public function recalc() {
	
	}
}

class Line extends Object {
	protected $color;

	public function __construct($points = array(), $color = array(0, 0, 0)) {
		$this->color = $color;
		
		parent::__construct($points);
	}

	public function draw($im) {
		if (count($this->points > 1)) {
			$pointA = $this->points[0];
			for ($i = 1; $i < count($this->points); $i++) {
				$pointB = $this->points[$i];
				
				$im->drawLine($pointA, $pointB, $this->color);
				
				$pointA = $pointB;
			}
		}
	}
}

class Cube extends Object {
	protected $color;
	protected $texture;
	protected $shading;
	protected $faces;
	protected $avgZ;
	
	public function __construct($pointZero = null, $width = 2, $color = array(100, 100, 100), $texture = null) {
		$this->color = $color;
	
		if (is_null($pointZero)) {
			$pointZero = new Point3D(-1, -1, -1);
		}
	
		$points = array(
			new Point3D($pointZero->x, $pointZero->y, $pointZero->z),
			new Point3D($pointZero->x + $width, $pointZero->y, $pointZero->z),
			new Point3D($pointZero->x + $width, $pointZero->y + $width, $pointZero->z),
			new Point3D($pointZero->x, $pointZero->y + $width, $pointZero->z),
			new Point3D($pointZero->x, $pointZero->y, $pointZero->z + $width),
			new Point3D($pointZero->x + $width, $pointZero->y, $pointZero->z + $width),
			new Point3D($pointZero->x + $width, $pointZero->y + $width, $pointZero->z + $width),
			new Point3D($pointZero->x, $pointZero->y + $width, $pointZero->z + $width)
		);
		
		$this->faces = array(
			array(0, 3, 1, 2), // front
			array(4, 7, 5, 6), // back
			array(3, 7, 2, 6), // top
			array(0, 4, 1, 5), // bottom
			array(1, 2, 5, 6), // right
			array(0, 3, 4, 7)  // left
		);
		
		$this->shading = array(
			0.9,
			0.9,
			1.2,
			1.2,
			0.5,
			0.5
		);

		if (! is_null($texture)) {
			if (! isset($texture[0][0][0])) {
				$this->texture = array(
					$texture,
					$texture,
					$texture,
					$texture,
					$texture,
					$texture
				);
			} else {
				$this->texture = $texture;
			}
		}
		
		parent::__construct($points);
	}
	
	public function recalc() {
		$this->avgZ = array();
		foreach ($this->faces as $i => $f) {
			$this->avgZ[$i] = ($this->points[$f[0]]->z + $this->points[$f[1]]->z + $this->points[$f[2]]->z + $this->points[$f[3]]->z) / 4.0;
		}
		
		arsort($this->avgZ);
	
		parent::recalc();
	}

	public function draw($im) {
		foreach ($this->avgZ as $i => $z) {
			$f = $this->faces[$i];
			$s = $this->shading[$i];
			$t = $this->texture[$i];
			
			$im->draw3DSquare($this->points[$f[0]], $this->points[$f[1]], $this->points[$f[2]], $this->points[$f[3]], $this->color, $t, $s);
		}
	}
}

class Renderer {
	protected $width;
	protected $height;
	
	protected $im;
	protected $textures = array();
	protected $objects = array();
	
	public $fov = 256;
	public $viewDistance = 4;
	
	public function __construct($width = 200, $height = 100, $colorBg = array(249, 249, 249)) {
		$this->width = $width;
		$this->height = $height;
	}
	
	public function __destruct() {
	
	}
	
	public function addObject($object) {
		$this->objects[] = $object;
	}
	
	public function draw() {
		foreach ($this->objects as $object) {
			$object->draw($this);
		}
	}
	
	public function rotateX($angle) {
		foreach ($this->objects as $object) {
			$object->rotateX($angle);
			$object->recalc();
		}
	}

	public function rotateY($angle) {
		foreach ($this->objects as $object) {
			$object->rotateY($angle);
			$object->recalc();
		}
	}
	
	public function rotateZ($angle) {
		foreach ($this->objects as $object) {
			$object->rotateZ($angle);
			$object->recalc();
		}
	}
	
	public function project($point) {
		$factor = (float)($this->fov) / ($this->viewDistance + $point->z);
		$x = $point->x * $factor + $this->width / 2;
		$y = - $point->y * $factor + $this->height / 2;
		return new Point3D($x, $y, $point->z);
	}
}

class RendererGD extends Renderer {
	public function __construct($width = 200, $height = 100) {
		parent::__construct($width, $height, $colorBg);
	
		$this->im = imagecreatetruecolor($this->width, $this->height);
		
		// alpha
		imagealphablending($this->im, false);
		$colorAlphaAlloc = imagecolorallocatealpha($this->im, 0, 0, 0, 127);
		imagefill($this->im, 0, 0, $colorAlphaAlloc);
		imagesavealpha($this->im, true);
		imagealphablending($this->im, true);
	}
	
	public function __destruct() {
		imagedestroy($this->im);
		parent::__destruct();
	}
	
	public function drawLine($pointA, $pointB, $color = array(100, 100, 100)) {
		$pointA1 = $this->project($pointA);
		$pointB1 = $this->project($pointB);
		
		$colorAlloc = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

		imageline($this->im, $pointA1->x, $pointA1->x, $pointB1->x, $pointB1->y, $colorAlloc);
	}
	
	public function drawPolygon($points, $color = array(100, 100, 100)) {
		if (is_array($color)) {
			$colorAlloc = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
		} else {
			$colorAlloc = $color;
		}
	
		$points1 = array();
		
		foreach ($points as $point) {
			$point1 = $this->project($point);
			
			$points1[] = $point1->x;
			$points1[] = $point1->y;
		}

		imagepolygon($this->im, $points1, count($points), $colorAlloc);
	}
	
	public function drawFilledPolygon($points, $color = array(100, 100, 100)) {
		if (is_array($color)) {
			$colorAlloc = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
		} else {
			$colorAlloc = $color;
		}
	
		$points1 = array();
		$points2 = array();
		
		foreach ($points as $point) {
			$point1 = $this->project($point);
			
			$points1[] = $point1->x;
			$points1[] = $point1->y;
			
			$points2[] = array($point1->x, $point1->y);
		}

		imagefilledpolygon($this->im, $points1, count($points), $colorAlloc);
		//imagepolygontexture($this->im, $points2, $colorAlloc);
	}
	
	public function draw3DSquare($pointA, $pointB, $pointC, $pointD, $color = array(100, 100, 100), $texture = null, $shading = 0) {
		if (is_null($texture)) {
			if (is_array($color)) {
				$colorAlloc = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
			} else {
				$colorAlloc = $color;
			}
			
			if ($shading != 0) {
				$colorAlloc = imagecolorshade($this->im, $colorAlloc, $shading);
			}
			
			$this->drawFilledPolygon(array($pointA, $pointB, $pointD, $pointC), $colorAlloc);
		} else {
			// get texture info
			$textureWidth = count($texture[0]);
			$textureHeight = count($texture);
			
			// left to right of texture (pointA to pointB)
			$points1 = array();		
			$points1[] = new Point3D(0, 0, 0);
			
			$v = Point3D::div(Point3D::sub($pointB, $pointA), $textureWidth);
	
			for ($i = 1; $i <= $textureWidth; $i++) {
				$points1[] = Point3D::add($points1[($i - 1)], $v);
			}
	
			// top to bottom of texture (pointA to pointC)
			$points2 = array();		
			$points2[] = new Point3D(0, 0, 0);
					
			$v = Point3D::div(Point3D::sub($pointC, $pointA), $textureHeight);
			
			for ($i = 1; $i <= $textureHeight; $i++) {
				$points2[] = Point3D::add($points2[($i - 1)], $v);
			}
	
			// now draw it
			for ($y = 0; $y < $textureHeight; $y++) {
				for ($x = 0; $x < $textureWidth; $x++) {
					$colorAlloc = $texture[$y][$x];
					
					if ($shading != 0) {
						$colorAlloc = imagecolorshade($this->im, $colorAlloc, $shading);
					}
					
					$pointAtemp = Point3D::add3($points1[$x], $points2[$y], $pointA);
					$pointBtemp = Point3D::add3($points1[$x], $points2[($y + 1)], $pointA);
					$pointCtemp = Point3D::add3($points1[($x + 1)], $points2[$y], $pointA);
					$pointDtemp = Point3D::add3($points1[($x + 1)], $points2[($y + 1)], $pointA);
	
					$this->drawFilledPolygon(array($pointAtemp, $pointBtemp, $pointDtemp, $pointCtemp), $colorAlloc);
				}
			}
		}
		
		// now draw outline with AA
		//$this->drawPolygon(array($pointA, $pointB, $pointD, $pointC));
		/*
		$pointA1 = $this->project($pointA);
		$pointB1 = $this->project($pointB);
		$pointC1 = $this->project($pointC);
		$pointD1 = $this->project($pointD);
		$points1 = array(
			array($pointA1->x, $pointA1->y),
			array($pointB1->x, $pointB1->y),
			array($pointD1->x, $pointD1->y),
			array($pointC1->x, $pointC1->y)
		);
		imagepolygonlineaa($this->im, $points1, 0);
		*/
	}
		
	public function draw() {
		parent::draw();
		
		header("Content-Type: image/png");
		imagepng($this->im);
	}
}

class Texture {
	protected $many = false;
	protected $numX = 1;
	protected $numY = 1;
	protected $path = '../images/null.png';

	protected $im;
	protected $width;
	protected $height;

	public function __construct($path = null) {
		if (is_null($path)) {
			$path = $this->path;
		}
	
		$this->im = imagecreatefrompng($path);
		imagealphablending($this->im, true);
		
		$this->width = imagesx($this->im) / $this->numX;
		$this->height = imagesy($this->im) / $this->numY;
	}

	public function __destruct() {
		imagedestroy($this->im);
	}

	public function get($xPos = 0, $yPos = 0) {
		$texture = array();
		
		for ($y = 0; $y < $this->height; $y++) {
			$texture[$y] = array();
			
			for ($x = 0; $x < $this->width; $x++) {
				$texture[$y][$x] = imagecolorat($this->im, $x + ($xPos * $this->width), $y + ($yPos * $this->height));
			}
		}
		
		return $texture;
	}
	
	public static function create($path) {
		$t = new Texture($path);
		$texture = $t->get();
		unset($t);
		
		return $texture;
	}
}

class TextureTerrain extends Texture {
	protected $numX = 16;
	protected $numY = 16;
	protected $path = '../images/terrain.png';

	public static function create($xPos = 0, $yPos = 0) {
		$t = new TextureTerrain();
		$texture = $t->get($xPos, $yPos);
		unset($t);
		
		return $texture;
	}
}




//$zero = new Point3D(0, 0, 0);
//$x = new Point3D(1, 0, 0);
//$y = new Point3D(0, 1, 0);
//$z = new Point3D(0, 0, 1);

$x = (isset($_GET['x'])) ? $_GET['x']: 1;
$y = (isset($_GET['y'])) ? $_GET['y']: 0;


$t = new TextureTerrain();
//$texWoodPlanks = $t->get(4, 0);
//$texWool = $t->get(1, 3);
$tex = $t->get($x, $y);
//$texLapis = $t->get(0, 9);
//$texClay = $t->get(8, 4);
/*
$cube = new Cube(new Point3D(-2.5, -0.5, -0.5), 0.6, null, $texWoodPlanks);
$img->addObject($cube);
$cube = new Cube(new Point3D(-1.5, -0.5, -0.5), 0.7, null, $texWool);
$img->addObject($cube);
$cube = new Cube(new Point3D(1.5, -0.5, -0.5), 1, null, $texClay);
$img->addObject($cube);
$cube = new Cube(new Point3D(0.5, -0.5, -0.5), 0.9, null, $texLapis);
$img->addObject($cube);
*/

$img = new RendererGd(32, 32);
$cube = new Cube(new Point3D(-0.14, -0.14, -0.14), 0.28, null, $tex);
$img->addObject($cube);
$img->rotateY(45);
$img->rotateX(-35);

/*
$img = new RendererGd(800, 800);
$cube = new Cube(new Point3D(-3, -3, -3), 6, null, $tex);
$img->addObject($cube);
$img->rotateY(45);
$img->rotateX(-35);
$img->viewDistance = 8;
$img->fov = 512;
*/

//$img->addObject(new Line(array($zero, $x), array(255, 0, 0)));
//$img->addObject(new Line(array($zero, $y), array(0, 255, 0)));
//$img->addObject(new Line(array($zero, $z), array(0, 0, 255)));


$img->draw();
unset($img);
?>