<?php

declare(strict_types=1);

namespace DavyCraft648\MCFurniture;

use DavyCraft648\MCFurniture\utils\SitUtils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\player\Player;

class EventListener implements \pocketmine\event\Listener{

	public Main $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	/** @priority MONITOR */
	public function onDataPacketSend(DataPacketSendEvent $event) : void{
		foreach($event->getPackets() as $packet){
			if($packet instanceof AddPlayerPacket && isset(SitUtils::$sittingData[$packet->actorRuntimeId])){
				SitUtils::broadcastSit($packet->actorRuntimeId, $event->getTargets());
			}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		if(SitUtils::isSitting($player)){
			SitUtils::unsetSit($player);
		}
	}

	/** @priority MONITOR */
	public function onTeleport(EntityTeleportEvent $event) : void{
		$entity = $event->getEntity();

		if($entity instanceof Player && SitUtils::isSitting($entity)){
			SitUtils::unsetSit($entity);
		}
	}

	public function onDeath(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		if(SitUtils::isSitting($player)){
			SitUtils::unsetSit($player);
		}
	}

	/** @priority MONITOR */
	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();

		if(SitUtils::isSitting($player)){
			SitUtils::optimizeRotation($player, $event->getTo()->yaw);
		}
	}

	/** @priority MONITOR */
	public function onBlockBreak(BlockBreakEvent $event) : void{
		$blockPos = $event->getBlock()->getPosition();

		if(SitUtils::isPositionUsed($blockPos)){
			$player = $blockPos->getWorld()->getEntity(SitUtils::getPlayerIdPos($blockPos));
			if($player instanceof Player){
				SitUtils::unsetSit($player);
			}
		}
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		$player = $event->getOrigin()->getPlayer();

		if($player === null){
			return;
		}

		if($packet instanceof InteractPacket and $packet->action === InteractPacket::ACTION_LEAVE_VEHICLE && SitUtils::isSitting($player)){
			SitUtils::unsetSit($player);
		}
	}
}