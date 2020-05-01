<?php


namespace game_system\interpreter;


use Closure;
use game_system\GameSystemListener;
use game_system\model\map\TeamDeathMatchMap;
use game_system\model\TeamDeathMatch;
use game_system\model\User;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\pmmp\form\WeaponSelectForm;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\BulletId;
use gun_system\models\GunList;
use gun_system\models\GunType;
use gun_system\models\shotgun\Shotgun;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class TeamDeathMatchInterpreter
{
    private $client;
    private $usersService;
    private $weaponService;
    private $scheduler;

    private $taskHandler;
    private $onFinished;

    private $game;
    private $limitSecond;

    public function __construct(TeamDeathMatchClient $client, UsersService $userService, WeaponsService $weaponService, TaskScheduler $scheduler) {
        $this->client = $client;
        $this->usersService = $userService;
        $this->scheduler = $scheduler;
        $this->weaponService = $weaponService;
    }

    public function getGameData(): TeamDeathMatch {
        return $this->game;
    }

    public function init(TeamDeathMatchMap $map, int $limitSecond, Closure $onFinished): bool {
        if ($this->game !== null)
            return false;

        $this->onFinished = $onFinished;
        $this->limitSecond = $limitSecond;
        $this->game = new TeamDeathMatch($map);
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

    private function onFinished(): void {
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
        $this->game = null;
        $this->client->onFinish($winTeam, $participants);
        ($this->onFinished)();
    }

    public function join(string $userName): bool {
        if ($this->game === null)
            return false;

        $user = $this->usersService->getUserData($userName);
        if ($user->getParticipatedGameId() !== null)
            return false;

        if ($this->game->isStarted()) {
            $this->usersService->joinGame(
                $userName,
                $this->game->getId(),
                $this->game->getBlueTeam()->getId(),
                $this->game->getRedTeam()->getId(),
                $user->getLastBelongTeamId());

            $user = $this->usersService->getUserData($userName);

            $this->client->joinOnTheWay(
                $user,
                $user->getBelongTeamId(),
                $this->game->redTeamScore,
                $this->game->blueTeamScore);

            $this->spawn($user);
            return true;
        } else {
            $this->usersService->joinGame(
                $userName,
                $this->game->getId(),
                $this->game->getBlueTeam()->getId(),
                $this->game->getRedTeam()->getId());
        }
        return true;
    }

    public function onReceiveDamage(Player $attackerPlayer, Entity $targetEntity, string $weaponName, int $damage): void {
        $health = $targetEntity->getHealth() - $damage;
        if ($this->game !== null) {
            if ($targetEntity->getLevel()->getName() === $this->game->getMap()->getName()) {
                $attacker = $this->usersService->getUserData($attackerPlayer->getName());
                $target = $this->usersService->getUserData($targetEntity->getName());

                if (!($attacker->getBelongTeamId()->equal($target->getBelongTeamId()))) {

                    $targetPlayer = Server::getInstance()->getPlayer($targetEntity->getName());

                    if ($health <= 0) {
                        //弾薬回復
                        $attackerPlayer->getInventory()->addItem($this->getAmmo($attacker));

                        $attackerName = $attacker->getName();
                        $this->weaponService->addKillCount($attacker->getName(), $weaponName);
                        $this->usersService->addMoney($attackerName, 100);
                        $this->addScoreByKilling($attacker, $target);

                        $targetPlayer->setGamemode(Player::SPECTATOR);
                        $targetPlayer->teleport(new Vector3(
                            $attackerPlayer->getX(),
                            $attackerPlayer->getY() + 4,
                            $attackerPlayer->getZ()
                        ));
                        
                        GameSystemListener::getInstance()->selectWeapon($targetPlayer);

                        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($targetPlayer,$target, $targetEntity): void {
                            if ($targetPlayer->isOnline()) {
                                $targetPlayer->setGamemode(Player::ADVENTURE);
                                if ($target->getParticipatedGameId() !== null) {
                                    if ($target->getParticipatedGameId()->equal($this->game->getId())) {
                                        $target = $this->usersService->getUserData($targetEntity->getName());
                                        $this->spawn($target);
                                    }
                                }
                            }
                        }), 20 * 8);
                    }

                    $this->client->onReceiveDamage($attackerPlayer, $targetPlayer, $damage, $weaponName);
                }
            }
        }
    }

    private function getAmmo(User $user): Item {
        $weaponName = $user->getSelectedWeaponName();
        $weapon = GunList::fromString($weaponName);
        if ($weapon instanceof Shotgun) {
            $id = BulletId::fromGunType($weapon->getType(), $weapon->getBulletType());
        } else {
            $id = BulletId::fromGunType($weapon->getType());
        }
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

    public function addScoreByKilling(User $attacker, User $victim): void {
        $attackerTeamId = $attacker->getBelongTeamId();

        if ($attackerTeamId->equal($this->game->getRedTeam()->getId())) {
            $this->client->updateRedTeamScoreboard(++$this->game->redTeamScore, $this->game->getMap()->getName());
        } else {
            $this->client->updateBlueTeamScoreboard(++$this->game->blueTeamScore, $this->game->getMap()->getName());
        }
        $this->spawn($victim);
    }

    private function spawn(User $user): void {
        $selectedWeaponName = $user->getSelectedWeaponName();
        $mapName = $this->game->getMap()->getName();

        $this->client->spawn($user->getName(), $selectedWeaponName, $mapName, $this->game->getSpawnPoint($user->getBelongTeamId()));
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

        $this->client->quitGame($userName);
        $this->usersService->quitGame($userName);
        return true;
    }
}