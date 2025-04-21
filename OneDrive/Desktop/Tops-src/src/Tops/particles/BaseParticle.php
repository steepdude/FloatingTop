<?php

namespace Tops\particles;

use pocketmine\player\Player;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use Tops\particles\interface\ParticleInterface;

abstract class BaseParticle extends FloatingTextParticle implements ParticleInterface
{
    private Position $position;

    public function __construct(string $text, Position $position)
    {
        $this->text = $text;
        $this->position = $position;

        parent::__construct($text);
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    public function toSpawn(Player $player): void
    {
        $this->setInvisible(false);
        $packets = $this->encode($this->getPosition());

        foreach ($packets as $packet) {
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }

    public function toRemove(Player $player, Position $position): void
    {
        $this->setInvisible(true);
        $packets = $this->encode($position);

        foreach ($packets as $packet) {
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }
}