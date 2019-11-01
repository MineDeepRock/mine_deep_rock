<?php

namespace discord_system\utils;

use pocketmine\Server;
use discord_system\tasks\DiscordAsyncTask;

class Discord
{

    private $name;
    private $webhook;

    public function __construct(string $webhook, string $name = "Server") {
        $this->name = $name;
        $this->webhook = $webhook;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function sendMessage(string $message): void {
        $curlopts = ['content' => $message, 'username' => $this->name];
        Server::getInstance()->getAsyncPool()->submitTask(new DiscordAsyncTask($this->webhook, serialize($curlopts)));
    }
}