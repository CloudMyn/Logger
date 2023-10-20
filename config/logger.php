<?php


return [

    /**
     *  Enable bases route
     */
    'enable_route' => true,

    /**
     *  Define the prefix of the route
     */
    'prefix'    =>  'logger',

    /**
     *  Define the middleware of the page route
     */
    'middleware'    => [],

    /**
     *  Override stack trace view
     */
    'trace_view' => 'cloudmyn_logger::components.stack-trace',

];
