<?php

    namespace Nemm\MRest;

    /**
     * Class for keeping Authentication data
     */
    class Authentication {
        private AuthenticationType $type = AuthenticationType::NONE;
        private string $username;
        private string $password;
        private string $token;

        public function __construct() {

        }

        public function getType(): AuthenticationType {
            return $this->type;
        }

        public function getUsername(): string {
            return $this->username;
        }

        public function getPassword(): string {
            return $this->password;
        }

        public function getToken(): string {
            return $this->token;
        }

        public function setType(AuthenticationType $type): Authentication {
            $this->type = $type;
            return $this;
        }

        public function setUsername(string $username): Authentication {
            $this->username = $username;
            return $this;
        }

        public function setPassword(string $password): Authentication {
            $this->password = $password;
            return $this;
        }

        public function setToken(string $token): Authentication {
            $this->token = $token;
            return $this;
        }

    }
