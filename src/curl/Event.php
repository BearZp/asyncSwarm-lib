<?php

namespace Lib\curl;

use Symfony\Component\EventDispatcher\GenericEvent;

class Event extends GenericEvent
{
    /**
     * @var Response
     */
    public $response;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var RequestsQueue
     */
    public $queue;
}
