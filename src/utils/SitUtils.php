<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture\utils;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use function abs;

class SitUtils{

	/** @var array<int, array{eid: int, pos: Position, blockPos: Vector3, updateYaw: bool, yaw: float}> */
	public static array $sittingData = [];
	/** @var array<string, array<int, int>> */
	private static array $sitBlocks = [];

	public static function isSitting(Player $player) : bool{
		return isset(self::$sittingData[$player->getId()]);
	}

	public static function isPositionUsed(Position $pos) : bool{
		return self::getPlayerIdPos($pos) !== null;
	}

	public static function getPlayerIdPos(Position $pos) : ?int{
		return self::$sitBlocks[$pos->getWorld()->getFolderName()][World::blockHash($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ())] ?? null;
	}

	public static function unsetSit(Player $player) : void{
		$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
		$player->sendMessage(TranslationMessage::no_longer_sit());

		$world = $player->getWorld();
		$world->broadcastPacketToViewers($player->getPosition(), RemoveActorPacket::create(self::$sittingData[$playerId = $player->getId()]['eid']));
		$world->broadcastPacketToViewers($player->getPosition(), SetActorLinkPacket::create(new EntityLink(self::$sittingData[$playerId]['eid'], $playerId, EntityLink::TYPE_REMOVE, true, true)));

		$blockPos = self::$sittingData[$playerId]["blockPos"];
		unset(self::$sitBlocks[$world->getFolderName()][World::blockHash($blockPos->x, $blockPos->y, $blockPos->z)]);
		unset(self::$sittingData[$player->getId()]);
	}

	public static function sit(Player $player, Block $block, Vector3 $pos, false|float $yaw = null) : bool{
		if(self::isSitting($player)){
			$player->sendMessage(TranslationMessage::already_sit());
			return false;
		}

		if(self::isPositionUsed($blockPos = $block->getPosition())){
			$player->sendMessage(TranslationMessage::seat_occupied());
			return false;
		}

		self::$sittingData[$player->getId()] = [
			'eid' => Entity::nextRuntimeId(),
			'pos' => Position::fromObject($pos, $player->getWorld()),
			'blockPos' => $blockPos->asVector3(),
			'updateYaw' => $yaw === false,
			'yaw' => ($yaw === false ? null : $yaw) ?? (($pYaw = $player->getLocation()->getYaw()) < 180 ? $pYaw + 180 : $pYaw - 180)
		];
		self::$sitBlocks[$player->getWorld()->getFolderName()][World::blockHash($blockPos->x, $blockPos->y, $blockPos->z)] = $player->getId();

		$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);
		self::broadcastSit($player->getId());

		$player->sendMessage(TranslationMessage::now_sit());
		return true;
	}

	/**
	 * @param int $playerId
	 * @param NetworkSession[] $sessions
	 */
	public static function broadcastSit(int $playerId, array $sessions = null) : void{
		$pos = self::$sittingData[$playerId]["pos"];
		$pk = AddActorPacket::create(
			$eid = self::$sittingData[$playerId]["eid"],
			$eid,
			"mcfurniture:sit",
			$pos->asVector3(),
			null,
			0.0,
			$yaw = self::$sittingData[$playerId]["yaw"],
			$yaw,
			$yaw,
			[],
			[EntityMetadataProperties::FLAGS => new LongMetadataProperty(1 << EntityMetadataFlags::IMMOBILE | 1 << EntityMetadataFlags::SILENT | 1 << EntityMetadataFlags::INVISIBLE)],
			new PropertySyncData([], []),
			[new EntityLink($eid, $playerId, EntityLink::TYPE_RIDER, true, true)]
		);

		if($sessions === null){
			$pos->getWorld()->broadcastPacketToViewers($pos, $pk);
		}else{
			foreach($sessions as $session){
				$session->sendDataPacket($pk);
			}
		}
	}

	public static function optimizeRotation(Player $player, float $yaw) : void{
		if(!self::$sittingData[$player->getId()]["updateYaw"] || abs(self::$sittingData[$player->getId()]["yaw"] - $yaw) < 10){
			return;
		}

		$pk = MoveActorAbsolutePacket::create(
			self::$sittingData[$player->getId()]['eid'],
			self::$sittingData[$player->getId()]['pos'],
			0.0,
			self::$sittingData[$player->getId()]["yaw"] = $yaw,
			$yaw,
			0
		);
		$player->getWorld()->broadcastPacketToViewers($player->getPosition(), $pk);
	}
}