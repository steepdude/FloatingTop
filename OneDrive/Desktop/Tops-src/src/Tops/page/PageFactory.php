<?php

namespace Tops\page;

use pocketmine\player\Player;
use pocketmine\world\Position;
use Tops\Main;
use Tops\particles\TextParticle;
use Tops\particles\tops\BaseTopParticle;
use pocketmine\Server;

class PageFactory
{
    /** @var TextParticle[] - Все частицы, без разбивки на страницы*/
    private array $particles = [];

    /** @var array<string, PageExecutor> */
    private array $players = [];

    private BaseTopParticle $particle;

    public function __construct(BaseTopParticle $particle)
    {
        $this->particle = $particle;
        $this->generatePages();
    }

    public function addPlayer(Player $player): void
    {
        if (isset($this->players[$player->getName()])) {
            return;
        }

        $this->players[$player->getName()] = new PageExecutor($this);
    }

    public function getPlayer(Player $player): ?PageExecutor
    {
        $playerName = $player->getName();

        if (!isset($this->players[$playerName])) {
            $this->addPlayer($player);
        }

        return $this->players[$playerName] ?? null;
    }

    public function getPages(): array {
        $maxLinesPerPage = Main::getInstance()->getConfigManager()->getMaxLinesForPage();
        return array_chunk($this->particles, $maxLinesPerPage);
    }

    /**
     * Generates and returns the correct page of text particles based on index
     * @param int $index
     * @return array<int, TextParticle>
     */
    public function getPage(int $index): array
    {
        $maxLinesPerPage = Main::getInstance()->getConfigManager()->getMaxLinesForPage();
        $this->generatePages();

        $allPages = array_chunk($this->particles, $maxLinesPerPage);

        if (!isset($allPages[$index])) {
            return [];
        }

        return $allPages[$index];
    }

    /**
     * Generates (or updates) the text particles.
     * This method now *always* regenerates the particles to ensure they're up-to-date.
     */
    private function generatePages(): void
    {
        $data = $this->getParticle()->getList();
        $position = $this->getParticle()->getPosition();
        $x = $position->getX();
        $z = $position->getZ();
        $world = $position->getWorld();

        if ($world === null) {
            Server::getInstance()->getLogger()->error("World is null for BaseTopParticle at position: " . $position->getX() . ", " . $position->getY() . ", " . $position->getZ());
            return;
        }

        $numDataItems = count($data);
        $numParticles = count($this->particles);

        // Update existing particles
        for ($i = 0; $i < min($numDataItems, $numParticles); $i++) {
            $this->particles[$i]->setText($data[$i]);
        }

        // Create new particles if needed
        if ($numDataItems > $numParticles) {
            for ($i = $numParticles; $i < $numDataItems; $i++) {
                try {
                    $this->particles[] = new TextParticle($data[$i], new Position($x, $position->getY(), $z, $world)); // Use the base Y position
                } catch (\Exception $e) {
                    Server::getInstance()->getLogger()->logException($e);
                }
            }
        }
        if ($numDataItems < $numParticles) {
            for ($i = $numDataItems; $i < $numParticles; $i++) {
                unset($this->particles[$i]);
            }
            $this->particles = array_values($this->particles); // Re-index array
        }
    }

    public function getParticle(): BaseTopParticle {
        return $this->particle;
    }
}