<?php

/**
 *
 * This script is meant for Africastalking.com USSD Gateway.
 *
 */

require_once('vendor/autoload.php');

header('Content-type: text/plain');

// Read the variables sent via POST from our API
$sessionId = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];

use App\Bases\DB;
use App\Menu;
use App\User;

$menu = new Menu();
$user = new User($phoneNumber);
$pdo = new DB();
$db = $pdo->connectToDB();

// Start of USSD application menu logic
$text = $menu->menuMiddleware($text);

$isRegistered = true;

if (!$text && !$user->isUserRegistered($db)) { // User is not registered and text is empty (1st menu which is to register)
    echo $menu->mainMenuUnregistered();
}
elseif (!$text && $user->isUserRegistered($db)) { // User is registered and text is empty (1st menu which is to choose what they want to do between (Check balance, Withdraw, Transfer)
    echo "CON ".$menu->mainMenuRegistered($user->getUserName($db));
}
elseif ($text && $user->isUserRegistered($db)) { // User is registered and text is not empty (Sub mmenu)
    $texts = explode('*', $text);

    switch($texts[0]) {
        case 1:
            echo $menu->sendMoneyMenu($texts);
            break;
        case 2:
            echo $menu->withdrawMoneyMenu($texts);
            break;
        case 3:
            echo $menu->checkBalanceMenu($texts);
            break;
        default:
            $ussd_level = count($texts) - 1;
            $menu->retainMenuForInvalidEntry($sessionId, $user, $ussd_level, $db);
            echo "CON Invalid option\n". $menu->mainMenuRegistered($user->getUserName($db));
    }
}
elseif ($text && !$user->isUserRegistered($db)) { // User is not registered and text is not empty (Sub menu)
    $texts = explode('*', $text);

    if ($texts[0] == 1) {
        echo $menu->registerMenu($texts, $phoneNumber, $db);
    }
    else {
        echo "END Invalid request.";
    }
}