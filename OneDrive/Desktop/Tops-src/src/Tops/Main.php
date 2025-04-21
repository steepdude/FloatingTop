<?php

namespace Tops;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

use Tops\config\ConfigManager;
use Tops\page_buttons\BaseButton;
use Tops\particles\TextParticle;
use Tops\particles\tops\DefaultTopParticle;
use Tops\particles\tops\KillerTopParticle;
use Tops\service\ParticleService;

class Main extends PluginBase
{
    private static self $instance;

    private ConfigManager $configManager;
    private ParticleService $particleService;

    /**
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function onEnable(): void
    {
        self::$instance = $this;

        $this->saveResource("config.yml");

        $this->configManager = new ConfigManager($this);
        $this->particleService = new ParticleService();

        foreach ($this->configManager->getConfig()->get("top") as $index => $topData) {
            $world = $this->getServer()->getWorldManager()->getWorldByName($topData['world']);

            switch ($index) {
                case "TestTop":
                    $this->particleService->addParticle(new DefaultTopParticle(Location::fromObject(new Vector3($topData['x'], $topData['y'], $topData['z']), $world), $index));
                    break;
                case "KillerTop":
                    $this->particleService->addParticle(new KillerTopParticle(Location::fromObject(new Vector3($topData['x'], $topData['y'], $topData['z']), $world), $index));
            }
        }

        if ($this->getConfigManager()->getConfig()->get("particle")) {
            foreach ($this->getConfigManager()->getConfig()->get("particle") as $index => $particleData) {
                $world = $this->getServer()->getWorldManager()->getWorldByName($particleData['world']);
                $this->getParticleService()->addParticle(new TextParticle($particleData['title'], Location::fromObject(new Vector3($particleData['x'], $particleData['y'], $particleData['z']), $world)));
            }
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);

        $this->registerEntity();
    }

    private function registerEntity(): void
    {
        EntityFactory::getInstance()->register(BaseButton::class, function(World $world, CompoundTag $nbt): BaseButton{
            return new BaseButton(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['BaseButton']);
    }

    /**
     * @return ConfigManager
     */
    public function getConfigManager(): ConfigManager
    {
        return $this->configManager;
    }

    /**
     * @return ParticleService
     */
    public function getParticleService(): ParticleService
    {
        return $this->particleService;
    }
}