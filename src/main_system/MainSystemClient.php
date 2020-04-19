<?php


namespace main_system;


use Client;

class MainSystemClient extends Client
{
    /** @var array */
    private $worldsName;

    public const DEFAULT = 0;
    public const FLAT = 1;

    public function teleport(Player $player, string $name, float $addX = 0.0, float $addY = 0.0, float $addZ = 0.0): void {
        if(!$this->loadWorld($name)) {
            echo "not found world data: {$name}".PHP_EOL;
            return;
        }

        $level = $this->getWorld($name);
        $pos = $level->getSpawnLocation();
        $pos->add($addX, $addY, $addZ);
        $player->teleport($pos);
    }

    private function update(): void{
        $array = array_diff(scandir("./worlds"), [".", ".."]);
        rsort($array);
        $this->worldsName = $array;
    }

    public function loadWorld(string $name): bool {
        return Server::getInstance()->loadLevel($name);
    }

    public function getWorld(string $name): ?Level{
        return Server::getInstance()->getLevelByName($name);
    }

    public function getAllWorldName(): array{
        $this->update();
        return $this->worldsName;
    }

    public function getAllPlayer(): array {
        return Server::getInstance()->getOnlinePlayers();
    }

    public function getAllPlayerName(): array {
        $result = [];
        foreach($this->getAllPlayer() as $player) {
            $result[] = $player->getName();
        }
        return $result;
    }

    private function toStringType(int $type): string {
        switch($type) {
            case self::FLAT: return 'flat';
            case self::DEFAULT: return 'default';
            default: return '';
        }
    }

    public function generate(int $type, string $name, array $options = []): bool {
        if($this->loadWorld($name)) return false;
        return Server::getInstance()->generateLevel($name, time(), GeneratorManager::getGenerator($this->toStringType($type)), $options);
    }
}