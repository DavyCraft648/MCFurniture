<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use customiesdevs\customies\block\permutations\{BlockProperty, Permutation};
use pocketmine\block\{Block, Transparent};
use pocketmine\data\bedrock\block\convert\{BlockStateReader, BlockStateWriter};
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\math\{Facing, Vector3};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class Table extends Transparent implements \customiesdevs\customies\block\permutations\Permutable{

	private bool $hasSouth = false;
	private bool $hasNorth = false;
	private bool $hasEast = false;
	private bool $hasWest = false;

	public function getBlockProperties() : array{
		return [
			new BlockProperty("mcfurniture:connected_north", [false, true]),
			new BlockProperty("mcfurniture:connected_south", [false, true]),
			new BlockProperty("mcfurniture:connected_west", [false, true]),
			new BlockProperty("mcfurniture:connected_east", [false, true])
		];
	}

	public function getPermutations() : array{
		return [
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table2")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table3")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table5")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table6")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table9")), // Barat Laut
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table8")), // Timur Laut
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == false"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table10")), // Barat Daya
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == false && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table7")), // Tenggara
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == true && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == false && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4")),
			(new Permutation("query.block_property('mcfurniture:connected_north') == true && query.block_property('mcfurniture:connected_south') == false && query.block_property('mcfurniture:connected_west') == true && query.block_property('mcfurniture:connected_east') == true"))
				->withComponent("minecraft:geometry", CompoundTag::create()
					->setString("identifier", "geometry.table4"))
		];
	}

	public function onNearbyBlockChange() : void{
		if($this->checkStateFor($this)){
			$this->position->getWorld()->setBlock($this->position, $this, false);
		}
	}

	private function checkStateFor(Table $table) : bool{
		$changed = false;
		$south = $table->getSide(Facing::SOUTH);
		if($south instanceof Table !== $table->hasSouth){
			$table->hasSouth = !$table->hasSouth;
			$changed = true;
		}
		$north = $table->getSide(Facing::NORTH);
		if($north instanceof Table !== $table->hasNorth){
			$table->hasNorth = !$table->hasNorth;
			$changed = true;
		}
		$east = $table->getSide(Facing::EAST);
		if($east instanceof Table !== $table->hasEast){
			$table->hasEast = !$table->hasEast;
			$changed = true;
		}
		$west = $table->getSide(Facing::WEST);
		if($west instanceof Table !== $table->hasWest){
			$table->hasWest = !$table->hasWest;
			$changed = true;
		}
		return $changed;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		$this->checkStateFor($this);
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function getCurrentBlockProperties() : array{
		return [$this->hasSouth, $this->hasNorth, $this->hasEast, $this->hasWest];
	}

	public function setHasSouth(bool $hasSouth) : Table{
		$this->hasSouth = $hasSouth;
		return $this;
	}

	public function hasSouth() : bool{
		return $this->hasSouth;
	}

	public function setHasNorth(bool $hasNorth) : Table{
		$this->hasNorth = $hasNorth;
		return $this;
	}

	public function hasNorth() : bool{
		return $this->hasNorth;
	}

	public function setHasEast(bool $hasEast) : Table{
		$this->hasEast = $hasEast;
		return $this;
	}

	public function hasEast() : bool{
		return $this->hasEast;
	}

	public function setHasWest(bool $hasWest) : Table{
		$this->hasWest = $hasWest;
		return $this;
	}

	public function hasWest() : bool{
		return $this->hasWest;
	}

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->bool($this->hasSouth);
		$w->bool($this->hasNorth);
		$w->bool($this->hasEast);
		$w->bool($this->hasWest);
	}

	public function serializeState(BlockStateWriter $blockStateOut) : void{
		$blockStateOut->writeBool("mcfurniture:connected_north", $this->hasSouth())
			->writeBool("mcfurniture:connected_south", $this->hasNorth())
			->writeBool("mcfurniture:connected_west", $this->hasEast())
			->writeBool("mcfurniture:connected_east", $this->hasWest());
	}

	public function deserializeState(BlockStateReader $blockStateIn) : void{
		$this->setHasNorth($blockStateIn->readBool("mcfurniture:connected_north"))
			->setHasSouth($blockStateIn->readBool("mcfurniture:connected_south"))
			->setHasWest($blockStateIn->readBool("mcfurniture:connected_west"))
			->setHasEast($blockStateIn->readBool("mcfurniture:connected_east"));
	}
}
