<?php namespace Mailgun\Events;



/**
 * Implementation (part) of the events api
 * https://documentation.mailgun.com/api-events.html
 *
 */
class Events {
    protected $client;
    protected $events;
    protected $domain;
    protected $beginTs;

    function __construct(&$client, $domain) {
        $this->client = $client;
        $this->domain = $domain;
    }


    /**
     * Implementation of the event polling mechanism as suggested in
     * https://documentation.mailgun.com/api-events.html#event-polling
     *
     * @return [type] [description]
     */
    public function poll($uri, $args) {
        if(is_null($uri)){
            $uri = sprintf('%s/events', $this->domain);
            $this->beginTs = $args['begin'];
        } else {
            $args = array();
        }

        $response = $this->client->get( $uri, $args );

        $threshold = $this->beginTs + 3600; //
        $totalItems = count($response->http_response_body->items);

        if($totalItems){
            $lastEventTimestamp = (int) $response->http_response_body->items[$totalItems - 1]->timestamp;

            if($lastEventTimestamp < $threshold){
                return false;
            }
        }

        return $response;
    }
}