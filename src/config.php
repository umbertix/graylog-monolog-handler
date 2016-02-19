<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | UDP Port
    |--------------------------------------------------------------------------
    |
    |
    | This port is the port where Graylog will expect the messages. Default: 12201
    |
    */
    'port_udp' => env('GRAYLOG_PORT_UDP', 12201),

    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    |
    | Set what server should send errors to.
    |
    */
    'endpoint' => env('GRAYLOG_ENDPOINT', null)


);