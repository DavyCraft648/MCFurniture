<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture;

use customiesdevs\customies\block\{CustomiesBlockFactory, Material, Model};
use customiesdevs\customies\item\CreativeInventoryInfo;
use DavyCraft648\MCFurniture\block\{BarStool, BedsideCabinet, Bin, Blinds, Candle, CeilingLight, Chair, Chimney,
	DigitalClock, FairyLight, Lamp, Table};
use pocketmine\block\{BlockBreakInfo, BlockIdentifier, BlockTypeInfo};
use pocketmine\block\{Block, Opaque, Slab, Stair};
use pocketmine\entity\Entity;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
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
use pocketmine\resourcepacks\ZippedResourcePack;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\world\Position;
use Symfony\Component\Filesystem\Path;
use function array_merge;

class Main extends \pocketmine\plugin\PluginBase{

    public array $toggleSit = [];

    public array $sittingData = [];

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->saveResource("MCFurniture.mcpack");
		$rpManager = $this->getServer()->getResourcePackManager();
		$rpManager->setResourceStack(array_merge($rpManager->getResourceStack(), [new ZippedResourcePack(Path::join($this->getDataFolder(), "MCFurniture.mcpack"))]));
		(new \ReflectionProperty($rpManager, "serverForceResources"))->setValue($rpManager, true);

