<?php


namespace game_system\listener;


use game_system\pmmp\form\sub_weapon_select_form\SubWeaponSelectForm;
use game_system\pmmp\form\trial_weapon_select_form\TrialWeaponSelectForm;
use game_system\pmmp\form\weapon_purchase_form\WeaponPurchaseForm;
use game_system\pmmp\form\weapon_select_form\WeaponSelectForm;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\GunList;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\Server;

class WeaponListener
{
    protected $usersService;
    protected $weaponService;

    public function __construct(UsersService $usersService, WeaponsService $weaponService) {
        $this->usersService = $usersService;
        $this->weaponService = $weaponService;
    }

    public function buyWeapon(string $ownerName, string $weaponName): void {
        if ($this->isAbleToBuy($ownerName, $weaponName)) {
            $weapon = GunList::fromString($weaponName);
            $this->usersService->spendMoney($ownerName, $weapon->getMoneyCost()->getValue());
            $this->weaponService->register($ownerName, $weaponName);
        }
    }

    public function isAbleToBuy(string $ownerName, string $weaponName): bool {
        $gun = GunList::fromString($weaponName);

        $user = $this->usersService->getUserData($ownerName);

        if ($user->getMoney() <= $gun->getMoneyCost()->getValue())
            return false;

        if ($this->weaponService->isOwn($ownerName, $weaponName))
            return false;

        $killCountCondition = $gun->getKillCountCondition();

        if ($killCountCondition !== null) {
            if (!($this->weaponService->getWeapon($ownerName, $killCountCondition->getWeaponName())->getKillCount() <= $killCountCondition->getCount())) {
                return false;
            }
        }

        return true;
    }

    public function displayWeaponPurchaseForm(Player $player) {
        $playerName = $player->getName();
        $player->sendForm(new WeaponPurchaseForm(function ($weaponName) use ($player, $playerName) {
            if ($this->isAbleToBuy($playerName, $weaponName)) {
                $this->buyWeapon($playerName, $weaponName);
                $player->sendMessage($weaponName . "を購入しました");
            } else {
                $player->sendMessage("条件を満たしていないか、すでに持っているので購入できません");
            }
        }, array_map(function ($weapon) {
            return $weapon->getName();
        }, $this->weaponService->getOwnWeapons($playerName))));
    }

    public function displayTrialWeaponSelectForm(Player $player) {
        $playerName = $player->getName();
        $player->sendForm(new TrialWeaponSelectForm(function ($weaponName, $scopeName) use ($playerName): void {
            Server::getInstance()->dispatchCommand(
                new ConsoleCommandSender(),
                "gun give \"" . $playerName . "\" " . $weaponName . " " . $scopeName);
        }));
    }

    public function displayWeaponSelectForm(Player $player) {
        $playerName = $player->getName();
        $user = $this->usersService->getUserData($playerName);

        $player->sendForm(new WeaponSelectForm(function ($weaponName, $scopeName) use ($playerName) {
            $this->usersService->selectWeapon($playerName, $weaponName);
            $this->weaponService->setScope($playerName, $weaponName, $scopeName);
        },
            $this->weaponService->getOwnWeapons($playerName),
            $user->getMilitaryDepartment()->getCanEquipGunTypes()));
    }

    public function displaySubWeaponSelectForm(Player $player) {
        $playerName = $player->getName();
        $player->sendForm(new SubWeaponSelectForm(function ($weaponName, $scopeName) use ($playerName) {
            $this->usersService->selectSubWeapon($playerName, $weaponName);
            $this->weaponService->setScope($playerName, $weaponName, $scopeName);
        }, $this->weaponService->getOwnWeapons($playerName)));
    }
}