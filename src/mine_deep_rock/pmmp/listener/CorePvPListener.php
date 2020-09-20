<?php


namespace mine_deep_rock\pmmp\listener;


use gun_system\pmmp\event\BulletHitBlockEvent;
use gun_system\pmmp\service\PlaySoundsService;
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
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
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
        $attackerData = TeamGameSystem::getPlayerData($event->getPlayer());
        $attackerTeam = TeamGameSystem::getTeam($gameId, $attackerData->getTeamId());

        $core = CoresStore::findByTeamId($teamId);
        $coreTeam = TeamGameSystem::getTeam($gameId, $teamId);

        //メッセージと音
        $playerDataList = TeamGameSystem::getGamePlayersData($gameId);
        foreach ($playerDataList as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            if ($playerData->getTeamId()->equals($core->getTeamId())) {
                $player->sendTip("コアが攻撃されています");
            } else {
                $player->sendTip($attackerTeam->getTeamColorFormat() . $event->getPlayer()->getName() . TextFormat::RESET . "が" . $coreTeam->getTeamColorFormat() . $coreTeam->getName() . TextFormat::RESET . "を攻撃中");
            }
            $this->playSound($player, $core->getPosition(), "random.anvil_land");
        }

        $core->attack($event->getPlayer(), 1);

        //パーティクル
        $level = $core->getPosition()->getLevel();
        $pos = $core->getPosition();
        $level->setBlock($pos, new Air());
        $level->addParticle(new DestroyBlockParticle($pos, new CoreBlock()));
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($level, $pos) : void {
            $level->setBlock($pos, new CoreBlock());
        }), 20 * 0.5);


        //Scoreboard
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
        $player = $event->getPlayer();
        $gameId = $event->getGameId();

        $playerData = TeamGameSystem::getPlayerData($player);
        TeamGameSystem::addScore($gameId, $playerData->getTeamId(), new Score(1));
    }

    public function onBulletHitCore(BulletHitBlockEvent $event) {
        $block = $event->getBlock();
        if ($block->getId() !== CoreBlock::getBlockId()) return;

        foreach (CoresStore::getAll() as $core) {
            if ($core->getPosition()->asVector3()->equals($block->asVector3())) {
                $shooterData = TeamGameSystem::getPlayerData($event->getShooter());
                if ($shooterData->getGameId() === null) return;
                if ($shooterData->getTeamId()->equals($core->getTeamId())) return;

                $this->playSound($event->getShooter(), $event->getShooter(), "random.break");
                $damage = CalculateDamageService::execute($event->getShooter(), $event->getBlock());
                $core->attackCoreBlock($event->getShooter(), $damage * 5);
            }
        }
    }

    public function onFinishedGame(FinishedGameEvent $event) {
        CoresStore::delete($event->getGame()->getId());
    }

    private function playSound(Player $player, Vector3 $pos, string $name) {
        $packet = new PlaySoundPacket();
        $packet->x = $pos->x;
        $packet->y = $pos->y;
        $packet->z = $pos->z;
        $packet->volume = 50;
        $packet->pitch = 1;
        $packet->soundName = $name;
        $player->sendDataPacket($packet);
    }
}