<?php

namespace App;

use App\Bases\BaseUser;

class User extends BaseUser
{

    public function __construct($phone)
    {
        $this->phone = $phone;
    }

    public function register($pdo)
    {
        try {
            if (!$this->isUserRegistered($pdo)) {
                $hashed_pin = password_hash($this->getPin(), PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (`name`, `pin`, `phone`, `balance`, `date_registered`) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$this->getName(), $hashed_pin, $this->getPhone(), $this->getBalance(), date('Y-m-d h:i:s')]);

                return $this->isUserRegistered($pdo);
            }
            else {
                return 'Phone number has already been registered.';
            }
        }
        catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function isUserRegistered($pdo): bool
    {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE phone=?");
        $stmt->execute([$this->getPhone()]);

        if (count($stmt->fetchAll()) > 0) {
            return true;
        }

        return false;
    }

    public function getUserId($pdo)
    {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        $row = $stmt->fetch();

        return $row['user_id'];
    }

    public function getUserName($pdo): string
    {
        $stmt = $pdo->prepare("SELECT name FROM users WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        $row = $stmt->fetch();

        return $row['name'];
    }

    public function correctPin($pdo): bool
    {
        $stmt = $pdo->prepare("SELECT pin FROM users WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        if (password_verify($this->getPin(), $row['pin'])) {
            return true;
        }

        return false;
    }

    public function checkBalance($pdo): float
    {
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        $row = $stmt->fetch();

        return $row['balance'];
    }
}