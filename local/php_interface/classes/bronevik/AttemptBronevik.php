<?php

namespace Naturalist\bronevik;

trait AttemptBronevik
{
    public function setAttempt(int $attempt = 0)
    {
        $this->bronevik->setAttempt($attempt);
    }
}