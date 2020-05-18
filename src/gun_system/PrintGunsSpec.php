<?php

namespace gun_system;

use gun_system\models\assault_rifle\CeiRigotti;
use gun_system\models\assault_rifle\FedorovAvtomat;
use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\assault_rifle\Ribeyrolles;
use gun_system\models\Gun;
use gun_system\models\hand_gun\C96;
use gun_system\models\hand_gun\HowdahPistol;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\hand_gun\P08;
use gun_system\models\light_machine_gun\BAR1918;
use gun_system\models\light_machine_gun\LewisGun;
use gun_system\models\light_machine_gun\MG15;
use gun_system\models\light_machine_gun\Chauchat;
use gun_system\models\revolver\ColtSAA;
use gun_system\models\revolver\NagantRevolver;
use gun_system\models\revolver\No3Revolver;
use gun_system\models\revolver\RevolverMk6;
use gun_system\models\shotgun\Automatic12G;
use gun_system\models\shotgun\M1897;
use gun_system\models\shotgun\Model10A;
use gun_system\models\shotgun\Model1900;
use gun_system\models\sniper_rifle\Gewehr98;
use gun_system\models\sniper_rifle\GewehrM95;
use gun_system\models\sniper_rifle\MartiniHenry;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sniper_rifle\VetterliVitali;
use gun_system\models\sub_machine_gun\Automatico;
use gun_system\models\sub_machine_gun\FrommerStopAuto;
use gun_system\models\sub_machine_gun\Hellriegel1915;
use gun_system\models\sub_machine_gun\MP18;
use pocketmine\utils\TextFormat;

class PrintGunsSpec
{
    public static function carryOut() {
        $list = array(
            new Mle1903(),
            new P08(),
            new C96(),
            new HowdahPistol(),
            new M1907SL(),
            new CeiRigotti(),
            new FedorovAvtomat(),
            new Ribeyrolles(),
            new M1897(),
            new Model10A(),
            new Automatic12G(),
            new Model1900(),
            new SMLEMK3(),
            new Gewehr98(),
            new MartiniHenry(),
            new VetterliVitali(),
            new GewehrM95(),
            new MP18(),
            new Automatico(),
            new Hellriegel1915(),
            new FrommerStopAuto(),
            new LewisGun(),
            new Chauchat(),
            new MG15(),
            new BAR1918(),
            new ColtSAA(),
            new NagantRevolver(),
            new No3Revolver(),
            new RevolverMk6()
        );
        $data = "";
        $data .= "|種類|名前|ダメージ|弾速|レート(毎秒)|マガジン|リロード時間|精度|距離減衰|";
        $data .= "|----|----|----|----|----|----|----|----|";
        foreach ($list as $gun) {
            if ($gun instanceof  Gun) {
                $bulletDamage = $gun->getBulletDamage();
                $rate = $gun->getRate();
                $reloadingType = $gun->getReloadingType();

                $data .= "|" . $gun->getType()->getTypeText();
                $data .= "|" . $gun::NAME;
                $data .= "|" . $bulletDamage->getValue();
                $data .= "|" . $gun->getBulletSpeed()->getPerSecond();
                $data .= "|" . $rate->getPerSecond();
                $data .= "|" . $reloadingType->magazineCapacity. "/" .$reloadingType->initialAmmo;
                $data .= "|" . $reloadingType->secondToString();
                $data .= "|" . $gun->getReaction();
                $data .= "|" . "ADS:" . $gun->getPrecision()->getADS() . "腰撃ち:" . $gun->getPrecision()->getHipShooting();
                $data .= "|" . "![" .$gun::NAME . "](https://raw.githubusercontent.com/MineDeepRock/MineDeepRock.github.io/master/data/".$gun::NAME .".png)";
                $data .= "|\n";
            }
        }
        $json = fopen('D:\pmmp\plugins\mine_deep_rock\src\gun_system\data\effective_ranges\data.md', 'w+b');
        fwrite($json, json_encode($data,JSON_UNESCAPED_UNICODE));
    }
}