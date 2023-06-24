<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\data\bedrock\block\{BlockStateNames, convert\BlockStateReader, convert\BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class Lamp extends \pocketmine\block\Transparent implements \customiesdevs\customies\block\permutations\Permutable{

	private bool $light = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty(BlockStateNames::LIT, [false, true])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("q.block_property('lit') == true"))
				->withComponent("minecraft:light_emission", CompoundTag::create()
					->setByte("emission", 15)),
			(new Permutation("q.block_property('lit') == false"))
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

	public function setLight(bool $light = true) : Lamp{
		$this->light = $light;
		return $this;
	}

	public function isLight() : bool{
		return $this->light;
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
