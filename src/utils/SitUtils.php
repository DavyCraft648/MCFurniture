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
}