<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\block\{Block, Transparent, utils\HorizontalFacingTrait};
use pocketmine\data\bedrock\block\{BlockStateNames, convert\BlockStateReader, convert\BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class DigitalClock extends Transparent implements \customiesdevs\customies\block\permutations\Permutable{
	use HorizontalFacingTrait;

	private int $time = 0;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::FACING_DIRECTION, [2, 3, 4, 5]),
			new BlockProperty("mcfurniture:clock_time", [0, 1, 2, 3])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('mcfurniture:clock_time') == 0"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.clock_0600")),
			(new Permutation("q.block_property('mcfurniture:clock_time') == 1"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.clock_1200")),
			(new Permutation("q.block_property('mcfurniture:clock_time') == 2"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.clock_1800")),
			(new Permutation("q.block_property('mcfurniture:clock_time') == 3"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.clock_2400")),
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
					->setFloat("TZ", 0)),
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
					->setFloat("TZ", 0)),
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
					->setFloat("TZ", 0)),
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
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->facing, $this->time];
	}

	public function setTime(int $time) : DigitalClock{
		$this->time = $time;
		return $this;
	}

	public function getTime() : int{
		return $this->time;
	}

	public function getLightLevel() : int{
		return 2;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player !== null){
			$this->facing = $player->getHorizontalFacing();
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		$this->time >= 3 ? $this->time = 0 : ++$this->time;
		$this->position->getWorld()->setBlock($this->position, $this);
		return true;
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->horizontalFacing($this->facing);
		$w->int(2, $this->time);
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeFacingDirection($this->getFacing())
			->writeInt("mcfurniture:clock_time", $this->getTime());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setFacing($blockStateIn->readHorizontalFacing())
			->setTime($blockStateIn->readInt("mcfurniture:clock_time"));
	}
}
