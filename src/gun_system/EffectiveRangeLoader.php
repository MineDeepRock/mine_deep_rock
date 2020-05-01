<?php


namespace gun_system;


class EffectiveRangeLoader
{
    private static $instance;

    public $ranges = [
        //"Mle1903" => [],
        //"P08" => [],
        //"C96" => [],
        //"HowdahPistol" => [],
        "M1907SL" => [],
        "CeiRigotti" => [],
        "FedorovAvtomat" => [],
        "Ribeyrolles" => [],
        "M1897" => [],
        "Model10A" => [],
        "Automatic12G" => [],
        "Model1900" => [],
        //"SMLEMK3" => [],
        //"Gewehr98" => [],
        //"MartiniHenry" => [],
        //"Type38Arisaka" => [],
        //"MP18" => [],
        //"Automatico" => [],
        //"Hellriegel1915" => [],
        //"FrommerStopAuto" => [],
        //"LewisGun" => [],
        //"ParabellumMG14" => [],
        //"MG15" => [],
        //"BAR1918" => [],
        //"ColtSAA" => [],
        //"NagantRevolver" => [],
        //"No3Revolver" => [],
        //"RevolverMk6" => []
    ];

    public function __construct() {
        self::$instance = $this;
    }

    public static function getInstance(): EffectiveRangeLoader {
        return self::$instance;
    }

    public function loadAll() {
        foreach ($this->ranges as $name => $range) {
            $this->ranges[$name] = $this->load("D:\pmmp\plugins\mine_deep_rock\src\gun_system\data\\effective_ranges\\" . $name . ".png");
        }
        var_dump($this->ranges);
    }

    public function load(string $path): array {
        $im = imagecreatefrompng($path);
        $range = [];
        $rgb = [];
        $x = 0;
        while ($x < 100) {
            $y = 0;
            while ($y < 100) {
                $imageColorAt = imagecolorat($im, $x, $y);
                $rgb['red'] = ($imageColorAt >> 16) & 0xFF;
                $rgb['green'] = ($imageColorAt >> 8) & 0xFF;
                $rgb['blue'] = $imageColorAt & 0xFF;
                if ($rgb['red'] === 255 && $rgb['green'] === 0 && $rgb['blue'] === 0) {
                    $range[$x] = 100 - $y;
                }
                $y++;
            }
            $x++;
        }
        imagedestroy($im);
        return $range;
    }
}