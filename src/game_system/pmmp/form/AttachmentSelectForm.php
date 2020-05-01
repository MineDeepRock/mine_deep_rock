<?php


namespace game_system\pmmp\form;


use gun_system\models\GunType;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\Server;

class AttachmentSelectForm implements Form
{
    private $buttons;

    public function __construct(Player $player) {
        $gun = null;
        foreach ($player->getInventory()->getContents() as $item) {
            if (is_subclass_of($item,"gun_system\pmmp\items\ItemGun")) {
                $gun = $item;
            }
        }

        if ($gun === null) {
            $this->buttons = [
                ['text' => '銃を所有時のみ利用可能です'],
            ];
        }  else {
            switch ($gun->getGunData()->getType()->getTypeText()) {
                case GunType::HandGun()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                        ['text' => '2xScope'],
                        ['text' => '4xScope'],
                    ];
                    break;
                case GunType::AssaultRifle()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                        ['text' => '2xScope'],
                        ['text' => '4xScope'],
                    ];
                    break;
                case GunType::Shotgun()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                    ];
                    break;
                case GunType::SMG()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                        ['text' => '2xScope'],
                        ['text' => '4xScope'],
                    ];
                    break;
                case GunType::LMG()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                        ['text' => '2xScope'],
                        ['text' => '4xScope'],
                    ];
                    break;
                case GunType::SniperRifle()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                        ['text' => '2xScope'],
                        ['text' => '4xScope'],
                    ];
                    break;
                case GunType::Revolver()->getTypeText():
                    $this->buttons = [
                        ['text' => 'IronSight'],
                    ];
                    break;
            }
        }
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $buttons =
            [
                'IronSight',
                '2xScope',
                '4xScope',
            ];
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun attachment " . $player->getName() . " " . $buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => 'スコープ選択',
            'content' => 'スコープ',
            'buttons' => $this->buttons,
        ];
    }
}