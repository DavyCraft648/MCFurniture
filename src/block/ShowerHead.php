<?php

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\BlockProperty;
use customiesdevs\customies\block\permutations\Permutation;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\nbt\tag\CompoundTag;

class ShowerHead extends \pocketmine\block\Transparent implements \customiesdevs\customies\block\permutations\Permutable
{
	use HorizontalFacingTrait;


	public function getBlockProperties() : array{
		return [];
	}
	public function getPermutations() : array{
		return [];
	}
	public function getCurrentBlockProperties() : array{
		return [];
	}
	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
	}
}