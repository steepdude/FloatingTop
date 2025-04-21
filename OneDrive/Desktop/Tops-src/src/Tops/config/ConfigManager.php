<?php

namespace Tops\config;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\Config;
use Tops\Main;

class ConfigManager implements PluginOwned
{
    private Plugin $plugin;
    private Config $config;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->config = new Config($this->plugin->getResourceFolder() . "config.yml", Config::YAML);
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getMaxLinesForPage(): int
    {
        return $this->config->get("max.line.for.page", 5);
    }

    public function getSwitchPageCooldown(): int|float
    {
        return $this->config->get("swith.page.cooldown", 1);
    }

    public function getButtonNextPageMessage(): string
    {
        return $this->config->getNested("message.button.next.page", "§7> §eСледующая §7<");
    }

    public function getButtonPreviousPageMessage(): string
    {
        return $this->config->getNested("message.button.previous.page", "§7> §eПредыдущая §7<");
    }

    public function getPageFormat(): string
    {
        return $this->config->get("page_format", "§eСтраница§7(§a{page}§7/§a{max.page}§7)");
    }

    public function getLineFormat(): string
    {
        return $this->config->get("line_format", "§7#{number} §a{username} §7- §a{progress}");
    }

    /**
     * @return array<string, mixed>
     */
    public function getTopConfig(string $topName): array
    {
        return $this->config->getNested("top." . $topName, []);
    }

    public function reloadConfig(): void
    {
        $this->config->reload();
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }
}