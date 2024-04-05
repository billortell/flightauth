<?php

namespace FlightAuth\Events;

class EventManager  {

    private $events = [];

    public function __construct()
    {
        $this->setupEvents();
    }

    public function setupEvents()
    {
        $event = new EventChangeEmailSuccess();
        $this->add($event, 'changeemailsuccess');

        $event = new EventAfterRegistration();
        $this->add($event, 'registrationconfirmation');

        $event = new EventVerificationEmailResend();
        $this->add($event, 'verificationemailresend');

        $event = new EventChangePasswordSuccess();
        $this->add($event, 'changepasswordsuccess');

        $event = new EventForgotPasswordConfirmation();
        $this->add($event, 'forgotpasswordconfirmation');

    }

    /***
     * Add event (observer) to the stack of observers.
     *
     * @param EventInterface $event
     * @param $eventName
     * @return void
     */
    public function add(EventInterface $event, $eventName) {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = [];
        }
        $this->events[$eventName][] = $event;
    }

    /***
     * Notify every event (observer) for any particular event(s)
     *
     * @param $eventTriggered
     * @param $data
     * @return true|void
     */
    public function notify($eventTriggered, $data=null) {
        $eventsFound = false;
        foreach ($this->events as $eventName => $observers) {
            if ( $eventsFound ) {
                return true;
            }
            // check if this event matches any observers
            if ( $eventTriggered != $eventName ) {
                continue;
            }
            // matched event to those observing the event
            $eventsFound = true;
            foreach ( $observers as $observer ) {
                $observer->notify($data);
            }
        }
    }

    /***
     * Get list of event observers.
     *
     * @return array
     */
    public function getEvents()
    {
        $events = [];
        foreach ($this->events as $eventName => $observers) {
            $events[] = $eventName;
        }
        return $events;
    }

}
