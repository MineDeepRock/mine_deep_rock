<?php


namespace game_system\pmmp\command;


use game_system\listener\UsersListener;
use game_system\listener\WeaponListener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class RankingCommand extends Command
{
    private $weaponListener;

    public function __construct(Plugin $owner, WeaponListener $weaponListener) {
        parent::__construct("ranking", "", "");
        $this->setPermission("Ranking.Command");
        $this->weaponListener = $weaponListener;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (count($args) === 0) {
            $sender->sendMessage("/ranking [weaponName]");
            return true;
        }

        if ($sender instanceof Player) {
            $weaponName = $args[0];
            $limit = 10;
            if (count($args) >= 2) {
                $limit = intval($args[1]);
            }
            $this->weaponListener->displayRanking($sender, $weaponName, $limit);
        }
        return true;
    }
}