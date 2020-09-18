<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\utils\TextFormat;

class ColorTextToString
{
    static function execute(string $text): string {
        switch ($text) {
            case TextFormat::BLACK:
                return "black";

            case TextFormat::DARK_BLUE:
                return "dark_blue";

            case TextFormat::DARK_GREEN:
                return "dark_green";

            case TextFormat::DARK_AQUA:
                return "dark_aqua";

            case TextFormat::DARK_RED:
                return "dark_red";

            case TextFormat::DARK_PURPLE:
                return "dark_purple";

            case TextFormat::GOLD:
                return "gold";

            case TextFormat::GRAY:
                return "gray";

            case TextFormat::DARK_GRAY:
                return "dark_gray";

            case TextFormat::BLUE:
                return "blue";

            case TextFormat::GREEN:
                return "green";

            case TextFormat::AQUA:
                return "aqua";

            case TextFormat::RED:
                return "red";

            case TextFormat::LIGHT_PURPLE:
                return "light_purple";

            case TextFormat::YELLOW:
                return "yellow";

            case TextFormat::WHITE:
                return "white";
        }

        return "";
    }
}