<?php

namespace Naturalist\Filters;

use Bitrix\Seo\SitemapIndex;
use Bitrix\Seo\SitemapFile;
use Bitrix\Main\Type\DateTime;
use Bitrix\Highloadblock\HighloadBlockTable;

/**
 * Карта сайта
 */
class Sitemap
{
    private const PROTOCOL = 'https';
    private const DOMAIN = 'naturalist.travel';
    private const FILE_NAME = 'chpy.xml';

    /**
     * Добавление файла с ЧПУ ссылоками в карту сайта
     */
    public static function addChpyFileToSitemap()
    {
        $sitemap = new SitemapIndex('/sitemap.xml', ['SITE_ID' => 's1', 'PROTOCOL' => self::PROTOCOL, 'DOMAIN' => self::DOMAIN]);

        $fileUrlEnc = self::PROTOCOL . '://' . self::DOMAIN . '/' . self::FILE_NAME;

        $contents = $sitemap->getContents();

        $reg = "/" . sprintf(preg_quote($sitemap::ENTRY_TPL, "/"), preg_quote($fileUrlEnc, "/"), "[^<]*") . "/";

        $newEntry = sprintf(
            $sitemap::ENTRY_TPL,
            $fileUrlEnc,
            date('c')
        );

        $count = 0;
        $contents = preg_replace($reg, $newEntry, $contents, 1, $count);

        if ($count <= 0) {
            $contents = mb_substr($contents, 0, -mb_strlen($sitemap::FILE_FOOTER))
                . $newEntry . $sitemap::FILE_FOOTER;
        }

        $sitemap->putContents($contents);
    }

    public static function getChpys()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        return $chpyDataClass::query()
            ->addSelect('UF_NEW_URL')
            ?->fetchAll();
    }

    public static function addChpyToFile()
    {
        file_put_contents('/' . self::FILE_NAME, '');

        $file = new SitemapFile('/' . self::FILE_NAME, ['SITE_ID' => 's1', 'PROTOCOL' => self::PROTOCOL, 'DOMAIN' => self::DOMAIN]);

        $file->addHeader();

        $chpys = self::getChpys();

        if (is_array($chpys) && !empty($chpys)) {
            foreach ($chpys as $chpy) {
                $file->addEntry([
                    'XML_LOC' => self::PROTOCOL . '://' . self::DOMAIN . $chpy['UF_NEW_URL'],
                    'XML_LASTMOD' => (new DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s'))->format('Y-m-d\TH:i:sP'),
                ]);
            }
        }

        $file->addFooter();
    }
}
