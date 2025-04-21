<?php

namespace Tops\particles\tops;

use pocketmine\world\Position;

class KillerTopParticle extends BaseTopParticle
{
    public function __construct(Position $position, string $index)
    {
        $this->setInfo();
        parent::__construct($position, $index);
    }

    public function setInfo(): void {
        $list = [
            'nad543' => rand(0, 100000),
            'nadziratelunb2' => 99,
            'nad435elunb3' => 88,
            'nadziratelun4' => 77,
            'na543nb5' => 66,
            'n34elunb6' => 55,
            'n343elunb7' => 44,
            'nadziratelunb8' => 33,
            'nadziratelunb9' => 22,
            'nadzi543lunb88' => 11,
            'nadziratelunb99' => 9,
            'nad543lunb56' => 1,
        ];

        $this->setList($list);
    }

    public function setList(array $list): void
    {
        parent::setList($list);
    }
}