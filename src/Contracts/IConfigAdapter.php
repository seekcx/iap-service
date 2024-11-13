<?php

namespace Simplephp\IapService\Contracts;

interface IConfigAdapter
{
    public function getConfig(): array;
}