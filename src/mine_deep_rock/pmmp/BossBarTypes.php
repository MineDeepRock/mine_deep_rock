<?php

namespace mine_deep_rock\pmmp;


use bossbar_system\model\BossBarType;
use team_game_system\model\GameType;

class BossBarTypes
{
    static function TDM(): BossBarType {
        return new BossBarType("TDM");
    }

    static function Domination(): BossBarType {
        return new BossBarType("Domination");
    }

    static function OneOnOne(): BossBarType {
        return new BossBarType("OneOnOne");
    }

    static function fromGameType(GameType $gameType): ?BossBarType {
        switch (strval($gameType)) {
            case strval(BossBarTypes::TDM()):
                return BossBarTypes::TDM();

            case strval(BossBarTypes::Domination()):
                return BossBarTypes::Domination();

            case strval(BossBarTypes::OneOnOne()):
                return BossBarTypes::OneOnOne();
        }

        return null;
    }
}