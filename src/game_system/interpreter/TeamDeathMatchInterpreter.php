<?php


namespace game_system\interpreter;


use game_system\model\User;
use pocketmine\Player;
use pocketmine\Server;

class TeamDeathMatchInterpreter extends TwoTeamGameInterpreter
{
    protected function onDead(Player $attackerPlayer, string $attackerWeaponName, Player $targetPlayer, User $attackerUser, User $targetUser): void {
        if ($attackerPlayer->getName() !== $targetPlayer->getName()) {
            $this->addScoreByKilling($attackerUser);
        }

        parent::onDead($attackerPlayer, $attackerWeaponName, $targetPlayer, $attackerUser, $targetUser);
    }

    public function addScoreByKilling(User $attacker): void {

        $attackerTeamId = $attacker->getBelongTeamId();
        if ($attackerTeamId->equal($this->game->getRedTeam()->getId())) {
            $players = Server::getInstance()->getLevelByName($this->game->getMap()->getName())->getPlayers();
            foreach ($players as $player) {
                $this->client->updateRedTeamScoreboard($player, ++$this->game->redTeamScore);
            }
        } else {
            $players = Server::getInstance()->getLevelByName($this->game->getMap()->getName())->getPlayers();
            foreach ($players as $player) {
                $this->client->updateBlueTeamScoreboard($player, ++$this->game->blueTeamScore);
            }
        }
    }
}