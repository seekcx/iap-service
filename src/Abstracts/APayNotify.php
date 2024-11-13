<?php

namespace Simplephp\IapService\Abstracts;

use Simplephp\IapService\Contracts\INotify;

abstract class APayNotify implements INotify
{
    /**
     * @var string
     */
    protected $notificationType;

    /**
     * @var string
     */
    protected $subtype;

    /**
     * @var array
     */
    protected $notifyData;

    /**
     * @return string
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * @return array
     */
    public function getNotifyData()
    {
        return $this->notifyData;
    }
}