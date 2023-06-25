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
}