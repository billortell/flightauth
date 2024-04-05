<?php

namespace FlightAuth\Events;

class EventSample implements EventInterface {

    public function notify($action)
    {
        echo "Observer observed in: ".__CLASS__;
        exit("in ".__METHOD__);
    }

}
