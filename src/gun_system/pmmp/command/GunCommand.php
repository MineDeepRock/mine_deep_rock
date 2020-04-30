<?php


namespace gun_system\pmmp\command;


use gun_system\interpreter\AssaultRifleInterpreter;
use gun_system\interpreter\HandGunInterpreter;
use gun_system\interpreter\LightMachineGunInterpreter;
use gun_system\interpreter\RevolverInterpreter;
use gun_system\interpreter\ShotgunInterpreter;
use gun_system\interpreter\SniperRifleInterpreter;
use gun_system\interpreter\SubMachineGunInterpreter;
use gun_system\models\assault_rifle\attachiment\scope\FourFoldScopeForAR;
use gun_system\models\assault_rifle\attachiment\scope\IronSightForAR;
use gun_system\models\assault_rifle\attachiment\scope\TwoFoldScopeForAR;
use gun_system\models\assault_rifle\CeiRigotti;
use gun_system\models\assault_rifle\FedorovAvtomat;
use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\assault_rifle\Ribeyrolles;
use gun_system\models\attachment\bullet\Bullet;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletId;
use gun_system\models\GunList;
use gun_system\models\GunType;
use gun_system\models\hand_gun\attachment\scope\FourFoldScopeForHG;
use gun_system\models\hand_gun\attachment\scope\IronSightForHG;
use gun_system\models\hand_gun\attachment\scope\TwoFoldScopeForHG;
use gun_system\models\hand_gun\C96;
use gun_system\models\hand_gun\HowdahPistol;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\hand_gun\P08;
use gun_system\models\light_machine_gun\attachment\scope\FourFoldScopeForLMG;
use gun_system\models\light_machine_gun\attachment\scope\IronSightForLMG;
use gun_system\models\light_machine_gun\attachment\scope\TwoFoldScopeForLMG;
use gun_system\models\light_machine_gun\BAR1918;
use gun_system\models\light_machine_gun\LewisGun;
use gun_system\models\light_machine_gun\MG15;
use gun_system\models\light_machine_gun\ParabellumMG14;
use gun_system\models\revolver\NagantRevolver;
use gun_system\models\revolver\No3Revolver;
use gun_system\models\revolver\ColtSAA;
use gun_system\models\revolver\RevolverMk6;
use gun_system\models\shotgun\attachment\scope\IronSightForSG;
use gun_system\models\shotgun\Automatic12G;
use gun_system\models\shotgun\M1897;
use gun_system\models\shotgun\Model10A;
use gun_system\models\shotgun\Model1900;
use gun_system\models\sniper_rifle\attachment\scope\FourFoldScopeForSR;
use gun_system\models\sniper_rifle\attachment\scope\IronSightForSR;
use gun_system\models\sniper_rifle\attachment\scope\TwoFoldScopeForSR;
use gun_system\models\sniper_rifle\Gewehr98;
use gun_system\models\sniper_rifle\MartiniHenry;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sniper_rifle\Type38Arisaka;
use gun_system\models\sub_machine_gun\attachment\scope\FourFoldScopeForSMG;
use gun_system\models\sub_machine_gun\attachment\scope\IronSightForSMG;
use gun_system\models\sub_machine_gun\attachment\scope\TwoFoldScopeForSMG;
use gun_system\models\sub_machine_gun\Automatico;
use gun_system\models\sub_machine_gun\FrommerStopAuto;
use gun_system\models\sub_machine_gun\Hellriegel1915;
use gun_system\models\sub_machine_gun\MP18;
use gun_system\pmmp\items\bullet\ItemAssaultRifleBullet;
use gun_system\pmmp\items\ItemAssaultRifle;
use gun_system\pmmp\items\ItemGun;
use gun_system\pmmp\items\ItemHandGun;
use gun_system\pmmp\items\ItemLightMachineGun;
use gun_system\pmmp\items\ItemRevolver;
use gun_system\pmmp\items\ItemShotGun;
use gun_system\pmmp\items\ItemSubMachineGun;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GunCommand extends Command
{

    private $scheduler;
    private $server;
    
    private $gunList;

    public function __construct(Plugin $owner, TaskScheduler $scheduler, Server $server) {
        $this->scheduler = $scheduler;
        parent::__construct("gun", "", "");
        $this->setPermission("Gun.Command");
        $this->server = $server;
        $this->gunList = new GunList();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/gun [args]");
            return true;
        }
        $method = $args[0];
        if ($method === "give") {
            if (count($args) < 3) {
                $sender->sendMessage("/gun give [playerName] [name]");
                return true;
            }

            $player = $sender->getServer()->getPlayer($args[1]);

            $this->give($player, $args[2]);
        } else if ($method === "attachment") {
            if (count($args) < 3) {
                $sender->sendMessage("/gun attachment [playerName] [name]");
                return true;
            }
            $player = $sender->getServer()->getPlayer($args[1]);

            $this->attachment($player, $args[2]);
        }
        return true;
    }

    public function attachment(Player $player, string $name) {
        $gun = null;
        foreach ($player->getInventory()->getContents() as $item) {
            if (is_subclass_of($item,"gun_system\pmmp\items\ItemGun")) {
                $gun = $item;
            }
        }

        if ($gun instanceof ItemAssaultRifle) {
            switch ($name) {
                case "IronSight":
                    $gun->getInterpreter()->setScope(new IronSightForAR());
                    break;
                case "2xScope":
                    $gun->getInterpreter()->setScope(new TwoFoldScopeForAR());
                    break;
                case "4xScope":
                    $gun->getInterpreter()->setScope(new FourFoldScopeForAR());
                    break;
            }
        } else if ($gun instanceof ItemHandGun) {
            switch ($name) {
                case "IronSight":
                    $gun->getInterpreter()->setScope(new IronSightForHG());
                    break;
                case "2xScope":
                    $gun->getInterpreter()->setScope(new TwoFoldScopeForHG());
                    break;
                case "4xScope":
                    $gun->getInterpreter()->setScope(new FourFoldScopeForHG());
                    break;
            }
        } else if ($gun instanceof ItemLightMachineGun) {
            switch ($name) {
                case "IronSight":
                    $gun->getInterpreter()->setScope(new IronSightForLMG());
                    break;
                case "2xScope":
                    $gun->getInterpreter()->setScope(new TwoFoldScopeForLMG());
                    break;
                case "4xScope":
                    $gun->getInterpreter()->setScope(new FourFoldScopeForLMG());
                    break;
            }
        } else if ($gun instanceof ItemShotGun) {
            switch ($name) {
                case "IronSight":
                    $gun->getInterpreter()->setScope(new IronSightForSG());
                    break;
            }
        } else if ($gun instanceof ItemSniperRifle) {
            switch ($name) {
                case "IronSight":
                    $gun->getInterpreter()->setScope(new IronSightForSR());
                    break;
                case "2xScope":
                    $gun->getInterpreter()->setScope(new TwoFoldScopeForSR());
                    break;
                case "4xScope":
                    $gun->getInterpreter()->setScope(new FourFoldScopeForSR());
                    break;
            }
        } else if ($gun instanceof ItemSubMachineGun) {
            switch ($name) {
                case "IronSight":
                    $gun->getInterpreter()->setScope(new IronSightForSMG());
                    break;
                case "2xScope":
                    $gun->getInterpreter()->setScope(new TwoFoldScopeForSMG());
                    break;
                case "4xScope":
                    $gun->getInterpreter()->setScope(new FourFoldScopeForSMG());
                    break;
            }
        }
    }

    //TODO:GunListをつかってリファクタリング
    public function give(Player $player, string $name, string $bulletName = null) {
        $gun = $this->gunList->fromString($name);
        switch ($name) {
            //Handgun
            case "Mle1903":
                $item = new ItemHandGun("Mle1903", new HandGunInterpreter(new Mle1903(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::HandGun());
                break;
            case "P08":
                $item = new ItemHandGun("P08", new HandGunInterpreter(new P08(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::HandGun());
                break;
            case "C96":
                $item = new ItemHandGun("C96", new HandGunInterpreter(new C96(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::HandGun());
                break;
            case "HowdahPistol":
                $item = new ItemHandGun("HowdahPistol", new HandGunInterpreter(new HowdahPistol(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::HandGun());
                break;

            //AssaultRifle
            case "M1907SL":
                $item = new ItemAssaultRifle("M1907SL", new AssaultRifleInterpreter(new M1907SL(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::AssaultRifle());
                break;
            case "CeiRigotti":
                $item = new ItemAssaultRifle("CeiRigotti", new AssaultRifleInterpreter(new CeiRigotti(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::AssaultRifle());
                break;
            case "FedorovAvtomat":
                $item = new ItemAssaultRifle("FedorovAvtomat", new AssaultRifleInterpreter(new FedorovAvtomat(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::AssaultRifle());
                break;
            case "Ribeyrolles":
                $item = new ItemAssaultRifle("Ribeyrolles", new AssaultRifleInterpreter(new Ribeyrolles(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::AssaultRifle());
                break;

            //Shotgun
            case "M1897":
                $item = new ItemShotGun("M1897", new ShotgunInterpreter(new M1897(ShotgunBulletType::Buckshot()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::BUCK_SHOT);
                break;
            case "Model10A":
                $item = new ItemShotGun("Model10A", new ShotgunInterpreter(new Model10A(ShotgunBulletType::Buckshot()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::BUCK_SHOT);
                break;
            case "Automatic12G":
                $item = new ItemShotGun("Automatic12G", new ShotgunInterpreter(new Automatic12G(ShotgunBulletType::Buckshot()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::BUCK_SHOT);
                break;
            case "Model1900":
                $item = new ItemShotGun("Model1900", new ShotgunInterpreter(new Model1900(ShotgunBulletType::Buckshot()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::BUCK_SHOT);
                break;

            case "M1897:Slug":
                $item = new ItemShotGun("M1897", new ShotgunInterpreter(new M1897(ShotgunBulletType::Slug()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::SLUG);
                break;
            case "Model10A:Slug":
                $item = new ItemShotGun("Model10A", new ShotgunInterpreter(new Model10A(ShotgunBulletType::Slug()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::SLUG);
                break;
            case "Automatic12G:Slug":
                $item = new ItemShotGun("Automatic12G", new ShotgunInterpreter(new Automatic12G(ShotgunBulletType::Slug()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::SLUG);
                break;
            case "Model1900:Slug":
                $item = new ItemShotGun("Model1900", new ShotgunInterpreter(new Model1900(ShotgunBulletType::Slug()), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Shotgun(), BulletId::SLUG);
                break;

            //SniperRifle
            case "SMLEMK3":
                $item = new ItemSniperRifle("SMLEMK3", new SniperRifleInterpreter(new SMLEMK3(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SniperRifle());
                break;
            case "Gewehr98":
                $item = new ItemSniperRifle("Gewehr98", new SniperRifleInterpreter(new Gewehr98(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SniperRifle());
                break;
            case "MartiniHenry":
                $item = new ItemSniperRifle("MartiniHenry", new SniperRifleInterpreter(new MartiniHenry(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SniperRifle());
                break;
            case "Type38Arisaka":
                $item = new ItemSniperRifle("Type38Arisaka", new SniperRifleInterpreter(new Type38Arisaka(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SniperRifle());
                break;

            //SMG
            case "MP18":
                $item = new ItemSubMachineGun("MP18", new SubMachineGunInterpreter(new MP18(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SMG());
                break;
            case "Automatico":
                $item = new ItemSubMachineGun("Automatico", new SubMachineGunInterpreter(new Automatico(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SMG());
                break;
            case "Hellriegel1915":
                $item = new ItemSubMachineGun("Hellriegel1915", new SubMachineGunInterpreter(new Hellriegel1915(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SMG());
                break;
            case "FrommerStopAuto":
                $item = new ItemSubMachineGun("FrommerStopAuto", new SubMachineGunInterpreter(new FrommerStopAuto(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::SMG());
                break;

            //LMG
            case "LewisGun":
                $item = new ItemLightMachineGun("LewisGun", new LightMachineGunInterpreter(new LewisGun(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::LMG());
                break;
            case "ParabellumMG14":
                $item = new ItemLightMachineGun("ParabellumMG14", new LightMachineGunInterpreter(new ParabellumMG14(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::LMG());
                break;
            case "MG15":
                $item = new ItemLightMachineGun("MG15", new LightMachineGunInterpreter(new MG15(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::LMG());
                break;
            case "BAR1918":
                $item = new ItemLightMachineGun("BAR1918", new LightMachineGunInterpreter(new BAR1918(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::LMG());
                break;

            //LMG
            case "ColtSAA":
                $item = new ItemRevolver("ColtSAA", new RevolverInterpreter(new ColtSAA(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Revolver());
                break;
            case "RevolverMk6":
                $item = new ItemRevolver("RevolverMk6", new RevolverInterpreter(new RevolverMk6(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Revolver());
                break;
            case "No3Revolver":
                $item = new ItemRevolver("No3Revolver", new RevolverInterpreter(new No3Revolver(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Revolver());
                break;
            case "NagantRevolver":
                $item = new ItemRevolver("NagantRevolver", new RevolverInterpreter(new NagantRevolver(), $player, $this->scheduler));
                $item->setCustomName($item->getName());
                $player->getInventory()->setItemInHand($this->setItemDescription($item));
                $this->giveBullet($player, GunType::Revolver());
                break;
        }
    }

    //TODO:リファクタリング
    public function giveBullet(Player $player, GunType $gunType, int $bulletId = null) {
        $player->getInventory()->addItem(ItemFactory::get(Item::ARROW, 0, 10));
        switch ($gunType->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get(BulletId::HAND_GUN, 0, 64));
                break;
            case GunType::AssaultRifle()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get(BulletId::ASSAULT_RIFLE, 0, 128));
                break;
            case GunType::SniperRifle()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get(BulletId::SNIPER_RIFLE, 0, 64));
                break;
            case GunType::Shotgun()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get($bulletId, 0, 64));
                break;
            case GunType::SMG()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get(BulletId::SMG, 0, 128));
                break;
            case GunType::LMG()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get(BulletId::LMG, 0, 192));
                break;
            case GunType::Revolver()->getTypeText():
                $player->getInventory()->addItem(ItemFactory::get(BulletId::REVOLVER, 0, 64));
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
            //TextFormat::RESET . "マガジンキャパ" . TextFormat::GRAY . $gun->getMagazineCapacity(),
            TextFormat::RESET . "リロード時間" . $gun->getReloadingType()->toString(),
            TextFormat::RESET . "反動" . TextFormat::GRAY . $gun->getReaction(),
            TextFormat::RESET . "精度" . TextFormat::GRAY . "ADS:" . $gun->getPrecision()->getADS() . "腰撃ち:" . $gun->getPrecision()->getHipShooting(),
        ]);
    }
}