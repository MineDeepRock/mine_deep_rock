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
use gun_system\models\revolver\ColtSAA;
use gun_system\models\revolver\NagantRevolver;
use gun_system\models\revolver\No3Revolver;
use gun_system\models\revolver\RevolverMk6;
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

class GunList
{
    private $ar = [];
    private $hg = [];
    private $sg = [];
    private $sgs = [];
    private $sn = [];
    private $smg = [];
    private $lmg = [];
    private $rv = [];

    public function __construct() {
        $this->ar = [
            new M1907SL(),
            new CeiRigotti(),
            new FedorovAvtomat(),
            new Ribeyrolles(),
        ];
        $this->hg = [
            new Mle1903(),
            new P08(),
            new C96(),
            new HowdahPistol(),
        ];
        $this->sg = [
            new M1897(ShotgunBulletType::Buckshot()),
            new Model10A(ShotgunBulletType::Buckshot()),
            new Automatic12G(ShotgunBulletType::Buckshot()),
            new Model1900(ShotgunBulletType::Buckshot()),
        ];
        $this->sgs = [
            new M1897(ShotgunBulletType::Slug()),
            new Model10A(ShotgunBulletType::Slug()),
            new Automatic12G(ShotgunBulletType::Slug()),
            new Model1900(ShotgunBulletType::Slug()),
        ];
        $this->sn = [
            new SMLEMK3(),
            new Gewehr98(),
            new MartiniHenry(),
            new Type38Arisaka(),
        ];
        $this->smg = [
            new MP18(),
            new Automatico(),
            new Hellriegel1915(),
            new FrommerStopAuto(),
        ];
        $this->lmg = [
            new LewisGun(),
            new ParabellumMG14(),
            new MG15(),
            new BAR1918()
        ];
        $this->rv = [
            new ColtSAA(),
            new NagantRevolver(),
            new No3Revolver(),
            new RevolverMk6(),
        ];
    }

    static function fromString(string $string): Gun {
        switch ($string) {
            //Handgun
            case "Mle1903":
                return new Mle1903();
                break;
            case "P08":
                return new P08();
                break;
            case "C96":
                return new C96();
                break;
            case "HowdahPistol":
                return new HowdahPistol();
                break;

            //AssaultRifle
            case "M1907SL":
                return new M1907SL();
                break;
            case "CeiRigotti":
                return new CeiRigotti();
                break;
            case "FedorovAvtomat":
                return new FedorovAvtomat();
                break;
            case "Ribeyrolles":
                return new Ribeyrolles();
                break;

            //Shotgun
            case "M1897":
                return new M1897(ShotgunBulletType::Buckshot());
                break;
            case "Model10A":
                return new Model10A(ShotgunBulletType::Buckshot());
                break;
            case "Automatic12G":
                return new Automatic12G(ShotgunBulletType::Buckshot());
            case "Model1900":
                return new Model1900(ShotgunBulletType::Buckshot());
                break;

            case "M1897:Slug":
                return new M1897(ShotgunBulletType::Slug());
                break;
            case "Model10A:Slug":
                return new Model10A(ShotgunBulletType::Slug());
                break;
            case "Automatic12G:Slug":
                return new Automatic12G(ShotgunBulletType::Slug());
            case "Model1900:Slug":
                return new Model1900(ShotgunBulletType::Slug());
                break;

            //SniperRifle
            case "SMLEMK3":
                return new SMLEMK3();
                break;
            case "Gewehr98":
                return new Gewehr98();
                break;
            case "MartiniHenry":
                return new MartiniHenry();
                break;
            case "Type38Arisaka":
                return new Type38Arisaka();
                break;

            //SMG
            case "MP18":
                return new MP18();
                break;
            case "Automatico":
                return new Automatico();
                break;
            case "Hellriegel1915":
                return new Hellriegel1915();
                break;
            case "FrommerStopAuto":
                return new FrommerStopAuto();
                break;

            //LMG
            case "LewisGun":
                return new LewisGun();
                break;
            case "ParabellumMG14":
                return new ParabellumMG14();
                break;
            case "MG15":
                return new MG15();
                break;
            case "BAR1918":
                return new BAR1918();
                break;

            //LMG
            case "ColtSAA":
                return new ColtSAA();
                break;
            case "RevolverMk6":
                return new RevolverMk6();
                break;
            case "No3Revolver":
                return new No3Revolver();
                break;
            case "NagantRevolver":
                return new NagantRevolver();
                break;
        }
        return null;
    }
}