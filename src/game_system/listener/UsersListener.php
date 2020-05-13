<?php


namespace game_system\listener;


use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\FragGrenade;
use game_system\model\Game;
use game_system\model\GameId;
use game_system\pmmp\form\MilitaryDepartmentSelectForm;
use game_system\pmmp\items\MilitaryDepartmentSelectItem;
use game_system\pmmp\items\SubWeaponSelectItem;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\pmmp\WorldController;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\light_machine_gun\Chauchat;
use gun_system\models\shotgun\M1897;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sub_machine_gun\MP18;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class UsersListener
{

    protected $usersService;
    protected $weaponService;
    protected $interpreter;

    public function __construct(UsersService $usersService, WeaponsService $weaponService) {
        $this->usersService = $usersService;
        $this->weaponService = $weaponService;
    }

    public function showUserStatus(Player $player) {
        $user = $this->usersService->getUserData($player->getName());
        $player->sendMessage("所持金:" . $user->getMoney());
    }

    public function displayMilitaryDepartmentSelectForm(Player $player) {
        $playerName = $player->getName();

        $player->sendForm(new MilitaryDepartmentSelectForm(function ($militaryDepartment) use ($playerName) {
            $this->usersService->selectMilitaryDepartment($playerName, $militaryDepartment->getName());
            $this->usersService->selectWeapon($playerName, $militaryDepartment->getDefaultWeaponName());
        }));
    }

    public function userLogin(player $player): void {
        $this->printDescription($player);

        $player->getInventory()->setContents([]);
        $worldController = new WorldController();
        $worldController->teleport($player, "lobby");
        $player->getInventory()->addItem(new MilitaryDepartmentSelectItem());
        $player->getInventory()->addItem(new WeaponSelectItem());
        $player->getInventory()->addItem(new SubWeaponSelectItem());
        $player->setGamemode(Player::ADVENTURE);

        if (!$this->usersService->exists($player->getName())) {
            $this->weaponService->register($player->getName(), M1907SL::NAME);
            $this->weaponService->register($player->getName(), Mle1903::NAME);
            $this->weaponService->register($player->getName(), Chauchat::NAME);
            $this->weaponService->register($player->getName(), M1897::NAME);
            $this->weaponService->register($player->getName(), SMLEMK3::NAME);
            $this->weaponService->register($player->getName(), MP18::NAME);

            $fragGrenade = new FragGrenade();
            $this->weaponService->register($player->getName(), $fragGrenade->getName());
        }
        $this->usersService->userLogin($player->getName());
    }

    private function printDescription(Player $player){
        $text = "ようこそ！MineDeepRockへ！\n";
        $text .= "BF1をリスペクトした銃PvPサーバーです！\n";
        $text .= "兵科ごとに異なる武器やガジェットを活かして戦いましょう！\n";
        $player->sendMessage($text);
    }
}