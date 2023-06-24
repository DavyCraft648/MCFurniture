<?php

declare(strict_types=1);

namespace DavyCraft648\MCFurniture\utils;

use DavyCraft648\MCFurniture\block\Chair;
use DavyCraft648\MCFurniture\Main;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class SitUtils
{

    public Main $plugin;
    
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function isSitting(Player $player): bool {
        return isset($this->plugin->sittingData[strtolower($player->getName())]);
    }

    public function unsetSit(Player $player): void {
        $pk1 = new RemoveActorPacket();
        $pk1->actorUniqueId = $this->plugin->sittingData[strtolower($player->getName())]['eid'];

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->plugin->sittingData[strtolower($player->getName())]['eid'], $player->getId(), EntityLink::TYPE_REMOVE, true, true);

        unset($this->plugin->sittingData[strtolower($player->getName())]);

        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
        $player->sendMessage("Kamu sudah tidak duduk");

        foreach (Server::getInstance()->getOnlinePlayers() as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($pk1);
            $viewer->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function sit(Player $player, Block $block): void {
        if($block instanceof Chair){
            $pos = $block->getPosition()->add(0.5, 1.6, 0.5);
        } else{
            return;
        }

        foreach ($this->plugin->sittingData as $playerName => $data) {
            if ($pos->equals($data['pos'])) {
                $player->sendMessage("Tempat ini telah terisi player lain");
                return;
            }
        }

        if ($this->isSitting($player)) {
            $player->sendMessage("kamu sudah duduk");
            return;
        }

        $this->setSit($player, Server::getInstance()->getOnlinePlayers(), new Position($pos->x, $pos->y, $pos->z, Server::getInstance()->getWorldManager()->getWorldByName($player->getWorld()->getFolderName())));

        $player->sendMessage("kamu sedang duduk!");
        $player->sendTip("sneak untuk berdiri");
    }

    public function setSit(Player $player, array $viewers, Position $pos, ?int $eid = null): void {
        if ($eid === null) {
            $eid = Entity::nextRuntimeId();
        }

        $pk = new AddActorPacket();
        $pk->actorRuntimeId = $eid;
        $pk->actorUniqueId = $eid;
        $pk->type = EntityIds::WOLF;

        $pk->position = $pos->asVector3();
        $pk->metadata = [
            EntityMetadataProperties::FLAGS => new LongMetadataProperty(1 << EntityMetadataFlags::IMMOBILE | 1 << EntityMetadataFlags::SILENT | 1 << EntityMetadataFlags::INVISIBLE),
        ];
        $pk->syncedProperties = new PropertySyncData([], []);

        $link = new SetActorLinkPacket();
        $link->link = new EntityLink($eid, $player->getId(), EntityLink::TYPE_RIDER, true, true);
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);

        foreach ($viewers as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($pk);
            $viewer->getNetworkSession()->sendDataPacket($link);
        }

        if ($this->isSitting($player)) {
            return;
        }

        $this->plugin->sittingData[strtolower($player->getName())] = [
            'eid' => $eid,
            'pos' => $pos
        ];
    }

    public function isToggleSit(Player $player): bool {
        return in_array(strtolower($player->getName()), $this->plugin->toggleSit, true);
    }

    public function unsetToggleSit(Player $player): void {
        unset($this->plugin->toggleSit[strtolower($player->getName())]);

        $player->sendMessage("You have enabled tap-on-block sit");
    }

    public function setToggleSit(Player $player): void {
        $this->plugin->toggleSit[] = strtolower($player->getName());

        $player->sendMessage("You have disabled tap-on-block sit!");
    }

    public function optimizeRotation(Player $player): void {
        $pk = new MoveActorAbsolutePacket();
        $pk->position = $this->plugin->sittingData[strtolower($player->getName())]['pos'];
        $pk->actorRuntimeId = $this->plugin->sittingData[strtolower($player->getName())]['eid'];
        $pk->pitch = $player->getLocation()->getPitch();
        $pk->yaw = $player->getLocation()->getYaw();
        $pk->headYaw = $player->getLocation()->getYaw();

//        Server::getInstance()->broadcastPackets(Server::getInstance()->getOnlinePlayers(), [$pk]);
        foreach (Server::getInstance()->getOnlinePlayers() as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($pk);
        }
    }

}