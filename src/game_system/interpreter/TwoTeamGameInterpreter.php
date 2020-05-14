<?php


namespace game_system\interpreter;


use Closure;
use game_system\model\Coordinate;
use game_system\model\GadgetType;
use game_system\model\TwoTeamGame;
use game_system\model\User;
use game_system\pmmp\items\FragGrenadeItem;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\pmmp\items\SpawnFlareBoxItem;
use game_system\pmmp\items\SpawnMedicineBoxItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\BulletId;
use gun_system\models\GunList;
use gun_system\models\GunType;
use game_system\pmmp\client\TwoTeamGameClient;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class TwoTeamGameInterpreter
{
    protected $client;
    protected $usersService;
    protected $weaponService;
    protected $gameScoresService;
    protected $scheduler;

    private $taskHandler;
    private $startTaskHandler;
    private $onFinished;

    protected $game;
    private $limitSecond;

    public function __construct(TwoTeamGameClient $client, UsersService $userService, WeaponsService $weaponService, GameScoresService $gameScoresService, TaskScheduler $scheduler) {
        $this->client = $client;
        $this->usersService = $userService;
        $this->weaponService = $weaponService;
        $this->gameScoresService = $gameScoresService;
        $this->scheduler = $scheduler;
    }

    public function getGameData(): TwoTeamGame {
        return $this->game;
    }

    public function init(TwoTeamGame $game, int $limitSecond, Closure $onFinished): bool {
        if ($this->game !== null)
            return false;

        $this->onFinished = $onFinished;
        $this->limitSecond = $limitSecond;
        $this->game = $game;
        return true;
    }

    public function start(): bool {
        if ($this->game === null)
            return false;
        if ($this->game->isStarted())
            return false;

        $this->game->start();

        $participants = $this->usersService->getParticipants($this->game->getId());
        $this->client->start(
            $participants,
            $this->game->getRedTeam()->getId(),
            $this->game->redTeamScore,
            $this->game->blueTeamScore);


        $participants = $this->usersService->getParticipants($this->game->getId());
        foreach ($participants as $participant) {
            $this->spawn($participant);
        }

        $this->taskHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $tick): void {
            $this->game->pass();
            $this->client->displayRemainingTime($this->limitSecond - $this->game->getElapsedSecond(), $this->game->getMap()->getName());

            if ($this->game->getElapsedSecond() === $this->limitSecond) {
                $this->onFinished();
            }
        }), 20 * 1);
        return true;
    }

    protected function onFinished(): void {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());
        foreach ($level->getEntities() as $entity) {
            if (!($entity instanceof Player)) $entity->kill();
        }

        $this->taskHandler->cancel();
        $participants = $this->usersService->getParticipants($this->game->getId());
        $winTeam = $this->game->getWinTeam();
        foreach ($participants as $participant) {
            if ($participant->getBelongTeamId()->equal($winTeam->getId())) {
                $this->usersService->addWinCount($participant->getName());
                $this->usersService->addMoney($participant->getName(), 1000);
            }
            $this->usersService->quitGame($participant->getName());
        }
        $redTeamScores = $this->gameScoresService->getTeamScores($this->game->getRedTeam()->getId());
        $blueTeamScores = $this->gameScoresService->getTeamScores($this->game->getBlueTeam()->getId());

        $this->client->onFinish($winTeam, $participants, $redTeamScores, $blueTeamScores);
        $this->displayParticipantCount(0);
        $this->game = null;
        ($this->onFinished)();
    }

    public function join(string $userName): bool {
        if ($this->game === null)
            return false;

        $user = $this->usersService->getUserData($userName);
        if ($user->getParticipatedGameId() !== null)
            return false;

        if ($this->game->isStarted()) {
            if ($user->getLastBelongTeamId() === null) {
                $this->usersService->joinGame(
                    $userName,
                    $this->game->getId(),
                    $this->game->getBlueTeam()->getId(),
                    $this->game->getRedTeam()->getId());
            } else if ($user->getLastBelongTeamId()->equal($this->game->getBlueTeam()->getId()) ||
                $user->getLastBelongTeamId()->equal($this->game->getRedTeam()->getId())) {
                $this->usersService->joinGame(
                    $userName,
                    $this->game->getId(),
                    $this->game->getBlueTeam()->getId(),
                    $this->game->getRedTeam()->getId(),
                    $user->getLastBelongTeamId());
            } else {
                $this->usersService->joinGame(
                    $userName,
                    $this->game->getId(),
                    $this->game->getBlueTeam()->getId(),
                    $this->game->getRedTeam()->getId());
            }

            $user = $this->usersService->getUserData($userName);

            $this->client->joinOnTheWay(
                $user,
                $this->game->getRedTeam()->getId(),
                $this->game->redTeamScore,
                $this->game->blueTeamScore);

            $this->spawn($user);
        } else {
            $this->usersService->joinGame(
                $userName,
                $this->game->getId(),
                $this->game->getBlueTeam()->getId(),
                $this->game->getRedTeam()->getId());
        }

        //更新するためにもう一度取得する
        $user = $this->usersService->getUserData($userName);
        $this->gameScoresService->addScore($userName, $this->game->getId(), $user->getBelongTeamId());
        if (count($this->usersService->getParticipants($this->game->getId())) >= 4) {
            if (!$this->game->isStarted()) {
                $this->startTaskHandler = $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
                    $this->start();
                }), 20 * 30);
            }
        }
        return true;
    }

    public function scare(User $targetUser, User $attackerUser, Item $item) {
        if ($targetUser->getBelongTeamId() === null || $attackerUser->getBelongTeamId() === null) {
            return;
        }
        //味方には効果が無いように
        if ($targetUser->getBelongTeamId()->equal($attackerUser->getBelongTeamId()))
            return;

        //自分自身には効果がないように
        if (!($attackerUser->getName() === $targetUser->getName()) && is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $target = Server::getInstance()->getPlayer($targetUser->getName());
            $this->client->scare($target, $targetUser->getMilitaryDepartment()->getEffects(), $item);
        }
    }

    public function onReceiveDamage(Player $attackerPlayer, Entity $targetEntity, string $weaponName, float $damage): void {
        $health = $targetEntity->getHealth() - $damage;
        if ($this->game !== null) {
            if ($targetEntity->getLevel()->getName() === $this->game->getMap()->getName()) {
                $attacker = $this->usersService->getUserData($attackerPlayer->getName());
                $target = $this->usersService->getUserData($targetEntity->getName());

                if ($targetEntity instanceof Player) {
                    if (!($attacker->getBelongTeamId()->equal($target->getBelongTeamId()))
                        || $attackerPlayer->getName() === $targetEntity->getName()) {
                        if ($health <= 0) $this->onDead($attackerPlayer, $weaponName, $targetEntity, $attacker, $target);

                        $this->scare($target, $attacker, $targetEntity->getInventory()->getItemInHand());
                        $this->client->onReceiveDamage($attackerPlayer, $targetEntity, $damage, $weaponName);
                    }
                }
            }
        }
    }

    protected function onDead(Player $attackerPlayer, string $attackerWeaponName, Player $targetPlayer, User $attackerUser, User $targetUser): void {
        if ($attackerPlayer->getName() !== $targetPlayer->getName()) {
            $attackerName = $attackerPlayer->getName();
            $this->weaponService->addKillCount($attackerPlayer->getName(), $attackerWeaponName);
            $this->usersService->addMoney($attackerName, 100);
        }

        $targetPlayer->setGamemode(Player::SPECTATOR);
        $targetPlayer->teleport(new Vector3(
            $attackerPlayer->getX(),
            $attackerPlayer->getY() + 4,
            $attackerPlayer->getZ()
        ));
    }

    private function getAmmo(string $weaponName): Item {
        $weapon = GunList::fromString($weaponName);
        $id = BulletId::fromGunType($weapon->getType());
        switch ($weapon->getType()->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                return ItemFactory::get($id, 0, 30);
                break;
            case GunType::AssaultRifle()->getTypeText():
                return ItemFactory::get($id, 0, 30);
                break;
            case GunType::Shotgun()->getTypeText():
                return ItemFactory::get($id, 0, 20);
                break;
            case GunType::SMG()->getTypeText():
                return ItemFactory::get($id, 0, 30);
                break;
            case GunType::LMG()->getTypeText():
                return ItemFactory::get($id, 0, 64);
                break;
            case GunType::SniperRifle()->getTypeText():
                return ItemFactory::get($id, 0, 5);
                break;
            case GunType::Revolver()->getTypeText():
                return ItemFactory::get($id, 0, 30);
                break;
        }
        return ItemFactory::get(Item::COOKED_BEEF, 0, 30);
    }

    public function spawn(User $user, ?Coordinate $spawnPoint = null): void {
        $player = Server::getInstance()->getPlayer($user->getName());

        if (!$player->isOnline())
            return;

        $player->setGamemode(Player::ADVENTURE);

        if ($user->getParticipatedGameId() === null)
            return;

        if (!$user->getParticipatedGameId()->equal($this->game->getId()))
            return;

        $selectedWeaponName = $user->getSelectedWeaponName();
        $selectedWeapon = $this->weaponService->getWeapon($user->getName(), $selectedWeaponName);
        $selectedWeaponType = GunList::fromString($selectedWeaponName)->getType()->getTypeText();

        $selectedSubWeaponName = $user->getSelectedSubWeaponName();
        $selectedSubWeapon = $this->weaponService->getWeapon($user->getName(), $selectedSubWeaponName);
        $selectedSubWeaponType = GunList::fromString($selectedSubWeaponName)->getType()->getTypeText();

        $mapName = $this->game->getMap()->getName();

        $spawnGadgetItems = [];
        foreach ($user->getMilitaryDepartment()->getCanEquipGadgetTypes() as $type) {
            switch ($type->getTypeText()) {
                case GadgetType::AmmoBox()->getTypeText():
                    $spawnGadgetItems[] = new SpawnAmmoBoxItem();
                    break;
                case GadgetType::MedicineBox()->getTypeText():
                    $spawnGadgetItems[] = new SpawnMedicineBoxItem();
                    break;
                case GadgetType::FlareBox()->getTypeText():
                    $spawnGadgetItems[] = new SpawnFlareBoxItem();
                    break;
                case GadgetType::FragGrenade()->getTypeText():
                    $spawnGadgetItems[] = new FragGrenadeItem();
                    break;
            }
        }

        $this->client->spawn(
            $player,
            $user->getBelongTeamId(),
            $this->game->getRedTeam()->getId(),
            $user->getMilitaryDepartment()->getJpaName(),
            $spawnGadgetItems,
            $user->getMilitaryDepartment()->getEffects(),
            $selectedWeapon,
            $selectedWeaponType,
            $selectedSubWeapon,
            $selectedSubWeaponType,
            $mapName,
            $spawnPoint ?? $this->game->getSpawnPoint($user->getBelongTeamId()));
    }

    public function closeGame(): bool {
        if ($this->game === null)
            return false;
        //TODO
        return true;
    }

    public function quitGame(string $userName): bool {
        if ($this->game === null)
            return false;

        if (count($this->usersService->getParticipants($this->game->getId())) < 4) {
            if ($this->startTaskHandler !== null) {
                $this->startTaskHandler->cancel();
            }
        }
        $this->client->quitGame($userName);
        $this->usersService->quitGame($userName);
        return true;
    }

    public function displayParticipantCount(?int $count = null) {
        $count = $count ?? count($this->usersService->getParticipants($this->game->getId()));
        $this->client->displayParticipantCount($count);
    }
}