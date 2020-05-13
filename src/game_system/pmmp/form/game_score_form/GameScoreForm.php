<?php


namespace game_system\pmmp\form\game_score_form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GameScoreForm implements Form
{
    private $redTeamScores;
    private $blueTeamScores;

    public function __construct(array $redTeamScores , array $blueTeamScores) {
        $this->redTeamScores = $redTeamScores;
        $this->blueTeamScores = $blueTeamScores;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }
    }

    public function jsonSerialize() {

        $redTeamScoresText = "名前 キル数 ポイント\n";
        $redTeamScoresText .= TextFormat::RED . "RedTeam\n";
        foreach ($this->redTeamScores as $score) {
            $score->setName(TextFormat::RED . $score->getName());
            $redTeamScoresText .= $score->toString() . "\n";
        }

        $blueTeamScoresText = TextFormat::WHITE . "名前 キル数 ポイント\nn";
        $blueTeamScoresText .= TextFormat::BLUE . "BlueTeam\n";
        foreach ($this->blueTeamScores as $score) {
            $score->setName(TextFormat::BLUE . $score->getName());
            $blueTeamScoresText .= $score->toString() . "\n";
        }

        return [
            'type' => 'form',
            'title' => "スコア",
            'content' => $redTeamScoresText . "\n" . $blueTeamScoresText,
            'buttons' => [
                ["text" => "閉じる"],
            ]
        ];
    }
}