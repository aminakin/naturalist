<?

use \Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    'add_object_bronevik',
    [
        'Local\AddObjectBronevik\Lib\AddObjectBronevikJsonParserManager' => 'lib/services/add_object_bronevik_json_parser_manager.php',
        'Local\AddObjectBronevik\Parser\IAddObjectBronevikParser' => 'parser/i_add_object_bronevik_parser.php',
        'Local\AddObjectBronevik\Parser\AddObjectBronevikJsonLineParser' => 'parser/add_object_bronevik_json_line_parser.php',
        'Local\AddObjectBronevik\Parser\IAddObjectBronevikJsonLinePosition' => 'parser/i_add_object_bronevik_json_line_position.php',
        'Local\AddObjectBronevik\Data\AdvanceHotelDTO' => 'data/advance_hotel_dto.php',

        'Local\AddObjectBronevik\Repository\IAddObjectBronevikRepository' => 'repository/i_add_object_bronevik_repository.php',
        'Local\AddObjectBronevik\Repository\AddObjectBronevikLoadFileRepository' => 'repository/add_object_bronevik_load_file_repository.php',
        'Local\AddObjectBronevik\Repository\AddObjectBronevikMysqlHotelRepository' => 'repository/add_object_bronevik_mysql_hotel_repository.php',

        'Local\AddObjectBronevik\Orm\AddObjectBronevikTable' => 'orm/add_object_bronevik_table.php',
        'Local\AddObjectBronevik\Agents\AddObjectBronevikParserAgent' => 'agents/add_object_bronevik_parser_agent.php',
        'Local\AddObjectBronevik\Controller\Hotel' => 'lib/controller/hotel.php',
        'Local\AddObjectBronevik\Lib\HotelService' => 'lib/services/hotel_service.php',
    ],
);
global $DBType, $MESS;

CModule::IncludeModule("iblock");
IncludeModuleLangFile(__FILE__);

?>
