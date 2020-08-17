<?php

namespace mine_deep_rock\pmmp;


use bossbar_system\model\BossBarType;

class BossBarTypes
{
    static function TDM(): BossBarType {
        return new BossBarType("TDM");
    }

    static function Domination(): BossBarType {
        return new BossBarType("Domination");
    }
}