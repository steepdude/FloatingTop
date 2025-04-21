<?php

namespace Tops;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class EventHandler implements Listener
{
    public function handleJoin(PlayerJoinEvent $event): void
    {
        Main::getInstance()->getParticleService()->spawnToAll($event->getPlayer());

        $json = json_encode([
            "format_version" => "1.16.0",
            "ui" => [
                "form1_main" => [
                    "type" => "panel",
                    "size" => [400, 300],
                    "controls" => [
                        [
                            "scroll_view" => [
                                "type" => "scroll_view",
                                "anchor_from" => "top_left",
                                "anchor_to" => "top_left",
                                "offset" => [20, 20],
                                "size" => [360, 240],
                                "controls" => [
                                    [
                                        "long_text" => [
                                            "type" => "label",
                                            "value" => "����� ������� �����, ������� ����� ��������������.  ����� ����� ���� ����� �������� ����������, ������� �������, ������� ����������, ������ ��������� ������ � �.�.  ���� ����� ������������� ����������� �� ����� ������, ���� �� ������� �������.  �� ������ ������������ �������� ����� (\\n) ��� ��������������� �������� ����� �������.\n\n��� ��� ���� �����.",
                                            "anchor_from" => "top_left",
                                            "anchor_to" => "top_left",
                                            "offset" => [0, 0],
                                            "size" => [340, "100%"],
                                            "color" => [0.9, 0.9, 0.9, 1]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $pk = new ModalFormRequestPacket();
        $pk->formId = 444444444444;
        $pk->formData = $json;
        $event->getPlayer()->getNetworkSession()->sendDataPacket($pk);
    }
}