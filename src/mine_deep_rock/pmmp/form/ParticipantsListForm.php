<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use team_game_system\data_model\PlayerData;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class ParticipantsListForm extends SimpleForm
{
    static function execute(): void {

    }

    public function __construct(Game $game) {

        $teams = $game->getTeams();
        $buttons = array_map(function (PlayerData $participant) use ($teams) {
            $participantTeam = null;
            foreach ($teams as $team) {
                if ($participant->getTeamId()->equals($team->getId())) $participantTeam = $team;
            }

            return new SimpleFormButton(
                $participantTeam->getTeamColorFormat() . $participant->getName(),
                null,
                function (Player $player) {});
        }, TeamGameSystem::getGamePlayersData($game->getId()));


        parent::__construct("参加者リスト", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {}
}