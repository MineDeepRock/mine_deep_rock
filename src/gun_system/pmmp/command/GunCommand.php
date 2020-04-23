<?php


namespace gun_system\pmmp\command;


use gun_system\models\assault_rifle\attachiment\scope\EightFoldScopeForAR;
use gun_system\models\assault_rifle\attachiment\scope\FourFoldScopeForAR;
use gun_system\models\assault_rifle\attachiment\scope\IronSightForAR;
use gun_system\models\assault_rifle\attachiment\scope\TwelveFoldScopeForAR;
use gun_system\models\assault_rifle\attachiment\scope\TwoFoldScopeForAR;
use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\hand_gun\attachment\scope\FourFoldScopeForHG;
use gun_system\models\hand_gun\attachment\scope\IronSightForHG;
use gun_system\models\hand_gun\attachment\scope\TwoFoldScopeForHG;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\light_machine_gun\attachment\scope\EightFoldScopeForLMG;
use gun_system\models\light_machine_gun\attachment\scope\FourFoldScopeForLMG;
use gun_system\models\light_machine_gun\attachment\scope\IronSightForLMG;
use gun_system\models\light_machine_gun\attachment\scope\TwoFoldScopeForLMG;
use gun_system\models\light_machine_gun\LewisGun;
use gun_system\models\light_machine_gun\ParabellumMG14;
use gun_system\models\shotgun\attachment\scope\IronSightForSG;
use gun_system\models\shotgun\M1897;
use gun_system\models\sniper_rifle\attachment\scope\IronSightForSR;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sub_machine_gun\attachment\scope\IronSightForSMG;
use gun_system\models\sub_machine_gun\Automatico;
use gun_system\models\sub_machine_gun\MP18;
use gun_system\pmmp\items\ItemAssaultRifle;
use gun_system\pmmp\items\ItemGun;
use gun_system\pmmp\items\ItemHandGun;
use gun_system\pmmp\items\ItemLightMachineGun;
use gun_system\pmmp\items\ItemShotGun;
use gun_system\pmmp\items\ItemSubMachineGun;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;

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
        if ($method === "give") {
            if (count($args) < 2) {
                $sender->sendMessage("/gun give [name] [bullet:onlyShotgun]");
                return true;
            }
            $bulletName = count($args) === 3 ? $args[1] : null;
            $this->give($player, $args[1],$bulletName);
        } else if ($method === "attachment") {
            if (count($args) < 2) {
                $sender->sendMessage("/gun attachment [name]");
                return true;
            }
            $this->attachment($player,$args[1]);
        }

        return true;
    }

    //TODO:リファクタ
    public function attachment(Player $player, string $name) {
        $gun = $player->getInventory()->getItemInHand();
        if ($gun instanceof ItemAssaultRifle) {
            switch ($name){
                case "IronSight":
                    $gun->setScope(new IronSightForAR());
                    break;
                case "2xScope":
                    $gun->setScope(new TwoFoldScopeForAR());
                    break;
                case "4xScope":
                    $gun->setScope(new FourFoldScopeForAR());
                    break;
                case "8xScope":
                    $gun->setScope(new EightFoldScopeForAR());
                    break;
                case "12xScope":
                    $gun->setScope(new TwelveFoldScopeForAR());
                    break;
            }
        } else if ($gun instanceof ItemHandGun){
            switch ($name){
                case "IronSight":
                    $gun->setScope(new IronSightForHG());
                    break;
                case "2xScope":
                    $gun->setScope(new TwoFoldScopeForHG());
                    break;
                case "4xScope":
                    $gun->setScope(new FourFoldScopeForHG());
                    break;
            }
        } else if ($gun instanceof ItemLightMachineGun){
            switch ($name){
                case "IronSight":
                    $gun->setScope(new IronSightForLMG());
                    break;
                case "2xScope":
                    $gun->setScope(new TwoFoldScopeForLMG());
                    break;
                case "4xScope":
                    $gun->setScope(new FourFoldScopeForLMG());
                    break;
                case "8xScope":
                    $gun->setScope(new EightFoldScopeForLMG());
                    break;
            }
        } else if ($gun instanceof ItemShotGun){
            switch ($name){
                case "IronSight":
                    $gun->setScope(new IronSightForSG());
                    break;
            }
        } else if ($gun instanceof ItemSniperRifle){
            switch ($name){
                case "IronSight":
                    $gun->setScope(new IronSightForSR());
                    break;
            }
        } else if ($gun instanceof ItemSubMachineGun){
            switch ($name){
                case "IronSight":
                    $gun->setScope(new IronSightForSMG());
                    break;
            }
        }
    }

    public function give(Player $player, string $name, string $bulletName = null) {
        switch ($name) {
            //Handgun
            case "Mle1903":
                $item = new ItemHandGun("Mle1903", new Mle1903($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            //AssaultRifle
            case "M1907SL":
                $item = new ItemAssaultRifle("M1907SL", new M1907SL($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            //Shotgun
            case "M1897":
                $bulletType = $bulletName === null ? ShotgunBulletType::Buckshot() : ShotgunBulletType::fromString($bulletName);
                $item = new ItemShotGun("M1897", new M1897($bulletType, $this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            //SniperRifle
            case "SMLEMK3":
                $item = new ItemSniperRifle("SMLEMK3", new SMLEMK3($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            //SMG
            case "MP18":
                $item = new ItemSubMachineGun("MP18", new MP18($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            case "Automatico":
                $item = new ItemSubMachineGun("Automatico", new Automatico($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            //LMG
            case "LewisGun":
                $item = new ItemLightMachineGun("LewisGun", new LewisGun($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;

            case "ParabellumMG14":
                $item = new ItemLightMachineGun("ParabellumMG14", new ParabellumMG14($this->scheduler), $player);
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                break;
        }
    }

    public function setItemDescription(ItemGun $item): ItemGun {
        $gun = $item->getGunData();
        $bulletDamage = $gun->getBulletDamage();
        $effectiveRange = $gun->getEffectiveRange();
        $rate = $gun->getRate();
        return $item->setLore([
            TextFormat::RESET . "火力" . TextFormat::GRAY . $bulletDamage->getMaxDamage() . "-" . $bulletDamage->getMinDamage(),
            TextFormat::RESET . "有効射程" . TextFormat::GRAY . $effectiveRange->getStart() . "-" . $effectiveRange->getEnd(),
            TextFormat::RESET . "レート" . TextFormat::GRAY . $rate->getPerSecond(),
            TextFormat::RESET . "マガジンキャパ" . TextFormat::GRAY . $gun->getBulletCapacity(),
            TextFormat::RESET . "反動" . TextFormat::GRAY . $gun->getReaction(),
            TextFormat::RESET . "リロード時間" . TextFormat::GRAY . $gun->getReloadDuration()->getSecond(),
            TextFormat::RESET . "精度" . TextFormat::GRAY . "ADS:" . $gun->getPrecision()->getADS()."腰撃ち:".$gun->getPrecision()->getHipShooting(),
        ]);
    }
}