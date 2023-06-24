<?php

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\BlockProperty;
use customiesdevs\customies\block\permutations\Permutable;
use customiesdevs\customies\block\permutations\Permutation;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\nbt\tag\CompoundTag;

class Shower extends \pocketmine\block\Transparent{
	use HorizontalFacingTrait;
}