		// $this->getServer()->getPluginManager()->registerEvent(PlayerInteractEvent::class, function(PlayerInteractEvent $event) : void{
		// 	$event->getPlayer()->sendMessage(GlobalBlockStateHandlers::getSerializer()->serialize($event->getBlock()->getStateId())->toNbt()->toString());
		// }, EventPriority::NORMAL, $this);

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new Bin(new BlockIdentifier($id), "Bin", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:bin",
			new Model([new Material(Material::TARGET_ALL, "bin", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.bin", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE),
		);

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new Candle(new BlockIdentifier($id), "Candle", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:candle",
			new Model([new Material(Material::TARGET_ALL, "candelabra", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.candle", new Vector3(-8, 0, -8), new Vector3(12, 16, 12)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
		);

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new CeilingLight(new BlockIdentifier($id), "Ceiling Light", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:ceiling_light",
			new Model([new Material(Material::TARGET_ALL, "ceiling_light", Material::RENDER_METHOD_BLEND)], "geometry.ceiling_light_off", new Vector3(-2, 7, -2), new Vector3(4, 8, 4)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
		);

		foreach(["acacia", "andesite", "birch", "dark_oak", "diorite", "granite", "jungle", "oak", "spruce", "stone"] as $variant){
			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new BedsideCabinet(new BlockIdentifier($id), "Bedside Cabinet", new BlockTypeInfo(new BlockBreakInfo(2))),
				"mcfurniture:{$variant}_bedside_cabinet",
				new Model([new Material(Material::TARGET_ALL, "bedside_cabinet_$variant", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.bedside_cabinet", new Vector3(-8, 0, -8), new Vector3(16, 12, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);

			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new Chair(new BlockIdentifier($id), "Chair", new BlockTypeInfo(new BlockBreakInfo(0.3))),
				"mcfurniture:{$variant}_chair",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_chair", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.chair", new Vector3(-8, 0, -8), new Vector3(16, 12, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);

			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new Table(new BlockIdentifier($id), "Table", new BlockTypeInfo(new BlockBreakInfo(1))),
				"mcfurniture:{$variant}_table",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_table", Material::RENDER_METHOD_OPAQUE)], "geometry.table", new Vector3(-8, 12, -8), new Vector3(16, 15, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);
		}

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new Chimney(new BlockIdentifier($id), "Chimney", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:chimney",
			new Model([new Material(Material::TARGET_ALL, "chimney", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.chimney_top", new Vector3(-6, 0, -6), new Vector3(12, 16, 12)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_CONSTRUCTION, CreativeInventoryInfo::NONE)
		);

		foreach([
			        "black", "blue", "brown", "cyan", "gray", "green", "light_blue", "light_gray", "lime", "magenta",
			        "orange", "pink", "purple", "red", "white", "yellow"
		        ] as $variant){
			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new BarStool(new BlockIdentifier($id), "Bar Stool", new BlockTypeInfo(new BlockBreakInfo(0.3))),
				"mcfurniture:{$variant}_clay_bar_stool",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_clay_bar_stool", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.bar_stool", new Vector3(-8, 0, -8), new Vector3(16, 12, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);

			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new DigitalClock(new BlockIdentifier($id), "Digital Clock", new BlockTypeInfo(new BlockBreakInfo(0.3))),
				"mcfurniture:{$variant}_digital_clock",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_digital_clock", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.clock_0600", new Vector3(-4, 0, -1), new Vector3(8, 5, 3)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);

			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new Lamp(new BlockIdentifier($id), "Lamp", new BlockTypeInfo(new BlockBreakInfo(0.3))),
				"mcfurniture:{$variant}_lamp",
                new Model([new Material(Material::TARGET_ALL, "{$variant}_lamp", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.lamp", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);
		}

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new FairyLight(new BlockIdentifier($id), "Fairy Light", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:fairy_light",
			new Model([new Material(Material::TARGET_ALL, "fairy_light", Material::RENDER_METHOD_BLEND)], "geometry.fairy_light", new Vector3(-8, 11, -1), new Vector3(16, 5, 3)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
		);

		foreach(["acacia", "birch", "dark_oak", "jungle", "oak", "spruce"] as $variant){
			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new Blinds(new BlockIdentifier($id), "Blinds", new BlockTypeInfo(new BlockBreakInfo(0.3))),
				"mcfurniture:{$variant}_blinds",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_blinds", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.blinds_open", new Vector3(-8, 0, -8), new Vector3(16, 16, 1)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);

			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new Chair(new BlockIdentifier($id), "Park Bench", new BlockTypeInfo(new BlockBreakInfo(0.3))),
				"mcfurniture:{$variant}_park_bench",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_park_bench", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.park_bench", new Vector3(-8, 0, -8), new Vector3(16, 12, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);
		}
	}

    // SIT FUNCTION

    public function isSitting(Player $player): bool {
        return isset($this->sittingData[strtolower($player->getName())]);
    }

    public function unsetSit(Player $player): void {
        $pk1 = new RemoveActorPacket();
        $pk1->actorUniqueId = $this->sittingData[strtolower($player->getName())]['eid'];

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->sittingData[strtolower($player->getName())]['eid'], $player->getId(), EntityLink::TYPE_REMOVE, true, true);

        unset($this->sittingData[strtolower($player->getName())]);

        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
        $player->sendMessage(TextFormat::colorize($this->getConfig()->get("no-longer-sit-message", "&6You are no longer sitting!")));

        foreach ($this->getServer()->getOnlinePlayers() as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($pk1);
            $viewer->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function sit(Player $player, Block $block): void {
//        if ($block instanceof Stair or $block instanceof Slab) {
//            $pos = $block->getPosition()->add(0.5, 1.5, 0.5);
//        } elseif ($block instanceof Opaque) {
//            $pos = $block->getPosition()->add(0.5, 2.1, 0.5);
//        } else {
//            $player->sendMessage(TextFormat::colorize($this->getConfig()->get("cannot-be-occupied-sit", "&cYou can only sit on the Opaque, Stair, or Slab block!")));
//            return;
//        }
        if($block instanceof Chair){
            $pos = $block->getPosition()->add(0.5, 1.6, 0.5);
        } else{
            return;
        }

        foreach ($this->sittingData as $playerName => $data) {
            if ($pos->equals($data['pos'])) {
                $player->sendMessage("Tempat ini telah terisi player lain");
                return;
            }
        }

        if ($this->isSitting($player)) {
            $player->sendMessage("kamu sudah duduk");
            return;
        }

        $this->setSit($player, $this->getServer()->getOnlinePlayers(), new Position($pos->x, $pos->y, $pos->z, $this->getServer()->getWorldManager()->getWorldByName($player->getWorld()->getFolderName())));

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

//        $this->getServer()->broadcastPackets($viewers, [$pk, $link]);
        foreach ($viewers as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($pk);
            $viewer->getNetworkSession()->sendDataPacket($link);
        }

        if ($this->isSitting($player)) {
            return;
        }

        $this->sittingData[strtolower($player->getName())] = [
            'eid' => $eid,
            'pos' => $pos
        ];
    }

    public function isToggleSit(Player $player): bool {
        return in_array(strtolower($player->getName()), $this->toggleSit, true);
    }

    public function unsetToggleSit(Player $player): void {
        unset($this->toggleSit[strtolower($player->getName())]);

        $player->sendMessage("&6You have enabled tap-on-block sit");
    }

    public function setToggleSit(Player $player): void {
        $this->toggleSit[] = strtolower($player->getName());

        $player->sendMessage("&6You have disabled tap-on-block sit!");
    }

    public function optimizeRotation(Player $player): void {
        $pk = new MoveActorAbsolutePacket();
        $pk->position = $this->sittingData[strtolower($player->getName())]['pos'];
        $pk->actorRuntimeId = $this->sittingData[strtolower($player->getName())]['eid'];
        $pk->pitch = $player->getLocation()->getPitch();
        $pk->yaw = $player->getLocation()->getYaw();
        $pk->headYaw = $player->getLocation()->getYaw();

//        $this->getServer()->broadcastPackets($this->getServer()->getOnlinePlayers(), [$pk]);
        foreach ($this->getServer()->getOnlinePlayers() as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($pk);
        }
    }
}
