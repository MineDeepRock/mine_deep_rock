<?php


namespace mine_deep_rock\pmmp\listener;


use gun_system\pmmp\event\BulletHitBlockEvent;
use gun_system\service\CalculateDamageService;
use mine_deep_rock\pmmp\block\CoreBlock;
use mine_deep_rock\pmmp\event\CoreBlockBrokeEvent;
use mine_deep_rock\pmmp\event\CoreBrokeEvent;
use mine_deep_rock\pmmp\scoreboard\CorePvPGameScoreboard;
use mine_deep_rock\store\CoresStore;
use pocketmine\block\Air;
use pocketmine\block\EndStone;
use pocketmine\block\Redstone;
use pocketmine\event\Listener;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_game_system\model\Score;
use team_game_system\pmmp\event\FinishedGameEvent;
use team_game_system\TeamGameSystem;

class CorePvPListener implements Listener
{
    /**
     * @var TaskScheduler
     */
    private $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onCoreBlockBroke(CoreBlockBrokeEvent $event) {
        $gameId = $event->getGameId();
        $teamId = $event->getTeamId();

        $core = CoresStore::findByTeamId($teamId);
        $core->setHealth($core->getHealth()-1);

        $level = $core->getPosition()->getLevel();
        $pos = $core->getPosition();
        $level->setBlock($pos, new Air());
        $level->addParticle(new DestroyBlockParticle($pos, new CoreBlock()));

        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($level,$pos) : void {
            $level->setBlock($pos, new CoreBlock());
        }), 20 * 0.5);


        $game = TeamGameSystem::getGame($gameId);
        if ($game === null) return;
        foreach (TeamGameSystem::getGamePlayersData($gameId) as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            if ($player === null) continue;
            if (!$player->isOnline()) continue;

            CorePvPGameScoreboard::update($player, $game);
        }
    }

    public function onCoreBroke(CoreBrokeEvent $event) {
        $gameId = $event->getGameId();
        $teamId = $event->getTeamId();

        TeamGameSystem::addScore($gameId, $teamId, new Score(1));
    }

    public function onBulletHitCore(BulletHitBlockEvent $event) {
        $block = $event->getBlock();
        if ($block->getId() !== CoreBlock::getBlockId()) return;

        foreach (CoresStore::getAll() as $core) {
            if ($core->getPosition()->asVector3()->equals($block->asVector3())) {
                $shooterData = TeamGameSystem::getPlayerData($event->getShooter());
                if ($shooterData->getGameId() === null) return;
                if ($shooterData->getTeamId()->equals($core->getTeamId())) return;

                $damage = CalculateDamageService::execute($event->getShooter(), $event->getBlock());
                $core->attackCoreBlock($core->getHealth() - $damage);
            }
        }
    }

    public function onFinishedGame(FinishedGameEvent $event) {
        CoresStore::delete($event->getGame()->getId());
    }
}