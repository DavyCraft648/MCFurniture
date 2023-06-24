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

class ShowerHead extends \pocketmine\block\Transparent implements \customiesdevs\customies\block\permutations\Permutable{
	use HorizontalFacingTrait;

	private bool $water = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::FACING_DIRECTION, [2, 3, 4, 5]),
			new BlockProperty("p:water", [false, true])
		];
	}
	public function getPermutations() : array{
		return [

		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->facing, $this->water];
	}
	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->horizontalFacing($this->facing);
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeHorizontalFacing($this->getFacing());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setFacing($blockStateIn->readHorizontalFacing());
	}
}