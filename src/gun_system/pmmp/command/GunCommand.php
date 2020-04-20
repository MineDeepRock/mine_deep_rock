<?php


namespace gun_system\pmmp\command;


use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\light_machine_gun\LewisGun;
use gun_system\models\light_machine_gun\ParabellumMG14;
use gun_system\models\shotgun\M1897;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sub_machine_gun\Automatico;
use gun_system\models\sub_machine_gun\MP18;
use gun_system\pmmp\items\ItemAssaultRifle;
use gun_system\pmmp\items\ItemHandGun;
use gun_system\pmmp\items\ItemLightMachineGun;
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
                $item = new ItemHandGun("Mle1903", new Mle1903($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            //AssaultRifle
            case "M1907SL":
                $item = new ItemAssaultRifle("M1907SL", new M1907SL($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            //Shotgun
            case "M1897":
                $item = new ItemShotGun("M1897", new M1897($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            //SniperRifle
            case "SMLEMK3":
                $item = new ItemSniperRifle("SMLEMK3", new SMLEMK3($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            //SMG
            case "MP18":
                $item = new ItemSubMachineGun("MP18", new MP18($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            case "Automatico":
                $item = new ItemSubMachineGun("Automatico", new Automatico($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            //LMG
            case "LewisGun":
                $item = new ItemLightMachineGun("LewisGun", new LewisGun($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;

            case "ParabellumMG14":
                $item = new ItemLightMachineGun("ParabellumMG14", new ParabellumMG14($this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($item);
                break;
        }
        return true;
    }

}