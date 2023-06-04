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

class Chair extends Transparent implements \customiesdevs\customies\block\permutations\Permutable{
	use HorizontalFacingTrait;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::FACING_DIRECTION, [2, 3, 4, 5]),
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('facing_direction') == 2"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 0)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('facing_direction') == 3"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 180)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('facing_direction') == 4"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 90)
					->setFloat("z", 0)),
			(new Permutation("q.block_property('facing_direction') == 5"))
				->withComponent("minecraft:rotation", CompoundTag::create()
					->setFloat("x", 0)
					->setFloat("y", 270)
					->setFloat("z", 0))
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->facing];
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player !== null){
			$this->facing = $player->getHorizontalFacing();
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
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
