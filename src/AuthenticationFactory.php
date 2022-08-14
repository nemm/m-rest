<?php

    namespace Nemm\MRest;

    /**
     * Factory for creating Authentication
     */
    class AuthenticationFactory {

        public static function createNoAuth(){
            return new Authentication();
        }

        public static function createBasicAuth( string $username = '', string $password = ''){
            return 
                (new Authentication())
                    ->setType(AuthenticationType::BASIC)
                    ->setUsername($username)
                    ->setPassword($password);
        }

        public static function createJWTAuth( string $token = ''){
            return 
                (new Authentication())
                    ->setType(AuthenticationType::JWT)
                    ->setToken($token);
        }
    }
