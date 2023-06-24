<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\utils;

use pocketmine\lang\Translatable;

class TranslationMessage{
	public static function no_longer_sit() : Translatable{
		return new Translatable("mcfurniture:sit.no_longer_sit");
	}

	public static function seat_occupied() : Translatable{
		return new Translatable("mcfurniture:sit.seat_occupied");
	}

	public static function already_sit() : Translatable{
		return new Translatable("mcfurniture:sit.already_sit");
	}

	public static function now_sit() : Translatable{
		return new Translatable("mcfurniture:sit.now_sit");
	}
}
