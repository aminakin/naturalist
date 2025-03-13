<?php

namespace Naturalist\Http;

interface HttpFetchInterface
{
    public function post(string $url, array $data): string;
}