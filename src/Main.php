<?php
declare(strict_types=1);

namespace DavyCraft648\MCFurniture;

use customiesdevs\customies\block\{CustomiesBlockFactory, Material, Model};
use customiesdevs\customies\item\CreativeInventoryInfo;
use DavyCraft648\MCFurniture\block\{BarStool, Basin, Bath, BedsideCabinet, Bin, Blinds, Cabinet, Candle, CeilingLight,
	Chair, Chimney, ChristmasTree, DigitalClock, FairyLight, Lamp, StoneCabinet, Table, WallCabinet};
use pocketmine\block\{BlockBreakInfo, BlockIdentifier, BlockTypeInfo};
use pocketmine\math\Vector3;
use pocketmine\resourcepacks\ZippedResourcePack;
use Symfony\Component\Filesystem\Path;
use function array_merge;
use function str_replace;

class Main extends \pocketmine\plugin\PluginBase{

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
			static fn(int $id) => new Basin(new BlockIdentifier($id), "Basin", new BlockTypeInfo(new BlockBreakInfo(2))),
			"mcfurniture:basin",
			new Model([new Material(Material::TARGET_ALL, "black_kitchen_counter_sink", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.basin", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE),
		);

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new Bath(new BlockIdentifier($id), "Bath", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:bath",
			new Model([new Material(Material::TARGET_ALL, "black_kitchen_counter_sink", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.bath", new Vector3(-8, 0, -8), new Vector3(16, 12, 16)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE),
		);

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
				new Model([new Material(Material::TARGET_ALL, "bedside_cabinet_$variant", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.bedside_cabinet", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
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

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new Chair(new BlockIdentifier($id), "Toilet", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:toilet",
			new Model([new Material(Material::TARGET_ALL, "black_kitchen_counter_sink", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.toilet", new Vector3(-8, 0, -8), new Vector3(16, 12, 16)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
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

			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new StoneCabinet(new BlockIdentifier($id), "Terracotta Cabinet", new BlockTypeInfo(new BlockBreakInfo(2))),
				"mcfurniture:{$variant}_terracotta_cabinet",
				new Model([new Material(Material::TARGET_ALL, str_replace(["lime", "purple"], ["light_green", "purpure"], "{$variant}_terracotta_cabinet"), Material::RENDER_METHOD_ALPHA_TEST)], "geometry.bedside_cabinet", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
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
				static fn(int $id) => new Cabinet(new BlockIdentifier($id), "Wooden Cabinet", new BlockTypeInfo(new BlockBreakInfo(2))),
				"mcfurniture:{$variant}_cabinet",
				new Model([new Material(Material::TARGET_ALL, $variant === "dark_oak" ? "darkoak_cabinet" : "{$variant}_cabinet", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.cabinet", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);

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

		foreach(["andesite", "diorite", "granite", "stone"] as $variant){
			CustomiesBlockFactory::getInstance()->registerBlock(
				static fn(int $id) => new StoneCabinet(new BlockIdentifier($id), "Stone Cabinet", new BlockTypeInfo(new BlockBreakInfo(2))),
				"mcfurniture:{$variant}_cabinet",
				new Model([new Material(Material::TARGET_ALL, "{$variant}_cabinet", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.stonecabinet", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
				new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
			);
		}

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new WallCabinet(new BlockIdentifier($id), "Wall Cabinet", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:wall_cabinet",
			new Model([new Material(Material::TARGET_ALL, "wall_cabinet", Material::RENDER_METHOD_ALPHA_TEST)], "geometry.wall_cabinet", new Vector3(-8, 0, -8), new Vector3(16, 16, 4)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
		);

		CustomiesBlockFactory::getInstance()->registerBlock(
			static fn(int $id) => new ChristmasTree(new BlockIdentifier($id), "Christmas Tree", new BlockTypeInfo(new BlockBreakInfo(0.3))),
			"mcfurniture:christmas_tree",
			new Model([new Material(Material::TARGET_ALL, "christmas_tree", Material::RENDER_METHOD_BLEND)], "geometry.christmas_tree", new Vector3(-8, 0, -8), new Vector3(16, 16, 16)),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE)
		);
	}
}
