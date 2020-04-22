<?php


namespace gun_system\models\attachment\bullet;


class ShotgunBulletType
{
    private $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function equal(ShotgunBulletType $gunType): bool {
        return $this->type == $gunType->type;
    }

    public static function Buckshot(): ShotgunBulletType {
        return new ShotgunBulletType("Buckshot");
    }

    public static function Slug(): ShotgunBulletType {
        return new ShotgunBulletType("Slug");
    }

    public static function Dart(): ShotgunBulletType {
        return new ShotgunBulletType("Dart");
    }

    public static function fromString(string $string): ShotgunBulletType {
        switch ($string) {
            case "Buckshot":
                return self::Buckshot();
            case "Slug":
                return self::Slug();
            case "Dart":
                return self::Dart();
        }
        return self::Buckshot();
    }

    /**
     * @return mixed
     */
    public function getTypeText() {
        return $this->type;
    }
}