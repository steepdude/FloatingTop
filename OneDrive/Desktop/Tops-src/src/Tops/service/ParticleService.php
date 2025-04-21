<?php

namespace Tops\service;

use pocketmine\player\Player;
use Tops\Main;
use Tops\particles\DynamicParticle;
use Tops\particles\interface\ParticleInterface;
use Tops\particles\tops\BaseTopParticle;
use Tops\task\ParticleUpdateTask;

class ParticleService
{
    private array $particle = [];

    public function __construct()
    {
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ParticleUpdateTask($this), Main::getInstance()->getConfigManager()->getConfig()->get("update.task"));
    }

    public function addParticle(ParticleInterface $particle): void {
        if (isset($this->particle[$particle->getIndex()])) {
            return;
        }

        $this->particle[$particle->getIndex()] = $particle;
    }

    public function spawnToAll(Player $player): void
    {

        /**
         * @var string $index
         * @var ParticleInterface $particle
         */
        foreach ($this->particle as $index => $particle) {

            if ($particle instanceof BaseTopParticle) {
                $particle->spawnButtons($player);
            }

            $particle->toSpawn($player);
        }
    }

    public function updateTo(Player $player): void
    {
        /**
         * @var string $index
         * @var ParticleInterface $particle
         */
        foreach ($this->particle as $index => $particle) {
            $particle->toSpawn($player);
        }

        if ($particle instanceof DynamicParticle) {
            $particle->update($player);
        }
    }

    public function update(): void {
        foreach ($this->particle as $particle) {
            if ($particle instanceof BaseTopParticle) {
                $particle->setInfo();
            }
        }
    }

    public function getParticles(): array {
        return $this->particle;
    }
}