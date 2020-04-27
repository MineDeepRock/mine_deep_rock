<?php


namespace game_system\pmmp;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class WeaponSelectForm implements Form
{
    private $onSelected;

    public function __construct(Closure $onSelected) {
        $this->onSelected = $onSelected;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $buttons =
            [
                'Mle1903',
                'P08',
                'C96',
                'HowdahPistol',

                'M1907SL',
                'CeiRigotti',
                'FedorovAvtomat',
                'Ribeyrolles',

                'M1897',
                'M1897 Slug',
                'Model10A',
                'Model10A Slug',
                'Automatic12G',
                'Automatic12G Slug',
                'Model1900',
                'Model1900 Slug',

                'SMLEMK3',
                'Gewehr98',
                'MartiniHenry',
                'Type38Arisaka',

                'MP18',
                'Automatico',
                'Hellriegel1915',
                'FrommerStopAuto',

                'LewisGun',
                'ParabellumMG14',
                'MG15',
                'BAR1918',

                'ColtSAA',
                'RevolverMk6',
                'No3Revolver',
                'NagantRevolver'
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => '銃選択',
            'buttons' => [
                ['text' => 'Mle1903'],
                ['text' => 'P08'],
                ['text' => 'C96'],
                ['text' => 'HowdahPistol'],

                ['text' => 'M1907SL'],
                ['text' => 'CeiRigotti'],
                ['text' => 'FedorovAvtomat'],
                ['text' => 'Ribeyrolles'],

                ['text' => 'M1897'],
                ['text' => 'M1897 Slug'],
                ['text' => 'Model10A'],
                ['text' => 'Model10A Slug'],
                ['text' => 'Automatic12G'],
                ['text' => 'Automatic12G Slug'],
                ['text' => 'Model1900'],
                ['text' => 'Model1900 Slug'],

                ['text' => 'SMLEMK3'],
                ['text' => 'Gewehr98'],
                ['text' => 'MartiniHenry'],
                ['text' => 'Type38Arisaka'],

                ['text' => 'MP18'],
                ['text' => 'Automatico'],
                ['text' => 'Hellriegel1915'],
                ['text' => 'FrommerStopAuto'],

                ['text' => 'LewisGun'],
                ['text' => 'ParabellumMG14'],
                ['text' => 'MG15'],
                ['text' => 'BAR1918'],

                ['text' => 'ColtSAA'],
                ['text' => 'RevolverMk6'],
                ['text' => 'No3Revolver'],
                ['text' => 'NagantRevolver']
            ]
        ];
    }
}