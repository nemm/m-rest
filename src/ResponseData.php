<?php
    namespace Nemm\MRest;

    /**
     * Class for keeping nicely formatted response
     */
    class ResponseData {
        private int $code;
        private ?array $data;

        public function __construct(int $code, ?array $data) {
            $this->code = $code;
            $this->data = $data;
        }

        public function getCode(): int {
            return $this->code;
        }

        public function getData(): ?array {
            return $this->data;
        }

        public function setCode(int $code): void {
            $this->code = $code;
        }

        public function setData(?array $data): void {
            $this->data = $data;
        }
    }
