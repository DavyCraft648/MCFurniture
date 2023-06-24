<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\RotatableTrait;

class ChristmasTree extends \pocketmine\block\Transparent implements \customiesdevs\customies\block\permutations\Permutable{
	use RotatableTrait;

	public function getFlammability() : int{
		return 5;
	}

	public function getFlameEncouragement() : int{
		return 5;
	}
}
