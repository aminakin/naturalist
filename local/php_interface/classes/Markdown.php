<?php
namespace Naturalist;

class Markdown {
    public static function arrayToMarkdown(array $array, int $level = 0):string {
        $markdown = '';
        $indent = str_repeat('  ', $level);

        foreach ($array as $key => $value) {
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