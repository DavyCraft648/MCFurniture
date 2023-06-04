<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\block\{Block, Transparent};
use pocketmine\data\bedrock\block\{BlockStateNames, convert\BlockStateReader, convert\BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\math\{Facing, Vector3};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class CeilingLight extends Transparent implements \customiesdevs\customies\block\permutations\Permutable{

	private bool $light = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::LIT, [false, true])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('lit') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.ceiling_light_on"))
				->withComponent("minecraft:light_emission", CompoundTag::create()
					->setByte("emission", 15)),
			(new Permutation("q.block_property('lit') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("value", "geometry.ceiling_light_off"))
				->withComponent("minecraft:light_emission", CompoundTag::create()
					->setByte("emission", 0))
		];
	}

	public function getCurrentBlockProperties() : array{
		return [$this->light];
	}

	public function getLightLevel() : int{
		return $this->light ? 15 : 0;
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		$this->position->getWorld()->setBlock($this->position, $this->setLight(!$this->light));
		return true;
	}

	public function setLight(bool $light = true) : CeilingLight{
		$this->light = $light;
		return $this;
	}

	public function isLight() : bool{
		return $this->light;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($face !== Facing::DOWN){
			return false;
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->bool($this->light);
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeBool(BlockStateNames::LIT, $this->isLight());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setLight($blockStateIn->readBool(BlockStateNames::LIT));
	}
}
