<?php

namespace FlightAuth\Events;

interface EventInterface
{
    public function notify($action);
}