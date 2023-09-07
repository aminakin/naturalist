<?php

namespace Sprint\Migration;


class Version20230905125135 extends Version
{
    protected $description = "99373 | Поисковая выдача / Доработка внешнего вида подсказок | Перезаполненные объекты в соответствии со справочником регионов";

    protected $moduleVersion = "4.4.1";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->getIblockIdIfExists(
            'catalog',
            'catalog'
        );

        $arSections = array(
                0 =>
                    array(
                        'CODE' => 'usadba_grebnevo',
                        'UF_REGION' => '1',
                    ),
                1 =>
                    array(
                        'CODE' => 'ananda_glamping',
                        'UF_REGION' => '2',
                    ),
                2 =>
                    array(
                        'CODE' => 'yelki-i-spb',
                        'UF_REGION' => '3',
                    ),
                3 =>
                    array(
                        'CODE' => 'set_kottedzhey_sova',
                        'UF_REGION' => '3',
                    ),
                4 =>
                    array(
                        'CODE' => 'saykol_glemping_inegen',
                        'UF_REGION' => '4',
                    ),
                5 =>
                    array(
                        'CODE' => 'luchshee_mesto',
                        'UF_REGION' => '5',
                    ),
                6 =>
                    array(
                        'CODE' => 'dom-na-dereve',
                        'UF_REGION' => '6',
                    ),
                7 =>
                    array(
                        'CODE' => 'enot',
                        'UF_REGION' => '5',
                    ),
                8 =>
                    array(
                        'CODE' => 'solnechnosele',
                        'UF_REGION' => '2',
                    ),
                9 =>
                    array(
                        'CODE' => 'lachi',
                        'UF_REGION' => '1',
                    ),
                10 =>
                    array(
                        'CODE' => 'yurevskoe_podvore',
                        'UF_REGION' => '1',
                    ),
                11 =>
                    array(
                        'CODE' => 'territoriya_otdykha_khevaa',
                        'UF_REGION' => '3',
                    ),
                12 =>
                    array(
                        'CODE' => 'glampoint',
                        'UF_REGION' => '7',
                    ),
                13 =>
                    array(
                        'CODE' => 'novo-okatovo',
                        'UF_REGION' => '19',
                    ),
                14 =>
                    array(
                        'CODE' => 'zvezdnyy_les',
                        'UF_REGION' => '27',
                    ),
                15 =>
                    array(
                        'CODE' => 'eko_park_apartel',
                        'UF_REGION' => '5',
                    ),
                16 =>
                    array(
                        'CODE' => 'barnkhaus_sova',
                        'UF_REGION' => '27',
                    ),
                17 =>
                    array(
                        'CODE' => 'rezidentsiya_villingston',
                        'UF_REGION' => '3',
                    ),
                18 =>
                    array(
                        'CODE' => 'berloga_country_resort',
                        'UF_REGION' => '11',
                    ),
                19 =>
                    array(
                        'CODE' => 'green_village_resort',
                        'UF_REGION' => '27',
                    ),
                20 =>
                    array(
                        'CODE' => 'park_otel_akter_ruza',
                        'UF_REGION' => '1',
                    ),
                21 =>
                    array(
                        'CODE' => 'zaytsevo_kantri_klab',
                        'UF_REGION' => '1',
                    ),
                22 =>
                    array(
                        'CODE' => 'api_balaklava',
                        'UF_REGION' => '2',
                    ),
                23 =>
                    array(
                        'CODE' => 'ecorancho',
                        'UF_REGION' => '1',
                    ),
                24 =>
                    array(
                        'CODE' => 'dobraya_reka',
                        'UF_REGION' => '30',
                    ),
                25 =>
                    array(
                        'CODE' => 'vetreno',
                        'UF_REGION' => '6',
                    ),
                26 =>
                    array(
                        'CODE' => 'les_holidays',
                        'UF_REGION' => '11',
                    ),
                27 =>
                    array(
                        'CODE' => 'laplandskaya_derevnya',
                        'UF_REGION' => '16',
                    ),
                28 =>
                    array(
                        'CODE' => 'saykol_glemping_kuray',
                        'UF_REGION' => '4',
                    ),
                29 =>
                    array(
                        'CODE' => 'welna_eco_spa_resort',
                        'UF_REGION' => '27',
                    ),
                30 =>
                    array(
                        'CODE' => 'khutor_yarvi',
                        'UF_REGION' => '28',
                    ),
                31 =>
                    array(
                        'CODE' => 'dikaya-myata-kemp',
                        'UF_REGION' => '15',
                    ),
                32 =>
                    array(
                        'CODE' => 'severnyy_bereg',
                        'UF_REGION' => '3',
                    ),
                33 =>
                    array(
                        'CODE' => 'les',
                        'UF_REGION' => '5',
                    ),
                34 =>
                    array(
                        'CODE' => 'sonnyy_zaliv',
                        'UF_REGION' => '28',
                    ),
                35 =>
                    array(
                        'CODE' => 'truvor',
                        'UF_REGION' => '25',
                    ),
                36 =>
                    array(
                        'CODE' => 'iskatel_holiday_park',
                        'UF_REGION' => '3',
                    ),
                37 =>
                    array(
                        'CODE' => 'pribrezhnyy_yarburg',
                        'UF_REGION' => '6',
                    ),
                38 =>
                    array(
                        'CODE' => 'semeynyy_klub_bunin_ruchey',
                        'UF_REGION' => '1',
                    ),
                39 =>
                    array(
                        'CODE' => 'gulyay_gorod',
                        'UF_REGION' => '15',
                    ),
                40 =>
                    array(
                        'CODE' => 'zebirsk',
                        'UF_REGION' => '21',
                    ),
                41 =>
                    array(
                        'CODE' => 'yurta_lakhti',
                        'UF_REGION' => '28',
                    ),
                42 =>
                    array(
                        'CODE' => 'abrikos_village',
                        'UF_REGION' => '10',
                    ),
                43 =>
                    array(
                        'CODE' => 'severnyy_krym',
                        'UF_REGION' => '3',
                    ),
                44 =>
                    array(
                        'CODE' => 'tundra_house',
                        'UF_REGION' => '16',
                    ),
                45 =>
                    array(
                        'CODE' => 'gornyy_veter_by_reston',
                        'UF_REGION' => '10',
                    ),
                46 =>
                    array(
                        'CODE' => 'view_ga',
                        'UF_REGION' => '3',
                    ),
                47 =>
                    array(
                        'CODE' => 'glemping_the_garden',
                        'UF_REGION' => '34',
                    ),
                48 =>
                    array(
                        'CODE' => 'sila-vetra',
                        'UF_REGION' => '1',
                    ),
                49 =>
                    array(
                        'CODE' => 'kemping_pod_sosnami',
                        'UF_REGION' => '5',
                    ),
                50 =>
                    array(
                        'CODE' => 'skazka_u_tsarya_saltana',
                        'UF_REGION' => '5',
                    ),
                51 =>
                    array(
                        'CODE' => 'dalniy-kordon',
                        'UF_REGION' => '27',
                    ),
                52 =>
                    array(
                        'CODE' => 'bereg-neba',
                        'UF_REGION' => '14',
                    ),
                53 =>
                    array(
                        'CODE' => 'urochishche_saykol',
                        'UF_REGION' => '4',
                    ),
                54 =>
                    array(
                        'CODE' => 'pyatyy_sezon',
                        'UF_REGION' => '3',
                    ),
                55 =>
                    array(
                        'CODE' => 'istracottage',
                        'UF_REGION' => '1',
                    ),
                56 =>
                    array(
                        'CODE' => 'apparadise',
                        'UF_REGION' => '5',
                    ),
                57 =>
                    array(
                        'CODE' => 'kiwi_travel',
                        'UF_REGION' => '5',
                    ),
                58 =>
                    array(
                        'CODE' => 'paporotnik',
                        'UF_REGION' => '28',
                    ),
                59 =>
                    array(
                        'CODE' => 'greenvald_park_skandinaviya',
                        'UF_REGION' => '3',
                    ),
                60 =>
                    array(
                        'CODE' => 'dacha_inn_les',
                        'UF_REGION' => '1',
                    ),
                61 =>
                    array(
                        'CODE' => 'park_otel_paustovskiy',
                        'UF_REGION' => '23',
                    ),
                62 =>
                    array(
                        'CODE' => 'vazuza_camp',
                        'UF_REGION' => '22',
                    ),
                63 =>
                    array(
                        'CODE' => 'o2kislorod',
                        'UF_REGION' => '24',
                    ),
                64 =>
                    array(
                        'CODE' => 'territoriya',
                        'UF_REGION' => '20',
                    ),
                65 =>
                    array(
                        'CODE' => 'volkov-house',
                        'UF_REGION' => '30',
                    ),
                66 =>
                    array(
                        'CODE' => 'elitnaya_dacha_u_finskogo_zaliva_zelenogorsk',
                        'UF_REGION' => '3',
                    ),
                67 =>
                    array(
                        'CODE' => 'rodionova_dacha',
                        'UF_REGION' => '1',
                    ),
                68 =>
                    array(
                        'CODE' => 'staryy_sig',
                        'UF_REGION' => '19',
                    ),
                69 =>
                    array(
                        'CODE' => 'grafskaya_polyana',
                        'UF_REGION' => '1',
                    ),
                70 =>
                    array(
                        'CODE' => 'villa_pozitiv',
                        'UF_REGION' => '26',
                    ),
                71 =>
                    array(
                        'CODE' => 'yasno_pole',
                        'UF_REGION' => '15',
                    ),
                72 =>
                    array(
                        'CODE' => 'rezidentsiya_severnoe_siyanie',
                        'UF_REGION' => '16',
                    ),
                73 =>
                    array(
                        'CODE' => 'ekonevidal',
                        'UF_REGION' => '1',
                    ),
                74 =>
                    array(
                        'CODE' => 'lagom',
                        'UF_REGION' => '4',
                    ),
                75 =>
                    array(
                        'CODE' => 'edimonovo',
                        'UF_REGION' => '19',
                    ),
                76 =>
                    array(
                        'CODE' => 'zagorodnyy_klub_ilmen',
                        'UF_REGION' => '25',
                    ),
                77 =>
                    array(
                        'CODE' => 'forest',
                        'UF_REGION' => '1',
                    ),
                78 =>
                    array(
                        'CODE' => 'vishnevyy_sad',
                        'UF_REGION' => '1',
                    ),
                79 =>
                    array(
                        'CODE' => 'shepot_trav',
                        'UF_REGION' => '2',
                    ),
                80 =>
                    array(
                        'CODE' => 'borvikha',
                        'UF_REGION' => '22',
                    ),
                81 =>
                    array(
                        'CODE' => 'mayak_glemping',
                        'UF_REGION' => '7',
                    ),
                82 =>
                    array(
                        'CODE' => 'usadba_mordvesa',
                        'UF_REGION' => '15',
                    ),
                83 =>
                    array(
                        'CODE' => 'gostevoy_dom_psakho',
                        'UF_REGION' => '5',
                    ),
                84 =>
                    array(
                        'CODE' => 'sokolinoe_gnezdo',
                        'UF_REGION' => '1',
                    ),
                85 =>
                    array(
                        'CODE' => 'baza_otdykha_morskaya_bukhta',
                        'UF_REGION' => '10',
                    ),
                86 =>
                    array(
                        'CODE' => 'na-volne',
                        'UF_REGION' => '5',
                    ),
                87 =>
                    array(
                        'CODE' => 'ashkhadakh',
                        'UF_REGION' => '30',
                    ),
                88 =>
                    array(
                        'CODE' => 'teneri',
                        'UF_REGION' => '4',
                    ),
                89 =>
                    array(
                        'CODE' => 'ust_vazuza_rybatskaya_derevnya',
                        'UF_REGION' => '19',
                    ),
                90 =>
                    array(
                        'CODE' => 'okreka',
                        'UF_REGION' => '1',
                    ),
                91 =>
                    array(
                        'CODE' => 'bereg_resort',
                        'UF_REGION' => '18',
                    ),
                92 =>
                    array(
                        'CODE' => 'yakhta_cherepakha_v_portu_arkhelon_34',
                        'UF_REGION' => '3',
                    ),
                93 =>
                    array(
                        'CODE' => 'horseka_resort',
                        'UF_REGION' => '1',
                    ),
                94 =>
                    array(
                        'CODE' => 'just_wood',
                        'UF_REGION' => '15',
                    ),
                95 =>
                    array(
                        'CODE' => 'beryezovaya-13',
                        'UF_REGION' => '4',
                    ),
                96 =>
                    array(
                        'CODE' => 'armkhi',
                        'UF_REGION' => '17',
                    ),
                97 =>
                    array(
                        'CODE' => 'glemping_park_kosmosfera',
                        'UF_REGION' => '25',
                    ),
                98 =>
                    array(
                        'CODE' => 'rancho',
                        'UF_REGION' => '1',
                    ),
                99 =>
                    array(
                        'CODE' => 'tuchkovo-spa',
                        'UF_REGION' => '1',
                    ),
                100 =>
                    array(
                        'CODE' => 'tekhnokhutor',
                        'UF_REGION' => '6',
                    ),
                101 =>
                    array(
                        'CODE' => 'krym_nostalzhi',
                        'UF_REGION' => '2',
                    ),
                102 =>
                    array(
                        'CODE' => 'pentakli',
                        'UF_REGION' => '5',
                    ),
                103 =>
                    array(
                        'CODE' => 'dolgoruki_cottages',
                        'UF_REGION' => '11',
                    ),
                104 =>
                    array(
                        'CODE' => 'tayezhnye_dachi',
                        'UF_REGION' => '1',
                    ),
                105 =>
                    array(
                        'CODE' => 'okulova_zaimka',
                        'UF_REGION' => '1',
                    ),
                106 =>
                    array(
                        'CODE' => 'dream-village-oksino',
                        'UF_REGION' => '1',
                    ),
                107 =>
                    array(
                        'CODE' => 'shalet_greystone',
                        'UF_REGION' => '5',
                    ),
                108 =>
                    array(
                        'CODE' => 'staryy_gorod',
                        'UF_REGION' => '5',
                    ),
                109 =>
                    array(
                        'CODE' => 'dombay_winter_hall',
                        'UF_REGION' => '31',
                    ),
                110 =>
                    array(
                        'CODE' => 'mb_resort',
                        'UF_REGION' => '1',
                    ),
                111 =>
                    array(
                        'CODE' => 'gostinyy_dom_medved',
                        'UF_REGION' => '12',
                    ),
                112 =>
                    array(
                        'CODE' => 'dom_v_kavgolovo',
                        'UF_REGION' => '3',
                    ),
                113 =>
                    array(
                        'CODE' => 'rodnoe_gnezdo',
                        'UF_REGION' => '10',
                    ),
                114 =>
                    array(
                        'CODE' => 'rubas',
                        'UF_REGION' => '1',
                    ),
                115 =>
                    array(
                        'CODE' => 'filokseniya_olkhon_baykal',
                        'UF_REGION' => '8',
                    ),
                116 =>
                    array(
                        'CODE' => 'greenwood',
                        'UF_REGION' => '20',
                    ),
                117 =>
                    array(
                        'CODE' => 'chuchemlya_eco_village',
                        'UF_REGION' => '25',
                    ),
                118 =>
                    array(
                        'CODE' => 'art_kovcheg',
                        'UF_REGION' => '5',
                    ),
                119 =>
                    array(
                        'CODE' => 'villa_forrest',
                        'UF_REGION' => '13',
                    ),
                120 =>
                    array(
                        'CODE' => 'dubovaya_roshcha',
                        'UF_REGION' => '10',
                    ),
                121 =>
                    array(
                        'CODE' => 'glemping_108_zhelaniy',
                        'UF_REGION' => '4',
                    ),
                122 =>
                    array(
                        'CODE' => 'domingo_dacha',
                        'UF_REGION' => '1',
                    ),
                123 =>
                    array(
                        'CODE' => 'safari_chalda_by_reston',
                        'UF_REGION' => NULL,
                    ),
                124 =>
                    array(
                        'CODE' => 'ooo_parus_ruza',
                    ),
                125 =>
                    array(
                        'CODE' => 'kemping_gorizont',
                        'UF_REGION' => '5',
                    ),
                126 =>
                    array(
                        'CODE' => 'travel_khotels_tkhach',
                        'UF_REGION' => '30',
                    ),
                127 =>
                    array(
                        'CODE' => 'travel_khotels_ammonit',
                        'UF_REGION' => '30',
                    ),
                128 =>
                    array(
                        'CODE' => 'opushka_dom_sredi_derevev',
                        'UF_REGION' => '35',
                    ),
                129 =>
                    array(
                        'CODE' => 'fantastika_lodges_by_units',
                        'UF_REGION' => '1',
                    ),
                130 =>
                    array(
                        'CODE' => 'daniely',
                        'UF_REGION' => '5',
                    ),
                131 =>
                    array(
                        'CODE' => 'ekopark_polyany',
                        'UF_REGION' => '23',
                    ),
                132 =>
                    array(
                        'CODE' => 'terra_altaya',
                        'UF_REGION' => '4',
                    ),
                133 =>
                    array(
                        'CODE' => 'finnougoriya',
                        'UF_REGION' => '33',
                    ),
                134 =>
                    array(
                        'CODE' => 'immersivnyy_park_otel_vazuza_love',
                        'UF_REGION' => '22',
                    ),
                135 =>
                    array(
                        'CODE' => 'apart_otel_port',
                        'UF_REGION' => '29',
                    ),
                136 =>
                    array(
                        'CODE' => 'neva',
                        'UF_REGION' => NULL,
                    ),
                140 =>
                    array(
                        'CODE' => 'gostinitsa_mir_',
                        'UF_REGION' => '9',
                    ),
                148 =>
                    array(
                        'CODE' => 'vremena-goda',
                        'UF_REGION' => '5',
                    )
            );

        foreach ($arSections as $arSection) {
            $helper->Iblock()->updateSectionIfExists(
                $iblockId,
                $arSection
            );
        }
    }

    public function down()
    {
        //your code ...
    }
}
