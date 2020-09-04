<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\model\Skill;

class BuySkillService
{
    static function execute(string $name, Skill $skill) {
        $status = PlayerStatusDAO::get($name);

        if ($status->isOwingSkill($skill)) {
            return false;
        }

        if ($status->getMoney() <= 2000) {
            return false;
        }

        $skills = $status->getOwningSkills();
        $skills[] = $skill;

        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $status->getLevel(),
                $status->getMoney(),
                $skills
            )
        );
        SpendMoneyService::execute($name, 2000);
        return true;
    }
}