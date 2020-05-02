<?php


namespace game_system;


use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\interpreter\TeamDeathMatchInterpreter;
use game_system\model\map\TeamDeathMatchMap;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\pmmp\form\AttachmentSelectForm;
use game_system\pmmp\form\weapon_purchase_form\WeaponPurchaseForm;
use game_system\pmmp\form\weapon_select_form\WeaponSelectForm;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\pmmp\WorldController;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\GunList;
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
        return $this->teamDeathMatchInterpreter->init($map, 600, function () use ($map) {
            $this->onFinished($map);
        });
    }

    private function onFinished(TeamDeathMatchMap $map) {
        $this->initGame($map);
        $api = EasyScoreboardAPI::getInstance();

        $lobbyPlayers = Server::getInstance()->getLevelByName("lobby")->getPlayers();
        $game = $this->teamDeathMatchInterpreter->getGameData();
        if (!$game->isStarted()) {
            foreach ($lobbyPlayers as $player) {
                $api->sendScoreboard($player, "sidebar", "Lobby", false);
                $api->setScore($player, "sidebar", "ゲーム参加人数:", 0, 1);
            }
        }
    }

    public function startGame(): bool {
        $result = $this->teamDeathMatchInterpreter->start();
        $this->updateNumberOfParticipants();
        return $result;
    }

    public function joinGame(string $userName): bool {
        $result = $this->teamDeathMatchInterpreter->join($userName);
        $this->updateNumberOfParticipants();
        return $result;
    }

    public function quitGame(string $userName): bool {
        $result = $this->teamDeathMatchInterpreter->quitGame($userName);
        $this->updateNumberOfParticipants();
        return $result;
    }

    public function closeGame(): bool {
        return $this->teamDeathMatchInterpreter->closeGame();
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

        $use = $this->usersService->getUserData($ownerName);

        if ($use->getMoney() <= $gun->getMoneyCost()->getValue())
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

    public function scare(Player $target, Entity $attacker): void {
        $item = $target->getInventory()->getItemInHand();

        $targetUser = $this->usersService->getUserData($target->getName());
        $attackerUser = $this->usersService->getUserData($attacker->getName());

        if ($targetUser->getBelongTeamId() === null || $attackerUser->getBelongTeamId() === null) {
            return;
        }
        //味方には効果が無いように
        if ($targetUser->getBelongTeamId()->equal($attackerUser->getBelongTeamId())) {
            return;
        }
        //自分自身には効果がないように
        if (!($target->getName() === $attacker->getName()) && is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            //shotgunには効果がない
            if (($item instanceof ItemShotGun)) {
                return;
            }
            $target->removeEffect(Effect::REGENERATION);
            $item->scare(function () use ($target) {
                if ($target->isOnline()) {
                    $target->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), null, 1, false));
                }
            });
            $target->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20 * 3, 1, false));
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

    public function displayWeaponSelectForm(Player $player) {
        $playerName = $player->getName();
        $player->sendForm(new WeaponSelectForm(function ($weaponName) use ($playerName) {
            $this->usersService->selectWeapon($playerName, $weaponName);
        }, $this->weaponService->getOwnWeapons($playerName)));
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
        }));
    }

    public function displaySelectAttachmentForm(Player $player) {
        $player->sendForm(new AttachmentSelectForm($player));
    }

    public function userLogin(string $userName): void {
        $player = Server::getInstance()->getPlayer($userName);
        $player->getInventory()->setContents([]);
        $worldController = new WorldController();
        $worldController->teleport($player, "lobby");
        $player->getInventory()->addItem(new WeaponSelectItem());
        $player->setGamemode(Player::ADVENTURE);

        $api = EasyScoreboardAPI::getInstance();
        $api->sendScoreboard($player, "sidebar", "Lobby", false);
        $lobbyPlayers = Server::getInstance()->getLevelByName("lobby")->getPlayers();

        $game = $this->teamDeathMatchInterpreter->getGameData();
        foreach ($lobbyPlayers as $player) {
            $numberOfParticipants = $this->usersService->getParticipants($game->getId());
            $api->setScore($player, "sidebar", "ゲーム参加人数:", count($numberOfParticipants), 1);
        }

        if (!$this->usersService->exists($userName))
            $this->weaponService->register($userName, "M1907SL");

        $this->usersService->userLogin($userName);
    }

    public function updateNumberOfParticipants() {
        $lobbyPlayers = Server::getInstance()->getLevelByName("lobby")->getPlayers();
        $api = EasyScoreboardAPI::getInstance();
        $game = $this->teamDeathMatchInterpreter->getGameData();
        if (!$game->isStarted()) {
            foreach ($lobbyPlayers as $player) {
                $numberOfParticipants = $this->usersService->getParticipants($game->getId());
                $api->setScore($player, "sidebar", "ゲーム参加人数:", count($numberOfParticipants), 2);
            }
        } else {
            foreach ($lobbyPlayers as $player) {
                $api->removeScore($player, "sidebar", 2);
            }
        }
    }
}