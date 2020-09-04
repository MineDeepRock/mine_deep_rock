<?php


namespace mine_deep_rock\model;


use mine_deep_rock\model\skill\normal\Frack;

class PlayerStatus
{
    private $name;
    private $level;
    /**
     * @var Skill[]
     */
    private $owningSkills;
    private $money;

    public function __construct(string $name, PlayerLevel $level, int $money, array $owningSkills) {
        $this->name = $name;
        $this->level = $level;
        $this->owningSkills = $owningSkills;
        $this->money = $money;
    }

    static function asNew(string $name): PlayerStatus {
        return new PlayerStatus(
            $name,
            PlayerLevel::asNew(),
            3000,
            [
                new Frack(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMoney(): int {
        return $this->money;
    }

    /**
     * @return array
     */
    public function getOwningSkills(): array {
        return $this->owningSkills;
    }


    public function isOwingSkill(Skill $skill): bool {
        foreach ($this->owningSkills as $owningSkill) {
            if ($owningSkill::Name === $skill::Name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PlayerLevel
     */
    public function getLevel(): PlayerLevel {
        return $this->level;
    }
}