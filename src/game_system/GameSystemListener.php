<?php


namespace game_system;


use game_system\interpreter\TeamDeathMatchInterpreter;
use game_system\model\map\TeamDeathMatchMap;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\pmmp\form\WeaponSelectForm;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\pmmp\WorldController;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\pmmp\items\ItemShotGun;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class GameSystemListener
{
    //TODO:あんまり良くないと思う
    private static $instance;

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

        self::$instance = $this;
    }

    public static function getInstance(): GameSystemListener {
        return self::$instance;
    }

    public function initGame(TeamDeathMatchMap $map): bool {
        return $this->teamDeathMatchInterpreter->init($map, 600);
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

    public function scare(Player $target, Entity $attacker): void {
        $item = $target->getInventory()->getItemInHand();

        $targetUser = $this->usersService->getUserData($target->getName());
        $attackerUser = $this->usersService->getUserData($attacker->getName());
        if ($targetUser->getBelongTeamId() !== null && $attackerUser->getBelongTeamId() !== null) {
            if ($targetUser->getBelongTeamId()->equal($attackerUser->getBelongTeamId())) {
                //自分自身には効果がないように
                if (!($target->getName() === $attacker->getName()) && is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
                    if (!($item instanceof ItemShotGun)) {
                        $target->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20 * 3, 1));
                        $item->scare();
                    }
                }
            }
        }
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
        $player->getInventory()->addItem(new WeaponSelectItem());

        if (!$this->usersService->exists($userName))
            $this->weaponService->register($userName, "M1907SL");

        $this->usersService->userLogin($userName);
    }
}