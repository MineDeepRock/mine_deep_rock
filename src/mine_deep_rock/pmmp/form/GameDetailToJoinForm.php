<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\service\GenerateGameDescriptionService;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\GameType;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class GameDetailToJoinForm extends SimpleForm
{

    public function __construct(Game $game) {
        $buttons = [];

        $onJoin = function (Player $player, GameType $gameTyp, ?Team $team, bool $result) {
            if ($result) {
                $level = Server::getInstance()->getLevelByName("lobby");
                $player->sendMessage(strval($gameTyp) . "の" . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . "に参加しました");
                foreach ($level->getPlayers() as $lobbyPlayer) {
                    $lobbyPlayer->sendMessage(
                        $player->getName() . "が" . strval($gameTyp) . "の" . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . "に参加しました"
                    );
                }

            } else {
                $player->sendMessage("試合に参加出来ませんでした。\nすでに試合に参加しているか、試合が満員です");
            }
        };

        foreach ($game->getTeams() as $team) {
            $buttons[] = new SimpleFormButton(
                $team->getTeamColorFormat() . $team->getName(),
                null,
                function (Player $player) use ($game, $onJoin, $team) {
                    $result = TeamGameSystem::joinGame($player, $game->getId(), $team->getId());
                    $onJoin($player, $game->getType(), $team, $result);
                }
            );
        }
        
        $buttons[] = new SimpleFormButton(
            "ランダム",
            null,
            function (Player $player) use ($game, $onJoin) {
                $result = TeamGameSystem::joinGame($player, $game->getId());
                $team = null;
                if ($result) {
                    $playerData = TeamGameSystem::getPlayerData($player);
                    $team = TeamGameSystem::getTeam($playerData->getGameId(), $playerData->getTeamId());
                }
                $onJoin($player, $game->getType(), $team, $result);
            }
        );

        parent::__construct(
            "ゲームに参加",
            GenerateGameDescriptionService::execute($game),
            $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new GameListToJoinForm());
    }
}