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

class Shower extends \pocketmine\block\Transparent implements Permutable{
	use HorizontalFacingTrait;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::FACING_DIRECTION, [2, 3, 4, 5])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('facing_direction'') == 2"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 0)
					->setInt("RZ", 0)
					->setFloat("SX", 1)
					->setFloat("SY", 1)
					->setFloat("SZ", 1)
					->setFloat("TX", 0)
					->setFloat("TY", 0)
					->setFloat("TZ", 0))
			/*->downgradeComponent(ProtocolInfo::PROTOCOL_1_19_80, "minecraft:transformation", "minecraft:rotation", CompoundTag::create()
				->setFloat("x", 0)
				->setFloat("y", 180)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('facing_direction'') == 3"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 2)
					->setInt("RZ", 0)
					->setFloat("SX", 1)
					->setFloat("SY", 1)
					->setFloat("SZ", 1)
					->setFloat("TX", 0)
					->setFloat("TY", 0)
					->setFloat("TZ", 0))
			/*->downgradeComponent(ProtocolInfo::PROTOCOL_1_19_80, "minecraft:transformation", "minecraft:rotation", CompoundTag::create()
				->setFloat("x", 0)
				->setFloat("y", 0)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('facing_direction') == 4"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 1)
					->setInt("RZ", 0)
					->setFloat("SX", 1)
					->setFloat("SY", 1)
					->setFloat("SZ", 1)
					->setFloat("TX", 0)
					->setFloat("TY", 0)
					->setFloat("TZ", 0))
			/*->downgradeComponent(ProtocolInfo::PROTOCOL_1_19_80, "minecraft:transformation", "minecraft:rotation", CompoundTag::create()
				->setFloat("x", 0)
				->setFloat("y", 270)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('facing_direction') == 5"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 3)
					->setInt("RZ", 0)
					->setFloat("SX", 1)
					->setFloat("SY", 1)
					->setFloat("SZ", 1)
					->setFloat("TX", 0)
					->setFloat("TY", 0)
					->setFloat("TZ", 0))
			/*->downgradeComponent(ProtocolInfo::PROTOCOL_1_19_80, "minecraft:transformation", "minecraft:rotation", CompoundTag::create()
				->setFloat("x", 0)
				->setFloat("y", 90)
				->setFloat("z", 0))*/,
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->facing];
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