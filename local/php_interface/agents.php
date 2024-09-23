<?php

use Naturalist\CustomFunctions;
use Naturalist\Filters\AutoCreate;
use Naturalist\Filters\Sitemap;

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

    return 'updateSitemap();';
}
