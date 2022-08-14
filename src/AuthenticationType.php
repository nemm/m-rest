<?php

    namespace Nemm\MRest;

    /**
     * Implemented Authentication types
     */
    enum AuthenticationType {
        case NONE;
        case BASIC;
        case JWT;
    }
