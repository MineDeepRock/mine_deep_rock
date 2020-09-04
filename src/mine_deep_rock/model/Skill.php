<?php


namespace mine_deep_rock\model;


use mine_deep_rock\model\skill\assault_soldier\DontGiveUp;
use mine_deep_rock\model\skill\assault_soldier\SecondChance;
use mine_deep_rock\model\skill\engineer\LoveAmmo;
use mine_deep_rock\model\skill\engineer\StopProgress;
use mine_deep_rock\model\skill\engineer\TheWall;
use mine_deep_rock\model\skill\normal\AntiSpot;
use mine_deep_rock\model\skill\normal\Cover;
use mine_deep_rock\model\skill\normal\Frack;
use mine_deep_rock\model\skill\normal\QuickRunAway;
use mine_deep_rock\model\skill\nursing_soldier\Entrusting;
use mine_deep_rock\model\skill\nursing_soldier\HelpEachOther;
use mine_deep_rock\model\skill\nursing_soldier\StimulantSyringe;
use mine_deep_rock\model\skill\scout\LuminescentBullet;
use mine_deep_rock\model\skill\scout\SavingBullet;
use mine_deep_rock\model\skill\scout\Scape;

class Skill
{
    const Name = "";
    const Description = "";

    static function fromString(string $string): ?Skill {

        switch ($string) {
            case AntiSpot::Name:
                return new AntiSpot();
            case Cover::Name:
                return new Cover();
            case Frack::Name:
                return new Frack();
            case QuickRunAway::Name:
                return new QuickRunAway();

            case DontGiveUp::Name:
                return new DontGiveUp();
            case SecondChance::Name:
                return new SecondChance();

            case LoveAmmo::Name:
                return new LoveAmmo();
            case StopProgress::Name:
                return new StopProgress();
            case TheWall::Name:
                return new TheWall();

            case Entrusting::Name:
                return new Entrusting();
            case HelpEachOther::Name:
                return new HelpEachOther();
            case StimulantSyringe::Name:
                return new StimulantSyringe();

            case LuminescentBullet::Name:
                return new LuminescentBullet();
            case SavingBullet::Name:
                return new SavingBullet();
            case Scape::Name:
                return new Scape();
        }

        return null;
    }
}