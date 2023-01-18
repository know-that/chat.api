<?php

namespace App\Services;

class TestService
{
    public array $data;

    public function data( array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function call(): array
    {
        dump($this->data);
    }
}
