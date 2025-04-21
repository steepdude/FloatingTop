<?php

namespace Tops\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Tops\service\ParticleService;

class ParticleUpdateTask extends Task
{
    private ParticleService $particleService;

    public function __construct(ParticleService $particleService)
    {
        $this->particleService = $particleService;
    }

    public function onRun(): void
    {
        $this->particleService->update();

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->particleService->updateTo($player);
        }
    }
}