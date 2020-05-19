<?php


namespace game_system\interpreter;


use Closure;
use game_system\GameSystemBinder;
use game_system\model\FragGrenade;
use game_system\model\SpawnBeacon;
use game_system\pmmp\client\FragGrenadeClient;
use game_system\pmmp\Entity\GrenadeEntity;
use game_system\pmmp\Entity\SandbagEntity;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class FragGrenadeInterpreter extends GrenadeBaseInterpreter
{
    public function __construct(Player $owner, UsersService $usersService, GameScoresService $gameScoreService, TaskScheduler $scheduler) {
        parent::__construct($owner, $usersService, $gameScoreService, $scheduler);
        $this->client = new FragGrenadeClient();
        $this->grenade = new FragGrenade();
    }

    public function explode(GrenadeEntity $grenadeEntity, Closure $onExploded) {
        $func = function () use ($onExploded, $grenadeEntity) {
            $this->client->explodeParticle($this->owner->getLevel(), $grenadeEntity->getPosition());
            $this->client->playSound($this->owner->getLevel(), $grenadeEntity->getPosition());
            foreach ($this->owner->getLevel()->getEntities() as $entity) {
                if ($entity instanceof SandbagEntity) {
                    if ($entity->getPosition()->distance($grenadeEntity->getPosition()) < 4) {
                        $ownerUser = $this->usersService->getUserData($entity->getOwnerName());
                        $attackerUser = $this->usersService->getUserData($this->owner->getName());
                        if ($ownerUser->getName() === $entity->getOwnerName()) {
                            $entity->kill();
                            return;
                        }
                        if ($ownerUser->getBelongTeamId() === null || $attackerUser->getBelongTeamId() === null) {
                            $entity->kill();
                            return;
                        }
                        if (!$ownerUser->getBelongTeamId()->equal($attackerUser->getBelongTeamId())) {
                            $entity->kill();
                            return;
                        }
                    }
                }
            }
            $onExploded();
        };
        parent::explode($grenadeEntity, $func);
    }

    public function effectOn(Player $player, int $distance): void {
        if ($distance <= 3) {
            GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, FragGrenade::NAME, 20);
        } else {
            GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, FragGrenade::NAME, 15 - $distance);
        }
    }
}