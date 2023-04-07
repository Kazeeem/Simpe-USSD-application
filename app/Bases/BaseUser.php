<?php

namespace App\Bases;

class BaseUser
{
    protected string $name;
    protected string $phone;
    protected string $pin;
    protected float $balance;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getPhone():string
    {
        return $this->phone;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
        return $this;
    }

    public function getPin():string
    {
        return $this->pin;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    public function getBalance():float
    {
        return $this->balance;
    }
}