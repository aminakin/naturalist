<?php
namespace Naturalist;

class Markdown {
    public static function escapeMarkdownV2($text) {
        return preg_replace_callback('/([._*[\]()~`>#+\-=|{}])/u', function ($matches) {
            return '\\' . $matches[0];
        }, $text);
    }

    public static function arrayToMarkdown($input, int $level = 0): string {
        if (is_string($input)) {
            json_decode($input);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = json_decode($input, true);
            }
        }

        if (!is_array($input)) {
            return "параметр не является массивом";
        }

        $markdown = '';
        $indent = str_repeat('  ', $level);

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $markdown .= "{$indent}*{$key}:*\n";
                $markdown .= self::arrayToMarkdown($value, $level + 1);
            } else {
                $markdown .= "{$indent}*{$key}:* {$value}\n";
            }
        }

        return $markdown;
    }
}