<?php


namespace game_system\pmmp\client;


use game_system\pmmp\Entity\FlagEntity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\TextFormat;

class TeamDominationClient extends TwoTeamGameClient
{
    private $flags = [];

    public function __construct() {
        parent::__construct("TeamDomination");
    }

    public function spawnFlag(string $name, Level $level, Vector3 $pos) {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $pos->getX()),
                new DoubleTag('', $pos->getY() + 0.5),
                new DoubleTag('', $pos->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ]),
        ]);

        $flag = new FlagEntity($level, $nbt);
        $flag->teleport($pos);
        $flag->setNameTag(TextFormat::WHITE . $name);
        $flag->setNameTagAlwaysVisible(true);
        $this->flags[$name] = $flag;
        $flag->spawnToAll();
    }

    public function changeColorWhite(string $name): void {
        $this->flags[$name]->setNameTag(TextFormat::WHITE . $name);
        $this->flags[$name]->changeColorWhite();
    }

    public function changeColorRed(string $name): void {
        $this->flags[$name]->setNameTag(TextFormat::RED . $name);
        $this->flags[$name]->changeColorRed();
    }

    public function changeColorBlue(string $name): void {
        $this->flags[$name]->setNameTag(TextFormat::BLUE . $name);
        $this->flags[$name]->changeColorBlue();
    }

    public function removeAllFlags() {
        $this->flags = [];
    }
}