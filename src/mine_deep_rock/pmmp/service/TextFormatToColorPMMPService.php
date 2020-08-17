<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\utils\Color;

class TextFormatToColorPMMPService
{
    static function execute($text): Color {
        if (strpos("§0", $text) !== false) return Color::fromRGB(0x000000);
        if (strpos("§1", $text) !== false) return Color::fromRGB(0x0000AA);
        if (strpos("§2", $text) !== false) return Color::fromRGB(0x00AA00);
        if (strpos("§3", $text) !== false) return Color::fromRGB(0x00AAAA);
        if (strpos("§4", $text) !== false) return Color::fromRGB(0xAA0000);
        if (strpos("§5", $text) !== false) return Color::fromRGB(0xAA00AA);
        if (strpos("§6", $text) !== false) return Color::fromRGB(0xFFAA00);
        if (strpos("§7", $text) !== false) return Color::fromRGB(0xAAAAAA);
        if (strpos("§8", $text) !== false) return Color::fromRGB(0x555555);
        if (strpos("§9", $text) !== false) return Color::fromRGB(0x5555FF);
        if (strpos("§a", $text) !== false) return Color::fromRGB(0x55FF55);
        if (strpos("§b", $text) !== false) return Color::fromRGB(0x55FFFF);
        if (strpos("§c", $text) !== false) return Color::fromRGB(0xFF5555);
        if (strpos("§d", $text) !== false) return Color::fromRGB(0xFF55FF);
        if (strpos("§e", $text) !== false) return Color::fromRGB(0xFFFF55);
        if (strpos("§f", $text) !== false) return Color::fromRGB(0xFFFFFF);
        return new Color(0, 0, 0);
    }
}