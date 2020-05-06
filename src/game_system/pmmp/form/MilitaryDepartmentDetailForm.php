<?php


namespace game_system\pmmp\form;


use Closure;
use game_system\model\military_department\MilitaryDepartment;
use pocketmine\form\Form;
use pocketmine\Player;

class MilitaryDepartmentDetailForm implements Form
{
    private $onSelected;
    private $militaryDepartment;

    public function __construct(Closure $onSelected, MilitaryDepartment $militaryDepartment) {
        $this->onSelected = $onSelected;
        $this->militaryDepartment = $militaryDepartment;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }
        ($this->onSelected)();
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => $this->militaryDepartment->getName(),
            'content' => $this->militaryDepartment->getDescription(),
            'buttons' => [
                ["text" => "選択"],
            ]
        ];
    }
}