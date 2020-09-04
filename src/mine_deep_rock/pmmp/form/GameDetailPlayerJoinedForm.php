<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\service\GenerateGameDescriptionService;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\GameType;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class GameDetailPlayerJoinedForm extends SimpleForm
{

    public function __construct(Player $player) {
        $playerData = TeamGameSystem::getPlayerData($player);
        $game = TeamGameSystem::getGame($playerData->getGameId());

        $buttons = [];

        $onMove = function (Player $player, GameType $gameTyp, ?Team $team, bool $result) {
            if ($result) {
                $level = Server::getInstance()->getLevelByName("lobby");
                $player->sendMessage(strval($gameTyp) . "の" . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . "に移動しました");
                foreach ($level->getPlayers() as $lobbyPlayer) {
                    $lobbyPlayer->sendMessage(
                        $player->getName() . "が" . strval($gameTyp) . "の" . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . "に移動しました"
                    );
                }

            } else {
                $player->sendMessage("移動できませんでした");
            }
        };

        foreach ($game->getTeams() as $team) {
            $buttons[] = new SimpleFormButton(
                $team->getTeamColorFormat() . $team->getName(),
                null,
                function (Player $player) use ($game, $onMove, $team) {
                    $result = TeamGameSystem::moveTeam($player, $game->getId(), $team->getId());
                    $onMove($player, $game->getType(), $team, $result);
                }
            );
        }

        $buttons[] = new SimpleFormButton(
            "抜ける",
            null,
            function (Player $player) {
                TeamGameSystem::quitGame($player);
                $player->sendMessage("ゲームから抜けました");
            }
        );

        parent::__construct(
            "参加中のゲーム",
            GenerateGameDescriptionService::execute($game),
            $buttons);
    }

    function onClickCloseButton(Player $player): void {}
}