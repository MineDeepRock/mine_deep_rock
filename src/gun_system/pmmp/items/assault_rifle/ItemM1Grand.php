<?php


namespace gun_system\pmmp\items\assault_rifle;


use gun_system\models\assault_rifle\M1Garand;
use gun_system\models\GunId;
use gun_system\pmmp\items\ItemAssaultRifle;
use pocketmine\scheduler\TaskScheduler;

class ItemM1Grand extends ItemAssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::M1Garand, "M1Garand", new M1Garand($scheduler));
    }
}