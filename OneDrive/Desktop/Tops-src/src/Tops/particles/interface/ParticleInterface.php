<?php

namespace Tops\particles\interface;

use pocketmine\player\Player;
use pocketmine\world\Position;

interface ParticleInterface
{
    public function toSpawn(Player $player): void;
    public function setPosition(Position $position): void;
    public function getIndex(): string;
}