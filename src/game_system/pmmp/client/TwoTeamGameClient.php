<?php


namespace game_system\pmmp\client;


use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\Coordinate;
use game_system\model\Team;
use game_system\model\TeamId;
use game_system\model\User;
use game_system\model\Weapon;
use game_system\pmmp\form\game_score_form\GameScoreForm;
use game_system\pmmp\items\MilitaryDepartmentSelectItem;
use game_system\pmmp\items\SpawnItem;
use game_system\pmmp\items\SubWeaponSelectItem;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\pmmp\WorldController;
use gun_system\pmmp\GunSounds;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\GoldBoots;
use pocketmine\item\GoldChestplate;
use pocketmine\item\GoldHelmet;
use pocketmine\item\GoldLeggings;
use pocketmine\item\IronBoots;
use pocketmine\item\IronChestplate;
use pocketmine\item\IronHelmet;
use pocketmine\item\IronLeggings;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TwoTeamGameClient
{
    private $gameName;

    public function __construct(string $gameName) {
        $this->gameName = $gameName;
    }


    public function start(array $participants, TeamId $redTeamId, int $redTeamScore, int $blueTeamScore): void {
        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());

            if ($participant->getBelongTeamId()->equal($redTeamId)) {
                $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
            } else {
                $player->sendMessage(TextFormat::BLUE . "あなたは青チームです");
            }
            $this->displayBaseGameScoreboard($player, $redTeamScore, $blueTeamScore);
        }
    }

    public function onFinish(Team $winTeam, array $participants, array $redTeamScores, array $blueTeamScores): void {
        $worldController = new WorldController();
        $winTeamId = $winTeam->getId();

        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());
            $api = EasyScoreboardAPI::getInstance();
            $api->deleteScoreboard($player, "sidebar");

            $player->getInventory()->addItem(new MilitaryDepartmentSelectItem());
            $player->getInventory()->addItem(new WeaponSelectItem());
            $player->getInventory()->addItem(new SubWeaponSelectItem());

            $player->getInventory()->setContents([]);
            $player->setGamemode(Player::ADVENTURE);
            $worldController->teleport($player, "lobby");

            if ($participant->getBelongTeamId()->equal($winTeamId)) {
                $player->addTitle("勝利!!");
            } else {
                $player->addTitle("負け");
            }
            $player->sendForm(new GameScoreForm($redTeamScores, $blueTeamScores));
        }
    }

    public function joinOnTheWay(User $user, TeamId $redTeamId, int $redTeamScore, int $blueTeamScore): void {
        $playerName = $user->getName();
        $player = Server::getInstance()->getPlayer($playerName);

        if ($user->getBelongTeamId()->equal($redTeamId)) {
            $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
        } else {
            $player->sendMessage(TextFormat::BLUE . "あなたは青チームです");
        }
        $this->displayBaseGameScoreboard($player, $redTeamScore, $blueTeamScore);
    }

    //TODO:リファクタリング
    public function spawn(
        Player $player,
        TeamId $teamId,
        TeamId $redTeamId,
        string $tag,
        array $gadgetSpawnItems,
        array $effects,
        Weapon $selectedWeapon,
        string $selectedWeaponType,
        Weapon $selectedSubWeapon,
        string $selectedSubWeaponType,
        string $mapName,
        Coordinate $coordinate): void {

        if ($player === null) return;

        $player->removeAllEffects();
        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::HEALING), 20 * 5, 4, false));

        foreach ($effects as $effect) $player->addEffect($effect);

        $this->setArmorAndNameTag($player, $tag, $teamId, $redTeamId);

        $player->getInventory()->setContents([]);
        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun give \"" . $player->getName() . "\" " . $selectedWeapon->getName() . " " . $selectedWeapon->getScope());

        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun give \"" . $player->getName() . "\" " . $selectedSubWeapon->getName() . " " . $selectedSubWeapon->getScope());

        foreach ($gadgetSpawnItems as $gadgetSpawnItem) {
            $player->getInventory()->addItem($gadgetSpawnItem);
        }

        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun ammo \"" . $player->getName() . "\" " . $selectedWeaponType);

        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun ammo \"" . $player->getName() . "\" " . $selectedSubWeaponType);

        $player->getInventory()->addItem(ItemFactory::get(Item::COOKED_BEEF, 0, 64));

        $worldController = new WorldController();
        $worldController->teleport($player, $mapName);
        $player->teleport(new Vector3($coordinate->getX(), $coordinate->getY(), $coordinate->getZ()));
    }

    public function scare(Player $target, array $effects, Item $item) {
        $target->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20 * 3, 1, false));
        foreach ($effects as $effect) {
            if ($effect->getId() !== Effect::HEALTH_BOOST) {
                $target->removeEffect($effect->getId());
            }
        }

        $item->scare(function () use ($target, $effects) {
            if ($target->isOnline()) {
                foreach ($effects as $effect) {
                    if ($effect->getId() !== Effect::HEALTH_BOOST) {
                        $target->addEffect($effect);
                    }
                }
            }
        });
    }

    public function onReceiveDamage(Player $attacker, Player $targetPlayer, float $damage, string $weaponName, TaskScheduler $scheduler): void {
        $health = $targetPlayer->getHealth() - $damage;

        GunSounds::play($targetPlayer, GunSounds::bulletHitPlayer(), 10, 1);

        if ($health <= 0) {
            $this->onDead($attacker, $targetPlayer, $weaponName, $scheduler);
            $attacker->addTitle(TextFormat::RED . "><", "", 0, 1, 0);
        } else {
            $targetPlayer->setHealth($health);
            $attacker->addTitle("><", "", 0, 1, 0);
        }
    }

    private function onDead(Player $attacker, Player $target, string $weaponName, TaskScheduler $scheduler) {
        $target->setGamemode(Player::SPECTATOR);
        $target->getInventory()->setContents([]);
        $target->teleport(new Vector3(
            $attacker->getX(),
            $attacker->getY() + 4,
            $attacker->getZ()
        ));

        //TODO:Titleにしたい
        $target->addTitle(TextFormat::RED . $attacker->getName() . "に倒された");

        $players = $attacker->getLevel()->getPlayers();
        foreach ($players as $player) {
            $player->sendMessage($attacker->getNameTag() . TextFormat::WHITE . " " . $weaponName . " " . $target->getNameTag());
        }

        $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($target): void {
            $target->setGamemode(Player::SPECTATOR);
            $target->setHealth(20);
            $target->getInventory()->addItem(new MilitaryDepartmentSelectItem());
            $target->getInventory()->addItem(new WeaponSelectItem());
            $target->getInventory()->addItem(new SubWeaponSelectItem());
            $target->getInventory()->addItem(new SpawnItem());
        }), 20 * 5);
    }

    public function setArmorAndNameTag(Player $player, string $tag, TeamID $userTeamId, TeamId $redTeamId) {
        $player->setNameTagAlwaysVisible(false);
        if ($userTeamId->equal($redTeamId)) {
            $player->setNameTag(TextFormat::RED . "[{$tag}]" . $player->getName());
            $player->getArmorInventory()->setHelmet(new IronHelmet());
            $player->getArmorInventory()->setChestplate(new IronChestplate());
            $player->getArmorInventory()->setLeggings(new IronLeggings());
            $player->getArmorInventory()->setBoots(new IronBoots());
        } else {
            $player->setNameTag(TextFormat::BLUE . "[{$tag}]" . $player->getName());
            $player->getArmorInventory()->setHelmet(new GoldHelmet());
            $player->getArmorInventory()->setChestplate(new GoldChestplate());
            $player->getArmorInventory()->setLeggings(new GoldLeggings());
            $player->getArmorInventory()->setBoots(new GoldBoots());
        }
    }

    public function displayBaseGameScoreboard(Player $player, $redTeamScore, $blueTeamScore): void {
        $api = EasyScoreboardAPI::getInstance();
        $api->sendScoreboard($player, "sidebar", $this->gameName, false);
        $api->setScore($player, "sidebar", "============", 0, 0);
        $this->updateRedTeamScoreboard($player, $redTeamScore);
        $this->updateBlueTeamScoreboard($player, $blueTeamScore);
    }

    public function displayParticipantCount(int $count) {
        $lobbyPlayers = Server::getInstance()->getLevelByName("lobby")->getPlayers();
        $api = EasyScoreboardAPI::getInstance();
        foreach ($lobbyPlayers as $player) {
            if (!$api->hasScoreboard($player, "sidebar")) {
                $api->sendScoreboard($player, "sidebar", "MineDeepRock", false);
                $api->setScore($player, "sidebar", "============", 0, 0);
            }
            $api->removeScore($player, "sidebar", 1);
            $api->setScore($player, "sidebar", "参加人数:" . $count, 1, 1);
        }
    }

    public function displayRemainingTime(int $time, string $mapName): void {
        $players = Server::getInstance()->getLevelByName($mapName)->getPlayers();

        $api = EasyScoreboardAPI::getInstance();
        foreach ($players as $player) {
            $api->removeScore($player, "sidebar", 1);
            $api->setScore($player, "sidebar", "残り時間:" . $time, 1, 1);
        }
    }

    public function updateRedTeamScoreboard(Player $player, int $score): void {
        $api = EasyScoreboardAPI::getInstance();
        $api->removeScore($player, "sidebar", 2);
        $api->setScore($player, "sidebar", TextFormat::RED . "Red:" . TextFormat::WHITE . $score, 2, 2);
    }

    public function updateBlueTeamScoreboard(Player $player, int $score): void {
        $api = EasyScoreboardAPI::getInstance();
        $api->removeScore($player, "sidebar", 3);
        $api->setScore($player, "sidebar", TextFormat::BLUE . "Blue:" . TextFormat::WHITE . $score, 3, 3);
    }

    public function quitGame(string $userName): void {
        $player = Server::getInstance()->getPlayer($userName);
        if ($player->isOnline()) {
            $player->getInventory()->setContents([]);
            $worldController = new WorldController();
            $worldController->teleport($player, "lobby");
            EasyScoreboardAPI::getInstance()->deleteScoreboard($player, "sidebar");
        }
    }
}