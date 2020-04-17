<?php


namespace gun_system\pmmp\command;


use gun_system\models\assault_rifle\M1Garand;
use gun_system\models\hand_gun\DesertEagle;
use gun_system\models\hand_gun\M1911;
use gun_system\models\hand_gun\P08;
use gun_system\models\shotgun\M1897;
use gun_system\pmmp\items\ItemGun;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\TaskScheduler;

class GunCommand extends Command
{

    private $scheduler;

    public function __construct(Plugin $owner, TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
        parent::__construct("gun", "", "");
        $this->setPermission("Gun.Command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/gun [args]");
            return true;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        $method = $args[0];
        switch ($method) {
            //Handgun
            case "DesertEagle":
                $player->getInventory()->setItemInHand(new ItemGun("DesertEagle", new DesertEagle($this->scheduler)));
                break;
            case "P08":
                $player->getInventory()->setItemInHand(new ItemGun("P08", new P08($this->scheduler)));
                break;
            case "M1911":
                $player->getInventory()->setItemInHand(new ItemGun("M1911", new M1911($this->scheduler)));
                break;

            //AssaultRifle
            case "M1Garand":
                $player->getInventory()->setItemInHand(new ItemGun("M1Garand", new M1Garand($this->scheduler)));
                break;

            //Shotgun
            case "M1897":
                $player->getInventory()->setItemInHand(new ItemGun("M1897", new M1897($this->scheduler)));
                break;
        }

        return true;
    }

}