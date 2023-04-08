<?php

require_once('vendor/autoload.php');
require_once('secrets.php');

use App\User;
use App\Bases\DB;
use App\Utility;

$phone = $_POST['from'];
$text = $_POST['text']; // name pin e.g John 1234

$user = new User($phone);
$pdo = new DB();
$db = $pdo->connectToDB();

$text = explode(" ", $text);

if (count($text) == 2) {
    $user->setName($text[0])
        ->setPin($text[1])
        ->setBalance(Utility::USER_INITIAL_BALANCE)
        ->register($db);
}
