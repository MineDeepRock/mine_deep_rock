<?php


namespace game_system\pmmp\form;


use Closure;
use game_system\model\military_department\AssaultSoldier;
use game_system\model\military_department\Engineer;
use game_system\model\military_department\NursingSoldier;
use game_system\model\military_department\Scout;
use pocketmine\form\Form;
use pocketmine\Player;

class MilitaryDepartmentSelectForm implements Form
{
    private $onSelected;

    public function __construct(Closure $onSelected) {
        $this->onSelected = $onSelected;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $militaryDepartments =
            [
                "AssaultSoldier",
                "Engineer",
                "NursingSoldier",
                "Scout",
            ];

        switch ($militaryDepartments[$data]) {
            case 'AssaultSoldier':
                $player->sendForm(new MilitaryDepartmentDetailForm(function (){
                    ($this->onSelected)(new AssaultSoldier());
                },new AssaultSoldier()));
                break;
            case 'Engineer':
                $player->sendForm(new MilitaryDepartmentDetailForm(function (){
                    ($this->onSelected)(new Engineer());
                },new Engineer()));
                break;
            case 'NursingSoldier':
                $player->sendForm(new MilitaryDepartmentDetailForm(function (){
                    ($this->onSelected)(new NursingSoldier());
                },new NursingSoldier()));
                break;
            case 'Scout':
                $player->sendForm(new MilitaryDepartmentDetailForm(function (){
                    ($this->onSelected)(new Scout());
                },new Scout()));
                break;
        }
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => '武器種',
            'buttons' => [
                ["text" => "突撃兵"],
                ["text" => "工兵"],
                ["text" => "看護兵"],
                ["text" => "斥候兵"]
            ]
        ];
    }
}