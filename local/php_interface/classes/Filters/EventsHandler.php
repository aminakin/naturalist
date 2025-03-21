<?php

namespace Naturalist\Filters;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Context;
use Bitrix\Main\HttpRequest;
use Naturalist\Filters\UrlHandler;

/**
 * Работа с событиями
 */
class EventsHandler
{
    private static $i = 1;

    public static function PageStart()
    {
        global $APPLICATION, $PAGEN_1;

        if (mb_strpos($APPLICATION->GetCurPage(false), '/bitrix') === 0) {
            return;
        }

        // $excludeParams = Option::get(
        //     self::MODULE_NAME,
        //     'PARAMS_EXCEPTION_SETTINGS',
        //     '',
        //     SITE_ID
        // );

        $excludeParams = '';

        $context = Context::getCurrent();
        // if (
        //     $context->getRequest()->isAjaxRequest() && Option::get(
        //         self::MODULE_NAME,
        //         'RETURN_AJAX_' . SITE_ID,
        //         'N',
        //         SITE_ID
        //     ) == 'Y'
        // ) {
        //     return;
        // }

        if (
            !$context->getRequest()->getQueryList()->isEmpty()
            && method_exists($context->getRequest()->getQueryList(), 'getValues')
        ) {
            $queryValues = $context->getRequest()->getQueryList()->getValues();
            $queryValues = self::requestStrToUpper($queryValues);

            $endScrypt = self::checkRequestExceptions($excludeParams, $queryValues);
            if ($endScrypt) {
                return;
            }
        }

        $server = $context->getServer();
        $server_array = $server->toArray();
        $url_parts = explode("?", $context->getRequest()->getRequestUri());
        $url_parts[0] = rawurlencode(rawurldecode($url_parts[0]));
        $url_parts[0] = str_replace('%2F', '/', $url_parts[0]);

        $queryString = parse_url($context->getRequest()->getRequestUri(), PHP_URL_QUERY);
        parse_str($queryString, $queryParams);
        $guests = isset($queryParams['guests']) ? $queryParams['guests'] : null;



        //Debug::writeToFile($url_parts, 'url_parts', '__bx_log.log');
        $str = '';
        // $str = Option::get(
        //     "sotbit.seometa",
        //     'PAGENAV_' . SITE_ID,
        //     '',
        //     SITE_ID
        // );

        // if ($str != '') {
        //     $preg = str_replace('/', '\/', $str);
        //     $preg = '/' . str_replace('%N%', '\d', $preg) . '/';
        //     preg_match($preg, $url_parts[0], $matches);
        //     if ($matches) {
        //         $exploted_pagen = explode('%N%', $str);
        //         $n = str_replace($exploted_pagen[0], '', $matches[0]);
        //         $n = str_replace($exploted_pagen[1], '', $n);
        //         $_REQUEST['PAGEN_1'] = (int)$n;
        //         $url_parts[0] = str_replace($matches[0], '', $url_parts[0]);
        //     }

        //     if (isset($_REQUEST['PAGEN_1'])) {
        //         $n = $_REQUEST['PAGEN_1'];
        //         $pagen = str_replace('%N%', $n, $str);
        //         $url_parts[1] = '';
        //         unset($_GET['PAGEN_1']);
        //         foreach ($_GET as $i => $p) {
        //             $r[] = $i . '=' . $p;
        //         }

        //         $r[] = $pagen;
        //         $url_parts[1] = implode('&', $r);
        //         $PAGEN_1 = $n;
        //     }
        // }
        if (
            !($instance = UrlHandler::getByNewUrl($url_parts[0], SITE_ID))
            && !($instance = UrlHandler::getByNewUrl($context->getRequest()->getRequestUri(), SITE_ID))
        ) {
            $instance = UrlHandler::getByRealUrl($url_parts[0], SITE_ID);
            if (!$instance) {
                $instance = UrlHandler::getByRealUrl(rawurldecode($context->getRequest()->getRequestUri()), SITE_ID);
            }

            if ($instance && SITE_ID == $instance['SITE_ID'] && EventsHandler::$i) {
                EventsHandler::$i = 0;
                if (isset($pagen)) {
                    $instance['UF_NEW_URL'] = $instance['UF_NEW_URL'] . $pagen;
                    $url_parts[1] = '';
                }

                $additionalQueryString = '';
                if (intval($guests) > 2) {
                    $additionalQueryString = '?guests=' . $guests;
                }
                LocalRedirect(
                    $instance['UF_NEW_URL'] . $additionalQueryString/*. ($url_parts[1] != '' ? "?" . $url_parts[1] : '')*/,
                    false,
                    '301 Moved Permanently'
                );
            }
        }

        if ($instance && ($instance['UF_NEW_URL'] != $instance['UF_REAL_URL']) && SITE_ID == $instance['SITE_ID']) {
            $url_parts_query = explode("&", $url_parts[1]);
            $urlPartsCHPU = explode("?", $instance['UF_REAL_URL']);
            if ($urlPartsCHPU[1]) {
                $urlPartsCHPU = explode("&", $urlPartsCHPU[1]);
                if ($urlPartsCHPU) {
                    $url_parts_query = array_merge($urlPartsCHPU);
                }
            }

            foreach ($url_parts_query as $item) {
                $items = explode('=', $item);
                $_GET[$items[0]] = $items[1];
            }

            if (!isset($pagen)) {
                $_SERVER['REQUEST_URI'] = $instance['UF_REAL_URL'];
                $server_array['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
                $server->set($server_array);
                // $userAgent = $context->getServer()->getUserAgent();
                $context->initialize(
                    new HttpRequest(
                        $server,
                        $_GET,
                        [],
                        [],
                        $_COOKIE
                    ),
                    $context->getResponse(),
                    $server
                );
                $APPLICATION->sDocPath2 = GetPagePath(false, true);
                $APPLICATION->sDirPath = GetDirPath($APPLICATION->sDocPath2);
                $protocol =  ($context->getRequest()->isHttps() ? 'https' : 'http') . '://';
                // $url = $protocol .  $server->getServerName() . $instance['UF_NEW_URL'];
                // if (Option::get("sotbit.seometa", 'INC_STATISTIC', 'N', SITE_ID) == 'Y') {
                //     self::getStatus($url, $instance, $userAgent);
                // }
            } else {
                $url_parts[0] .= $pagen;
                $_SERVER['REQUEST_URI'] = $instance['UF_REAL_URL'];
                $server_array['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
                $server->set($server_array);
                // $userAgent = $context->getServer()->getUserAgent();
                $context->initialize(
                    new HttpRequest(
                        $server,
                        $_GET,
                        [],
                        [],
                        $_COOKIE
                    ),
                    $context->getResponse(),
                    $server
                );
                $APPLICATION->sDocPath2 = GetPagePath(false, true);
                $APPLICATION->sDirPath = GetDirPath($APPLICATION->sDocPath2);
                $protocol =  ($context->getRequest()->isHttps() ? 'https' : 'http') . '://';
                // $url = $protocol .  $server->getServerName() . $instance['UF_NEW_URL'] . $pagen;
                // if (Option::get("sotbit.seometa", 'INC_STATISTIC', 'N', SITE_ID) == 'Y') {
                //     self::getStatus($url, $instance, $userAgent);
                // }
                $APPLICATION->SetCurPage($url_parts[0]);
            }

            EventsHandler::$i = 0;
        }
    }

    public static function requestStrToUpper($queryValues)
    {
        foreach ($queryValues as $key => $queryValue) {
            if (is_array($queryValue)) {
                if (is_string($key)) {
                    $queryValues[mb_strtoupper($key)] = self::requestStrToUpper($queryValue);
                    unset($queryValues[$key]);
                } else {
                    $queryValues[$key] = self::requestStrToUpper($queryValue);
                }
            } else {
                if (is_string($key)) {
                    $queryValues[mb_strtoupper($key)] = mb_strtoupper($queryValue);
                    unset($queryValues[$key]);
                } else {
                    $queryValues[$key] = mb_strtoupper($queryValue);
                }
            }
        }
        return $queryValues;
    }

    public static function checkRequestExceptions($excludeParams, $queryValues): int
    {
        foreach ($excludeParams as $key => $excludeParam) {
            if (is_array($excludeParam)) {
                return self::checkRequestExceptions($excludeParam, $queryValues[$key]);
            } elseif ($excludeParam && ($queryValues[$key] == $excludeParam) || ($queryValues[$key] && !$excludeParam)) {
                return 1;
            }
        }
        return 0;
    }

    /**
     * Установка SEO данных
     */
    public static function OnEpilog()
    {
        global $APPLICATION;

        $context = Context::getCurrent();
        $seoData = UrlHandler::getByRealUrl(rawurldecode($context->getRequest()->getRequestUri()), SITE_ID);
        if ($seoData['UF_H1']) {
            $APPLICATION->SetPageProperty("custom_title", $seoData['UF_H1']);
        }
        if ($seoData['UF_TITLE']) {
            $APPLICATION->SetTitle($seoData['UF_TITLE']);
        }
        if ($seoData['UF_DESCRIPTION']) {
            $APPLICATION->SetPageProperty("description", $seoData['UF_DESCRIPTION']);
        }
    }
}
