<?php


namespace game_system\pmmp\client;


use Client;
use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\Coordinate;
use game_system\model\Team;
use game_system\model\TeamId;
use game_system\model\User;
use game_system\pmmp\WorldController;
use gun_system\pmmp\GunSounds;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TeamDeathMatchClient extends Client
{
    public function start(Array $participants, TeamId $redTeamId, int $redTeamScore, int $blueTeamScore): void {
        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());
            $playerName = $participant->getName();

            if ($participant->getBelongTeamId()->equal($redTeamId)) {
                $player->setNameTag(TextFormat::RED . $playerName);
                $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
            } else {
                $player->setNameTag(TextFormat::BLUE . $playerName);
                $player->sendMessage(TextFormat::BLUE . "あなたは青チームです");
            }

            $api = EasyScoreboardAPI::getInstance();
            $api->sendScoreboard($player, "sidebar", "TeamDeathMatch", false);
            $api->setScore($player, "sidebar", "RedTeamScore:", $redTeamScore, 2);
            $api->setScore($player, "sidebar", "BlueTeamScore:", $blueTeamScore, 3);
        }
    }

    public function onFinish(Team $winTeam, array $participants): void {
        $worldController = new WorldController();
        $winTeamId = $winTeam->getId();

        foreach ($participants as $participant) {
            EasyScoreboardAPI::getInstance()->allremove($participant->getName());
            $player = Server::getInstance()->getPlayer($participant->getName());
            $player->getInventory()->setContents([]);
            $worldController->teleport($player, "world");

            if ($participant->getBelongTeamId()->equal($winTeamId)) {
                $player->addTitle("勝利!!");
            } else {
                $player->addTitle("負け");
            }
        }
    }

    public function joinOnTheWay(User $user, TeamId $redTeamId, int $redTeamScore, int $blueTeamScore): void {
        $playerName = $user->getName();
        $player = Server::getInstance()->getPlayer($playerName);

        if ($user->getBelongTeamId()->equal($redTeamId)) {
            $player->setNameTag(TextFormat::RED . $playerName);
            $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
        } else {
            $player->setNameTag(TextFormat::BLUE . $playerName);
            $player->sendMessage(TextFormat::BLUE . "あなたは青チームです");
        }

        $api = EasyScoreboardAPI::getInstance();
        $api->sendScoreboard($player, "sidebar", "TeamDeathMatch", false);
        $api->setScore($player, "sidebar", "RedTeamScore:", $redTeamScore, 2);
        $api->setScore($player, "sidebar", "BlueTeamScore:", $blueTeamScore, 3);
    }

    public function spawn(string $userName, string $selectedWeaponName, string $mapName, Coordinate $coordinate) {
        $worldController = new WorldController();
        $player = Server::getInstance()->getPlayer($userName);

        $player->getInventory()->setContents([]);
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun give " . $userName . " " . $selectedWeaponName);
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "give " . $userName . " 364");
        $worldController->teleport($player, $mapName);
        $player->teleport(new Vector3($coordinate->getX(), $coordinate->getY(), $coordinate->getZ()));
    }

    public function onReceiveDamage(Player $attacker, Entity $target, int $damage, string $weaponName): void {
        $health = $target->getHealth() - $damage;
        $target->getLevel()->addParticle(new AngryVillagerParticle(
            new Vector3(
                $target->getX() + rand(1, 2),
                $target->getY() + rand(3, 5),
                $target->getZ() + rand(1, 2)
            )
        ));

        GunSounds::play(Server::getInstance()->getPlayer($target->getName()), GunSounds::bulletHitPlayer(), 10, 1);

        if ($health <= 0) {
            $target->setHealth(20);
            $players = $attacker->getLevel()->getPlayers();
            foreach ($players as $player) {
                $player->sendMessage($attacker->getName() . " が " . $target->getName() . " を倒した [" . $weaponName . "]");
            }
            $attacker->addTitle(TextFormat::RED . "><", "", 0, 1, 0);

        } else {
            $target->setHealth($health);
            $attacker->addTitle("><", "", 0, 1, 0);

        }
    }

    public function displayRemainingTime(int $time, string $mapName): void {
        $players = Server::getInstance()->getLevelByName($mapName)->getPlayers();

        $api = EasyScoreboardAPI::getInstance();
        foreach ($players as $player) {
            $api->setScore($player, "sidebar", "残り時間:", $time, 1);
        }
    }

    public function updateRedTeamScoreboard(int $score, string $mapName): void {
        $players = Server::getInstance()->getLevelByName($mapName)->getPlayers();
        $api = EasyScoreboardAPI::getInstance();

        foreach ($players as $player) {
            $api->setScore($player, "sidebar", "RedTeamScore:", $score, 2);
        }
    }

    public function updateBlueTeamScoreboard(int $score, string $mapName): void {
        $players = Server::getInstance()->getLevelByName($mapName)->getPlayers();
        $api = EasyScoreboardAPI::getInstance();

        foreach ($players as $player) {
            $api->setScore($player, "sidebar", "RedTeamScore:", $score, 2);
        }
    }

    public function quitGame(string $userName): void {
        $player = Server::getInstance()->getPlayer($userName);
        if ($player->isOnline()) {
            $player->getInventory()->setContents([]);
            $worldController = new WorldController();
            $worldController->teleport($player, "world");
            EasyScoreboardAPI::getInstance()->deleteScoreboard($player, "sidebar");
        }
    }
}