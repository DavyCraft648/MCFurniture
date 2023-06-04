<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\block;

use pocketmine\world\particle\FlameParticle;
use function mt_rand;

class Candle extends \pocketmine\block\Transparent{

	public function writeStateToWorld() : void{
		parent::writeStateToWorld();
		$this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, mt_rand(20, 40));
	}

	public function ticksRandomly() : bool{
		return true;
	}

	public function onRandomTick() : void{
		($world = $this->position->getWorld())->addParticle((match(mt_rand(0, 4)){
			0 => $this->position->add(0, 0.95, 0),
			1 => $this->position->add(0.3, 0.7, 0),
			2 => $this->position->add(-0.3, 0.7, 0),
			3 => $this->position->add(0, 0.7, 0.3),
			4 => $this->position->add(0, 0.7, -0.3)
		})->add(0.5, 0.5, 0.5), new FlameParticle());
		$world->scheduleDelayedBlockUpdate($this->position, mt_rand(20, 40));
	}

	public function onScheduledUpdate() : void{
		$this->onRandomTick();
	}

	public function getLightLevel() : int{
		return 15;
	}
}
