<?php

namespace Local\AddObjectBronevik\Agents;

use Local\AddObjectBronevik\Lib\AddObjectBronevikJsonParserManager;
use Local\AddObjectBronevik\Parser\AddObjectBronevikJsonLineParser;
use Local\AddObjectBronevik\Repository\AddObjectBronevikMysqlHotelRepository;

class AddObjectBronevikParserAgent
{
    public static function parser()
    {
        $writer = new AddObjectBronevikMysqlHotelRepository();
        $parser = new AddObjectBronevikJsonLineParser();
        $parserManager = new AddObjectBronevikJsonParserManager();
        $parserManager->setParser($parser);
        $parserManager->setWriter($writer);
        $parserManager->parse();

        return __CLASS__ . '::parser()';
    }
}