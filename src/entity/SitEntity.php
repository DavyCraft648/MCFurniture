<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\entity;

use pocketmine\entity\EntitySizeInfo;

class SitEntity extends \pocketmine\entity\Entity{

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.0, 0.0);
	}

	protected function getInitialDragMultiplier() : float{
		return 0;
	}

	protected function getInitialGravity() : float{
		return 0;
	}

	public static function getNetworkTypeId() : string{
		return "mcfurniture:sit";
	}
}
