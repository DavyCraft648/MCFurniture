<?php

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\Permutable;
use pocketmine\block\Transparent;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\data\runtime\RuntimeDataDescriber;

class ShowerHead extends Transparent implements Permutable{
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
	public function serializeState(BlockStateWriter $blockStateOut) : void{
	}
	public function deserializeState(BlockStateReader $blockStateIn) : void{
	}
	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
	}
}