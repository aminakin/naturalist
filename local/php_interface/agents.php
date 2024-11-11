<?php

use Naturalist\CustomFunctions;
use Naturalist\Filters\AutoCreate;
use Naturalist\Filters\Sitemap;
use Naturalist\BnovoDataFilesHandler;

function checkReviewInvites()
{
    CustomFunctions::sendReviewInvite();

    return 'checkReviewInvites();';
}

function createCHPU()
{
    AutoCreate::createAllChpys();

    return 'createCHPU();';
}

function updateSitemap()
{
    Sitemap::addChpyToFile();
    Sitemap::addChpyFileToSitemap();

    return 'updateSitemap();';
}

function handleBnovoFiles()
{
    if (!$GLOBALS['BNOVO_FILES_WORKING']) {
        $BnovoDataFilesHandler = new BnovoDataFilesHandler();
        $BnovoDataFilesHandler->handleFile();
    }

    return 'handleBnovoFiles();';
}
