<?php

use Naturalist\CustomFunctions;

function checkReviewInvites()
{
    CustomFunctions::sendReviewInvite();

    return 'checkReviewInvites();';
}