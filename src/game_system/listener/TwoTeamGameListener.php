<?php


namespace game_system\listener;


use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\interpreter\TeamDeathMatchInterpreter;
use game_system\interpreter\TeamDominationInterpreter;
use game_system\interpreter\TwoTeamGameInterpreter;
use game_system\model\GameId;
use game_system\model\GameType;
use game_system\model\map\ApocalypticCity;
use game_system\model\map\ApocalypticCityForDomination;
use game_system\model\map\WaterfrontHome;
use game_system\model\TeamDeathMatch;
use game_system\model\TeamDomination;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\pmmp\client\TeamDominationClient;
use game_system\pmmp\Entity\AmmoBoxEntity;
use game_system\pmmp\Entity\BoxEntity;
use game_system\pmmp\Entity\FlareBoxEntity;
use game_system\pmmp\Entity\MedicineBoxEntity;
use game_system\pmmp\form\game_score_form\GameScoreForm;
use game_system\pmmp\items\MilitaryDepartmentSelectItem;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\pmmp\items\SpawnFlareBoxItem;
use game_system\pmmp\items\SpawnMedicineBoxItem;
use game_system\pmmp\items\SubWeaponSelectItem;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\pmmp\WorldController;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TwoTeamGameListener
{

    protected $usersService;
    protected $weaponService;
    protected $interpreter;
    protected $scheduler;
    private $gameScoresService;

    public function __construct(UsersService $usersService, WeaponsService $weaponService, GameScoresService $gameScoresService ,TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->weaponService = $weaponService;
        $this->gameScoresService = $gameScoresService;
        $this->scheduler = $scheduler;
    }

    public function getGameId(): GameId {
        return $this->interpreter->getGameData()->getId();
    }

    public function initGame(GameType $gameType): bool {
        if ($gameType->equal(GameType::TeamDeathMatch())) {
            $this->interpreter = new TeamDeathMatchInterpreter(
                new TeamDeathMatchClient(),
                $this->usersService,
                $this->weaponService,
                $this->gameScoresService,
                $this->scheduler
            );

            $match = new TeamDeathMatch([
                new ApocalypticCity(),
                new WaterfrontHome()][rand(0, 1)]);
            return $this->interpreter->init($match, 600, function () use ($gameType) {
                $this->onFinished($gameType);
            });
        } else if ($gameType->equal(GameType::TeamDomination())) {
            $this->interpreter = new TeamDominationInterpreter(
                new TeamDominationClient(),
                $this->usersService,
                $this->weaponService,
                $this->gameScoresService,
                $this->scheduler
            );
            $match = new TeamDomination(new ApocalypticCityForDomination());
            return $this->interpreter->init($match, 600, function () use ($gameType) {
                $this->onFinished($gameType);
            });
        }

        return false;
    }

    protected function onFinished(GameType $gameType) {
        $this->initGame($gameType);
        $api = EasyScoreboardAPI::getInstance();
        $lobbyPlayers = Server::getInstance()->getLevelByName("lobby")->getPlayers();
        $game = $this->interpreter->getGameData();
        if (!$game->isStarted()) {
            foreach ($lobbyPlayers as $player) {
                $player->getInventory()->addItem(new MilitaryDepartmentSelectItem());
                $player->getInventory()->addItem(new WeaponSelectItem());
                $player->getInventory()->addItem(new SubWeaponSelectItem());
                $api->sendScoreboard($player, "sidebar", "Lobby", false);
                $api->setScore($player, "sidebar", "ゲーム参加人数:", 0, 1);
            }
        }
    }

    public function onReceivedDamage(Player $attacker, Entity $target, string $weaponName, float $damage): void {
        $this->interpreter->onReceiveDamage($attacker, $target, $weaponName, $damage);
    }

    public function startGame(): bool {
        $result = $this->interpreter->start();
        $this->updateNumberOfParticipants();
        return $result;
    }

    public function joinGame(Player $player): void {
        $result = $this->interpreter->join($player->getName());
        $this->updateNumberOfParticipants();
        if (!$result) {
            $player->sendMessage("試合が開かれていないか、すでに参加しています");
            return;
        }

        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        foreach ($onlinePlayers as $onlinePlayer)
            $onlinePlayer->sendMessage($player->getName() . "が試合に参加しました");
    }

    public function quitGame(string $userName): bool {
        $result = $this->interpreter->quitGame($userName);
        $this->updateNumberOfParticipants();
        return $result;
    }

    public function closeGame(): bool {
        return $this->interpreter->closeGame();
    }

    public function updateNumberOfParticipants() {
        $lobbyPlayers = Server::getInstance()->getLevelByName("lobby")->getPlayers();
        $api = EasyScoreboardAPI::getInstance();
        $game = $this->interpreter->getGameData();
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

    public function spawnAmmoBox(Player $player) {
        $player->getInventory()->remove(new SpawnAmmoBoxItem());

        $ammoBox = new AmmoBoxEntity(
            $player->getLevel(),
            $player,
            $this->usersService,
            $this->weaponService,
            $this->gameScoresService,
            $this->scheduler);
        $ammoBox->spawnToAll();
        $this->setBoxNameTag($ammoBox, $player->getName());
    }

    public function spawnMedicineBox(Player $player) {
        $player->getInventory()->remove(new SpawnMedicineBoxItem());
        $medicineBox = new MedicineBoxEntity(
            $player->getLevel(),
            $player,
            $this->usersService,
            $this->gameScoresService,
            $this->scheduler);
        $medicineBox->spawnToAll();
        $this->setBoxNameTag($medicineBox, $player->getName());
    }

    public function spawnFlareBox(Player $player) {
        $player->getInventory()->remove(new SpawnFlareBoxItem());

        $flareBox = new FlareBoxEntity(
            $player->getLevel(),
            $player,
            $this->usersService,
            $this->gameScoresService,
            $this->scheduler);

        $flareBox->spawnToAll();
        $this->setBoxNameTag($flareBox, $player->getName());
    }

    public function onBoxHitBullet(Player $attacker, BoxEntity $boxEntity): void {
        $ownerUser = $this->usersService->getUserData($boxEntity->getOwner()->getName());
        $attackerUser = $this->usersService->getUserData($attacker->getName());
        if ($ownerUser->getBelongTeamId() === null || $attackerUser->getBelongTeamId() === null) $boxEntity->kill();
        if (!$ownerUser->getBelongTeamId()->equal($attackerUser->getBelongTeamId())) $boxEntity->kill();
    }

    private function setBoxNameTag(BoxEntity $entity, string $ownerName) {
        $user = $this->usersService->getUserData($ownerName);
        if ($user->getBelongTeamId() === null) return;
        if ($this->interpreter->getGameData() === null) return;
        if ($user->getBelongTeamId()->equal($this->interpreter->getGameData()->getRedTeam()->getId())) {
            $entity->setNameTag(TextFormat::RED . $entity->getName());
        } else {
            $entity->setNameTag(TextFormat::BLUE . $entity->getName());
        }
        $entity->setNameTagAlwaysVisible(false);
    }

    public function spawnOnTeamDeath(string $playerName) {
        $this->interpreter->spawn($this->usersService->getUserData($playerName));
    }

    public function scare(Player $target, Entity $attacker): void {
        $item = $target->getInventory()->getItemInHand();

        $targetUser = $this->usersService->getUserData($target->getName());
        $attackerUser = $this->usersService->getUserData($attacker->getName());

        $this->interpreter->scare($targetUser, $attackerUser, $item);
    }
}