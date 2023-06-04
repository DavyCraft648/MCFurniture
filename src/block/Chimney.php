<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\data\bedrock\block\convert\{BlockStateReader, BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;

class Chimney extends \pocketmine\block\Transparent implements \customiesdevs\customies\block\permutations\Permutable{

	private bool $bottom = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty("mcfurniture:bottom_part", [false, true])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('mcfurniture:bottom_part') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.chimney_top")),
			(new Permutation("q.block_property('mcfurniture:bottom_part') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.chimney_bottom"))
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->bottom];
	}

	public function setBottom(bool $bottom = true) : Chimney{
		$this->bottom = $bottom;
		return $this;
	}

	public function isBottom() : bool{
		return $this->bottom;
	}

	public function onNearbyBlockChange() : void{
		if($this->getSide(Facing::UP) instanceof Chimney !== $this->bottom){
			$this->position->getWorld()->setBlock($this->position, $this->setBottom(!$this->bottom));
		}
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->bool($this->bottom);
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeBool("mcfurniture:bottom_part", $this->isBottom());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setBottom($blockStateIn->readBool("mcfurniture:bottom_part"));
	}
}
