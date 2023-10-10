<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\block\{Block, Transparent};
use pocketmine\data\bedrock\block\{BlockStateSerializeException, BlockStateStringValues,
	convert\BlockStateReader, convert\BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\math\{Axis, Facing, Vector3};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class FairyLight extends Transparent implements \customiesdevs\customies\block\permutations\Permutable{

	protected int $axis = Axis::X;

	public function getBlockProperties() : array{
		return [
			new BlockProperty("mcfurniture:light_axis", ["x", "z"]),
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('mcfurniture:light_axis') == 'x'"))
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
			(new Permutation("q.block_property('mcfurniture:light_axis') == 'z'"))
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
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->axis];
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->horizontalAxis($this->axis);
	}

	public function getAxis() : int{
		return $this->axis;
	}

	public function setAxis(int $axis) : self{
		if($axis !== Axis::X && $axis !== Axis::Z){
			throw new \InvalidArgumentException("Invalid axis");
		}
		$this->axis = $axis;
		return $this;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player !== null){
			$this->axis = Facing::axis(Facing::rotateY($player->getHorizontalFacing(), true));
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function getLightLevel() : int{
		return 10;
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeString("mcfurniture:light_axis", match($this->getAxis()){
			Axis::X => BlockStateStringValues::PORTAL_AXIS_X,
			Axis::Z => BlockStateStringValues::PORTAL_AXIS_Z,
			default => throw new BlockStateSerializeException("Invalid axis " . $this->getAxis())
		});
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setAxis(match($blockStateIn->readString("mcfurniture:light_axis")){
			BlockStateStringValues::PORTAL_AXIS_X => Axis::X,
			BlockStateStringValues::PORTAL_AXIS_Z => Axis::Z
		});
	}
}
