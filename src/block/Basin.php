<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\block\Block;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\data\bedrock\block\{BlockStateNames, convert\BlockStateReader, convert\BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\{Item, ItemTypeIds, VanillaItems};
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class Basin extends \pocketmine\block\Transparent implements \customiesdevs\customies\block\permutations\Permutable{
	use HorizontalFacingTrait;

	private bool $filled = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty("mcfurniture:filled", [false, true]),
			new BlockProperty(BlockStateNames::FACING_DIRECTION, [2, 3, 4, 5])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('facing_direction') == 2"))
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
				->setFloat("y", 180)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('facing_direction') == 3"))
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
				->setFloat("y", 0)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('facing_direction') == 4"))
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
				->setFloat("y", 270)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('facing_direction') == 5"))
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
				->setFloat("y", 90)
				->setFloat("z", 0))*/,
			(new Permutation("q.block_property('mcfurniture:filled') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.basin")),
			(new Permutation("q.block_property('mcfurniture:filled') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.basin_filled")),
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->filled, $this->facing];
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeBool("mcfurniture:filled", $this->isFilled())
			->writeHorizontalFacing($this->getFacing());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setFilled($blockStateIn->readBool("mcfurniture:filled"))
			->setFacing($blockStateIn->readHorizontalFacing());
	}

	public function isFilled() : bool{
		return $this->filled;
	}

	public function setFilled(bool $filled = true) : Basin{
		$this->filled = $filled;
		return $this;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player !== null){
			$this->facing = $player->getHorizontalFacing();
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		if($this->isFilled()){
			if($item->getTypeId() === ItemTypeIds::BUCKET){
				$this->position->getWorld()->setBlock($this->position, $this->setFilled(false));
				$item->pop();
				$returnedItems[] = VanillaItems::WATER_BUCKET();
				return true;
			}
		}elseif($item->getTypeId() === ItemTypeIds::WATER_BUCKET){
			$this->position->getWorld()->setBlock($this->position, $this->setFilled());
			$item->pop();
			$returnedItems[] = VanillaItems::BUCKET();
			return true;
		}
		return false;
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->bool($this->filled);
		$w->horizontalFacing($this->facing);
	}
}
