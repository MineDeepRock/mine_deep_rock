<?php


namespace game_system\interpreter;


use game_system\model\User;
use pocketmine\Player;

class TeamDeathMatchInterpreter extends TwoTeamGameInterpreter
{
    protected function onDead(Player $attackerPlayer, string $attackerWeaponName, Player $targetPlayer, User $attackerUser, User $targetUser): void {
        $this->addScoreByKilling($attackerUser);
        parent::onDead($attackerPlayer, $attackerWeaponName, $targetPlayer, $attackerUser, $targetUser);
    }

    public function addScoreByKilling(User $attacker): void {
        $attackerTeamId = $attacker->getBelongTeamId();
        if ($attackerTeamId->equal($this->game->getRedTeam()->getId())) {
            $this->client->updateRedTeamScoreboard(++$this->game->redTeamScore, $this->game->getMap()->getName());
        } else {
            $this->client->updateBlueTeamScoreboard(++$this->game->blueTeamScore, $this->game->getMap()->getName());
        }
    }
}