<?php


namespace game_system;


use game_system\interpreter\TeamDeathMatchInterpreter;
use game_system\model\map\TeamDeathMatchMap;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\pmmp\WeaponSelectForm;
use game_system\pmmp\WorldController;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class GameSystemListener
{
    private $usersService;
    private $weaponService;
    private $teamDeathMatchInterpreter;

    private $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->usersService = new UsersService();
        $this->weaponService = new WeaponsService();
        $this->scheduler = $scheduler;
        $this->teamDeathMatchInterpreter = new TeamDeathMatchInterpreter(
            new TeamDeathMatchClient(),
            $this->usersService,
            $this->weaponService,
            $this->scheduler
        );
    }

    public function initGame(TeamDeathMatchMap $map): bool {
        return $this->teamDeathMatchInterpreter->init($map,600);
    }

    public function startGame(): bool {
        return $this->teamDeathMatchInterpreter->start();
    }

    public function joinGame(string $userName): bool {
        return $this->teamDeathMatchInterpreter->join($userName);
    }

    public function quitGame(string $userName): bool {
        return $this->teamDeathMatchInterpreter->quitGame($userName);
    }

    public function closeGame(): bool {
        return $this->teamDeathMatchInterpreter->closeGame();
    }

    public function onReceivedDamage(Player $attacker, Entity $target, string $weaponName, int $damage): void {
        $health = $target->getHealth() - $damage;
        if ($target instanceof Human) {
            $this->teamDeathMatchInterpreter->onReceiveDamage($attacker, $target, $weaponName, $damage);
        } else {
            $target->setHealth($health);
        }
    }

    public function selectWeapon(Player $player) {
        $playerName = $player->getName();
        $player->sendForm(new WeaponSelectForm(function ($weaponName) use ($playerName) {
            if ($weaponName !== null) {
                //if ($this->weaponService->isOwn($playerName, $weaponName)) {
                $this->usersService->selectWeapon($playerName, $weaponName);
                //}
            }
        }));
    }

    public function userLogin(string $userName): void {
        $player = Server::getInstance()->getPlayer($userName);
        $player->getInventory()->setContents([]);
        $worldController = new WorldController();
        $worldController->teleport($player, "lobby");

        if (!$this->usersService->exists($userName))
            $this->weaponService->register($userName, "M1907SL");

        $this->usersService->userLogin($userName);
    }
}