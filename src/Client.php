<?php

    namespace Nemm\MRest;



    class Client {
        public function __construct($param) {
            $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
            $request = $psr17Factory->createRequest('GET', 'http://tnyholm.se/bla')->withBody( \Nyholm\Psr7\Stream::create('einekleine') );


            $request->getBody()->rewind();
            var_dump( $request->getBody()->getContents());
        }
    }
