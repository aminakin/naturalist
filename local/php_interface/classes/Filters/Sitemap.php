<?php

namespace Naturalist\Filters;

use Bitrix\Seo\SitemapIndex;
use Bitrix\Seo\SitemapFile;
use Bitrix\Main\Type\DateTime;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\XmlWriter;


/**
 * Карта сайта
 */
class Sitemap
{
    private const PROTOCOL = 'https';
    private const DOMAIN = 'naturalist.travel';
    private const FILE_NAME = 'chpy.xml';
    private const IBLOCK_SITEMAP_FILE_NAME = '/sitemap-iblock-1.xml';

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

    /**
     * Возвращает все ЧПУ
     */
    public static function getChpys()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        return $chpyDataClass::query()
            ->addSelect('UF_NEW_URL')
            ?->fetchAll();
    }

    /**
     * Добавляет ЧПУ в файл
     */
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

    /**
     * Возвращает все активные объекты
     */
    private static function getAllObjects()
    {
        $entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock(CATALOG_IBLOCK_ID);
        $rsSectionObjects = $entity::getList(
            [
                'filter' => ['IBLOCK_ID' => CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y'],
                'select' => ['ID', 'NAME', 'CODE', 'UF_PHOTOS', 'TIMESTAMP_X'],
            ]
        );

        return $rsSectionObjects->fetchAll();
    }

    /**
     * Добавляет фото в карту сайта по объектам, переписывая исходный файл
     */
    public static function addImagesToSitemap()
    {
        $export = new XmlWriter(array(
            'file' => self::IBLOCK_SITEMAP_FILE_NAME,
            'create_file' => true,
            'charset' => SITE_CHARSET,
            'lowercase' => true
        ));

        $export->openFile();

        $export->writeBeginTag('urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"');

        $objects = self::getAllObjects();

        foreach ($objects as $object) {
            $export->writeBeginTag('url');

            $export->writeFullTag('loc', self::PROTOCOL . '://' . self::DOMAIN . '/catalog/' . $object['CODE'] . '/');
            $export->writeFullTag('lastmod', $object['TIMESTAMP_X']->format('Y-m-d\TH:i:sP'));

            if (!empty($object['UF_PHOTOS'])) {
                foreach ($object['UF_PHOTOS'] as $photo) {
                    $export->writeBeginTag('image:image');
                    $export->writeFullTag('image:loc', self::PROTOCOL . '://' . self::DOMAIN  . \CFile::getPath($photo));
                    $export->writeEndTag('image:image');
                }
            }

            $export->writeEndTag('url');
        }

        $export->writeEndTag('urlset');
        $export->closeFile();
    }
}
