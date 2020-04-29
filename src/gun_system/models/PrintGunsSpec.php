<?php


namespace gun_system\models;


use gun_system\models\assault_rifle\CeiRigotti;
use gun_system\models\assault_rifle\FedorovAvtomat;
use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\assault_rifle\Ribeyrolles;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\hand_gun\C96;
use gun_system\models\hand_gun\HowdahPistol;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\hand_gun\P08;
use gun_system\models\light_machine_gun\BAR1918;
use gun_system\models\light_machine_gun\LewisGun;
use gun_system\models\light_machine_gun\MG15;
use gun_system\models\light_machine_gun\ParabellumMG14;
use gun_system\models\shotgun\Automatic12G;
use gun_system\models\shotgun\M1897;
use gun_system\models\shotgun\Model10A;
use gun_system\models\shotgun\Model1900;
use gun_system\models\sniper_rifle\Gewehr98;
use gun_system\models\sniper_rifle\MartiniHenry;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sniper_rifle\Type38Arisaka;
use gun_system\models\sub_machine_gun\Automatico;
use gun_system\models\sub_machine_gun\FrommerStopAuto;
use gun_system\models\sub_machine_gun\Hellriegel1915;
use gun_system\models\sub_machine_gun\MP18;

class PrintGunsSpec
{
    public static function carryOut($scheduler) {
        $list = array(
            new Mle1903($scheduler),
            new P08($scheduler),
            new C96($scheduler),
            new HowdahPistol($scheduler),
            new M1907SL($scheduler),
            new CeiRigotti($scheduler),
            new FedorovAvtomat($scheduler),
            new Ribeyrolles($scheduler),
            new M1897(ShotgunBulletType::Buckshot(), $scheduler),
            new Model10A(ShotgunBulletType::Buckshot(), $scheduler),
            new Automatic12G(ShotgunBulletType::Buckshot(), $scheduler),
            new Model1900(ShotgunBulletType::Buckshot(), $scheduler),
            new SMLEMK3($scheduler),
            new Gewehr98($scheduler),
            new MartiniHenry($scheduler),
            new Type38Arisaka($scheduler),
            new MP18($scheduler),
            new Automatico($scheduler),
            new Hellriegel1915($scheduler),
            new FrommerStopAuto($scheduler),
            new LewisGun($scheduler),
            new ParabellumMG14($scheduler),
            new MG15($scheduler),
            new BAR1918($scheduler));
        foreach ($list as $gun) {
            $bulletDamage = $gun->getBulletDamage();
            $effectiveRange = $gun->getEffectiveRange();
            $rate = $gun->getRate();
            $str =
                "|". get_class($gun) ."|".
                "| " . $bulletDamage->getMaxDamage() . "-" . $bulletDamage->getMinDamage() .
                "| " . $effectiveRange->getStart() . "-" . $effectiveRange->getEnd() .
                "| " . $rate->getPerSecond() .
                "| " . $gun->getBulletSpeed()->getPerSecond() .
                "| " . $gun->getMagazineCapacity() .
                "| " . $gun->getReloadController()->toString() .
                "| " . $gun->getReaction() .
                "| " . "ADS:" . $gun->getPrecision()->getADS() . "腰撃ち:" . $gun->getPrecision()->getHipShooting() . "|" . "\n";

            $file = 'D:\guns.md';
            $current = file_get_contents($file);
            $current .= $str;
            file_put_contents($file, $current);
        }
    }
}