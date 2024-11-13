<?php

namespace Simplephp\IapService\Contracts;

interface INotify
{
    public function getNotificationType();

    public function getSubtype();

    public function getNotifyData();

    /**
     * @param string $serviceProvider
     * @param array $notifyData
     * @return mixed
     */
    public function handle(string $serviceProvider, array $notifyData);
}