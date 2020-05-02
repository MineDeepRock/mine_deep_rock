<?php


namespace game_system\pmmp\client;


use Client;
use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\Coordinate;
use game_system\model\Team;
use game_system\model\TeamId;
use game_system\model\User;
use game_system\pmmp\items\AttachmentSelectItem;
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
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TeamDeathMatchClient extends Client
{
    public function start(Array $participants, TeamId $redTeamId, int $redTeamScore, int $blueTeamScore): void {
        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());

            $this->setArmorAndNameTag($player, $participant->getBelongTeamId(), $redTeamId);
            if ($participant->getBelongTeamId()->equal($redTeamId)) {
                $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
            } else {
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
            $player = Server::getInstance()->getPlayer($participant->getName());
            EasyScoreboardAPI::getInstance()->deleteScoreboard($player, "sidebar");
            $player->getInventory()->setContents([]);
            $player->setGamemode(Player::ADVENTURE);
            $worldController->teleport($player, "lobby");

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

        $this->setArmorAndNameTag($player, $user->getBelongTeamId(), $redTeamId);
        if ($user->getBelongTeamId()->equal($redTeamId)) {
            $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
        } else {
            $player->sendMessage(TextFormat::BLUE . "あなたは青チームです");
        }

        $api = EasyScoreboardAPI::getInstance();
        $api->sendScoreboard($player, "sidebar", "TeamDeathMatch", false);
        $api->setScore($player, "sidebar", "RedTeamScore:", $redTeamScore, 2);
        $api->setScore($player, "sidebar", "BlueTeamScore:", $blueTeamScore, 3);
    }

    public function spawn(string $userName, string $selectedWeaponName, string $mapName, Coordinate $coordinate): void {
        $player = Server::getInstance()->getPlayer($userName);
        if ($player !== null) {

            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), null, 1, false));

            $player->getInventory()->setContents([]);
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun give " . $userName . " " . $selectedWeaponName);

            $player->getInventory()->addItem(ItemFactory::get(Item::COOKED_BEEF, 0, 64));
            $player->getInventory()->addItem(new AttachmentSelectItem());

            $worldController = new WorldController();
            $worldController->teleport($player, $mapName);
            $player->teleport(new Vector3($coordinate->getX(), $coordinate->getY(), $coordinate->getZ()));
        }
    }

    public function onReceiveDamage(Player $attacker, Player $targetPlayer, int $damage, string $weaponName): void {
        $health = $targetPlayer->getHealth() - $damage;

        GunSounds::play($targetPlayer, GunSounds::bulletHitPlayer(), 10, 1);

        if ($health <= 0) {
            $targetPlayer->setHealth(20);
            $players = $attacker->getLevel()->getPlayers();

            //TODO:Titleにしたい
            $targetPlayer->sendPopup(TextFormat::RED . $attacker->getName() . "に倒された");
            $targetPlayer->getInventory()->setContents([]);
            foreach ($players as $player) {
                $player->sendMessage($attacker->getName() . " が " . $targetPlayer->getName() . " を倒した [" . $weaponName . "]");
            }
            $attacker->addTitle(TextFormat::RED . "><", "", 0, 1, 0);

        } else {
            $targetPlayer->setHealth($health);
            $attacker->addTitle("><", "", 0, 1, 0);

        }
    }

    public function setArmorAndNameTag(Player $player, TeamID $userTeamId, TeamId $redTeamId) {
        if ($userTeamId->equal($redTeamId)) {
            $player->setNameTag(TextFormat::RED . $player->getName());
            $player->getArmorInventory()->setHelmet(new IronHelmet());
            $player->getArmorInventory()->setChestplate(new IronChestplate());
            $player->getArmorInventory()->setLeggings(new IronLeggings());
            $player->getArmorInventory()->setBoots(new IronBoots());
        } else {
            $player->setNameTag(TextFormat::RED . $player->getName());
            $player->getArmorInventory()->setHelmet(new GoldHelmet());
            $player->getArmorInventory()->setChestplate(new GoldChestplate());
            $player->getArmorInventory()->setLeggings(new GoldLeggings());
            $player->getArmorInventory()->setBoots(new GoldBoots());
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
            $api->setScore($player, "sidebar", "BlueTeamScore:", $score, 3);
        }
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