<?php

namespace Tops\particles\tops;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use Tops\Main;
use Tops\page\PageFactory;
use Tops\page_buttons\BaseButton;
use Tops\particles\interface\ParticleInterface;
use Tops\particles\TextParticle;

abstract class BaseTopParticle implements ParticleInterface
{
    /** @var Position The position of the particle effect. */
    private Position $position;

    /** @var PageFactory The factory responsible for managing pages of text particles. */
    private PageFactory $factory;

    /** @var array<string> The list of strings to display as particles. */
    protected array $list = [];

    private string $index;

    private TextParticle $page;

    abstract public function setInfo(): void;

    /**
     * Constructs a new BaseTopParticle instance.
     *
     * @param array<string> $particleList The list of strings to display as particles.
     * @param Position $position The position where the particle effect will be displayed.
     */
    public function __construct(Position $position, string $index)
    {
        $this->position = $position;
        $this->factory = new PageFactory($this);
        $this->index = $index;
    }

    public function setPosition(Position $position): void
    {
        return;
    }

    /**
     * Spawns the navigation buttons around the particle effect.
     *
     * @return void
     */
    public function spawnButtons(Player $player): void
    {
        $world = $this->position->getWorld();
        $x = $this->position->getX();
        $y = $this->position->getY();
        $z = $this->position->getZ();

        $this->spawnTextParticle($player, $x, $y + 4.5, $z, Main::getInstance()->getConfigManager()->getTopConfig($this->getIndex())['title']);
        $this->spawnTextParticle($player, $x, $y + 5, $z, "§7(Обновляется каждую минуту)");

        $this->spawnButton($player, $x - 1, $y, $z, Main::getInstance()->getConfigManager()->getButtonPreviousPageMessage(), -1);
        $this->spawnButton($player, $x + 1, $y, $z, Main::getInstance()->getConfigManager()->getButtonNextPageMessage(), 1);
    }

    private function spawnTextParticle(Player $player, float $x, float $y, float $z, string $text): void
    {
        $world = $this->position->getWorld();
        $particle = new TextParticle($text, new Position($x, $y, $z, $world));

        $position = new Position($x, $y, $z, $world);
        foreach ($particle->encode($position) as $packet) {
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }

    private function spawnButton(Player $player, float $x, float $y, float $z, string $text, int $index): void
    {
        $world = $this->position->getWorld();

        $particle = new TextParticle($text, new Position($x, $y, $z, $world));

        $position = new Position($x, $y, $z, $world);
        foreach ($particle->encode($position) as $packet) {
            $player->getNetworkSession()->sendDataPacket($packet);
        }

        $button = new BaseButton(new Location($x, $y, $z, $world, 0, 0), null);
        $button->setTopParticle($this);
        $button->setIndex($index);

        $button->spawnTo($player);
    }

    /**
     * Sends the particle effect to a player.
     *
     * This method retrieves the current page for the player and sends each TextParticle
     * on that page to the player's client.
     *
     * @param Player $player The player to send the particle effect to.
     * @return void
     */
    public function toSpawn(Player $player): void
    {
        try {
            $pageExecutor = $this->getFactory()->getPlayer($player);

            if ($pageExecutor === null) {
                Server::getInstance()->getLogger()->error("PageExecutor not found for player: " . $player->getName());
                return;
            }

            $currentPageIndex = $pageExecutor->getPage();
            $currentPage = $this->getFactory()->getPage($currentPageIndex);

            $baseY = $this->position->getY() + 3;

            $pageFormat = Main::getInstance()->getConfigManager()->getPageFormat();
            $formatedLine = str_replace(['{page}', '{max.page}'], [$currentPageIndex + 1, count($this->getFactory()->getPages())], $pageFormat);

            if (!isset($this->page)) {
                $this->page = new TextParticle($formatedLine, new Position($this->getPosition()->getX(), $this->getPosition()->getY() + 4, $this->getPosition()->getZ(), $this->getPosition()->getWorld()));
            } else {
                $this->page->setInvisible(false);
                $this->page->setText($formatedLine);
            }

            foreach ($this->page->encode($this->page->getPosition()) as $packet) {
                $player->getNetworkSession()->sendDataPacket($packet);
            }


            /** @var TextParticle $page */
            foreach ($currentPage as $index => $page) {
                $newPosition = new Position($this->position->getX(), $baseY - (0.5 * $index), $this->position->getZ(), $this->position->getWorld());
                $page->setPosition($newPosition);
                $page->toSpawn($player);
            }
        } catch (\OutOfBoundsException $e) {
            $player->sendMessage(TextFormat::RED . "Page not found. Please try again.");
            Server::getInstance()->getLogger()->warning("Page not found for player " . $player->getName() . ": " . $e->getMessage());
        }
    }

    /**
     * Removes the particle effect for a specific player.
     *
     * @param Player $player The player to remove the particle effect from.
     * @return void
     */
    public function toRemove(Player $player): void
    {
        try {
            $pageExecutor = $this->getFactory()->getPlayer($player);

            if ($pageExecutor === null) {
                Server::getInstance()->getLogger()->error("PageExecutor not found for player: " . $player->getName());
                return;
            }

            $currentPageIndex = $pageExecutor->getPage();
            $currentPage = $this->getFactory()->getPage($currentPageIndex);

            if (isset($this->page)) {
                $this->page->setInvisible();
            }

            $baseY = $this->position->getY() + 3;

            foreach ($currentPage as $index => $page) {
                $newPosition = new Position($this->position->getX(), $baseY - (0.5 * $index), $this->position->getZ(), $this->position->getWorld());

                $page->toRemove($player, $newPosition);
            }
        } catch (\OutOfBoundsException $e) {
            $player->sendMessage(TextFormat::RED . "Page not found. Please try again.");
            Server::getInstance()->getLogger()->warning("Page not found for player " . $player->getName() . ": " . $e->getMessage());
        }
    }


    /**
     * Gets the position of the particle effect.
     *
     * @return Position The position of the particle effect.
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * Gets the list of strings to display as particles.
     *
     * @return array<string> The list of strings.
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Gets the factory responsible for managing pages of text particles.
     *
     * @return PageFactory The page factory.
     */
    public function getFactory(): PageFactory
    {
        return $this->factory;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        return $this->index;
    }

    /**
     * @param string[] $list
     */
    protected function setList(array $list): void
    {
        arsort($list);

        $format = Main::getInstance()->getConfigManager()->getLineFormat();
        $lines = [];
        $i = 1;

        foreach ($list as $username => $progress) {
            $escapedUsername = str_replace(['{', '}'], ['\{', '\}'], $username);

            $line = str_replace(
                ['{number}', '{username}', '{progress}'],
                [$i, $escapedUsername, $progress],
                $format
            );
            $lines[] = $line;
            $i++;
        }

        $this->list = $lines;
    }
}