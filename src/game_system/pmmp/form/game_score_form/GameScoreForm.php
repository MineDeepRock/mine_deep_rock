<?php


namespace game_system\pmmp\form\game_score_form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class GameScoreForm implements Form
{
    private $scores;

    public function __construct(array $scores) {
        $this->scores = $scores;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }
    }

    public function jsonSerialize() {
        $text = "";
        foreach ($this->scores as $score) {
            $text .= $score->toString() . "\n";
        }

        return [
            'type' => 'form',
            'title' => "スコア",
            'content' => $text,
            'buttons' => [
                ["text" => "閉じる"],
            ]
        ];
    }
}