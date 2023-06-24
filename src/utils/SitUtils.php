<?php

/** Thanks to www.github.com/brokiem/SimpleLay */

declare(strict_types=1);

namespace DavyCraft648\MCFurniture\utils;

use DavyCraft648\MCFurniture\block\BarStool;
use DavyCraft648\MCFurniture\block\Chair;
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
use pocketmine\world\Position;
use function strtolower;

class SitUtils{

	/**
	 * @var array<string, array{eid: int, pos: Position}>
	 */
	public static array $sittingData = [];

	public static function isSitting(Player $player) : bool{
		return isset(self::$sittingData[strtolower($player->getName())]);
	}

	public static function unsetSit(Player $player) : void{
		$pk1 = RemoveActorPacket::create(self::$sittingData[strtolower($player->getName())]['eid']);

		$pk = SetActorLinkPacket::create(new EntityLink(self::$sittingData[strtolower($player->getName())]['eid'], $player->getId(), EntityLink::TYPE_REMOVE, true, true));
		unset(self::$sittingData[strtolower($player->getName())]);

		$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
		$player->sendMessage(TranslationMessage::no_longer_sit());

		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $pk1);
		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $pk);
	}

	public static function sit(Player $player, Block $block) : void{
		if($block instanceof Chair or $block instanceof BarStool){
			$pos = $block->getPosition()->add(0.5, 1.6, 0.5);
		}else{
			return;
		}

		foreach(self::$sittingData as $data){
			if($pos->equals($data['pos'])){
				$player->sendMessage(TranslationMessage::seat_occupied());
				return;
			}
		}

		if(self::isSitting($player)){
			$player->sendMessage(TranslationMessage::already_sit());
			return;
		}

		self::setSit($player, Position::fromObject($pos, $player->getWorld()));

		$player->sendMessage(TranslationMessage::now_sit());
	}

	public static function setSit(Player $player, Position $pos, ?int $eid = null) : void{
		if($eid === null){
			$eid = Entity::nextRuntimeId();
		}

		$pk = AddActorPacket::create(
			$eid,
			$eid,
			EntityIds::WOLF,
			$pos->asVector3(),
			null,
			0.0,
			0.0,
			0.0,
			0.0,
			[],
			[EntityMetadataProperties::FLAGS => new LongMetadataProperty(1 << EntityMetadataFlags::IMMOBILE | 1 << EntityMetadataFlags::SILENT | 1 << EntityMetadataFlags::INVISIBLE)],
			new PropertySyncData([], []),
			[new EntityLink($eid, $player->getId(), EntityLink::TYPE_RIDER, true, true)]
		);
		$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);

		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $pk);

		if(self::isSitting($player)){
			return;
		}

		self::$sittingData[strtolower($player->getName())] = [
			'eid' => $eid,
			'pos' => $pos
		];
	}

	public static function optimizeRotation(Player $player) : void{
		$pk = MoveActorAbsolutePacket::create(
			self::$sittingData[strtolower($player->getName())]['eid'],
			self::$sittingData[strtolower($player->getName())]['pos'],
			$player->getLocation()->getPitch(),
			$player->getLocation()->getYaw(),
			$player->getLocation()->getYaw(),
			0
		);
		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $pk);
	}
}