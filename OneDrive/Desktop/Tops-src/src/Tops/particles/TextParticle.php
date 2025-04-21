<?php

namespace Tops\particles;

use pocketmine\world\Position;

class TextParticle extends BaseParticle
{
    public function __construct(string $text, Position $position)
    {
        parent::__construct($text, $position);
    }

    public function getIndex(): string
    {
        return "text-paticle_" . rand(0, 999999);
    }
}