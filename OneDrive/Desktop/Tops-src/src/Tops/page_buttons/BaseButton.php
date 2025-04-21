<?php

namespace Tops\page_buttons;

use pocketmine\entity\Location;
use pocketmine\entity\Villager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use Tops\Main;
use Tops\particles\tops\BaseTopParticle;

class BaseButton extends Villager
{
    private BaseTopParticle $topParticle;
    private int $index;

    private array $lastClickTime = []; // Store last click time for each player

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
    }

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);

        $this->setScale(0.5);
        $this->setInvisible();
    }

    public function hasMovementUpdate(): bool
    {
        return false;
    }

    public function attack(EntityDamageEvent $source): void
    {
        $damager = $source->getDamager();

        if ($damager instanceof Player) {
            $playerName = $damager->getName();
            $currentTime = microtime(true);

            if (isset($this->lastClickTime[$playerName]) && ($currentTime - $this->lastClickTime[$playerName] < Main::getInstance()->getConfigManager()->getSwitchPageCooldown())) {
                $source->cancel();
                return;
            }

            $this->lastClickTime[$playerName] = $currentTime;
            $this->onAction($damager);
        }
    }

    public function despawnFrom(Player $player, bool $send = true): void
    {
        parent::despawnFrom($player, $send);
    }

    public function onAction(Player $player): void
    {
        $pageExecutor = $this->topParticle->getFactory()->getPlayer($player);

        $this->topParticle->toRemove($player);

        if ($this->index > 0) {
            $pageExecutor->nextPage();
        } else {
            $pageExecutor->previousPage();
        }

        $this->topParticle->toSpawn($player);
    }

    public function setTopParticle(BaseTopParticle $topParticle): void
    {
        $this->topParticle = $topParticle;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }
}