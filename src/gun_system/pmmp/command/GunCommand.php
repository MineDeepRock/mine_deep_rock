<?php


namespace gun_system\pmmp\command;


use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\shotgun\M1897;
use gun_system\models\sniper_rifle\Gehenna;
use gun_system\models\sub_machine_gun\MP18;
use gun_system\pmmp\items\ItemAssaultRifle;
use gun_system\pmmp\items\ItemHandGun;
use gun_system\pmmp\items\ItemShotGun;
use gun_system\pmmp\items\ItemSubMachineGun;
use gun_system\pmmp\items\ItemSniperRifle;
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
            case "Mle1903":
                $player->getInventory()->setItemInHand(new ItemHandGun("DesertEagle", new Mle1903($this->scheduler)));
                break;

            //AssaultRifle
            case "M1907SL":
                $player->getInventory()->setItemInHand(new ItemAssaultRifle("M1907_SL", new M1907SL($this->scheduler)));
                break;

            //Shotgun
            case "M1897":
                $player->getInventory()->setItemInHand(new ItemShotGun("M1897", new M1897($this->scheduler)));
                break;

            //SniperRifle
            case "Gehenna":
                $player->getInventory()->setItemInHand(new ItemSniperRifle("Gehenna", new Gehenna($this->scheduler)));
                break;

            //SMG
            case "MP18":
                $player->getInventory()->setItemInHand(new ItemSubMachineGun("MP18", new MP18($this->scheduler)));
                break;

        }
        return true;
    }

}