<?php

/**
 *
 * This script is meant for Africastalking.com USSD Gateway.
 *
 * Things to Fix, error handling. (For example, if a registered user sends the wrong input on the menu that asks whether the user wants to send money, withdraw, etc.
 * Then if the user enters the correct options the second time, subsequent option/inputs the user fills do not happen, it returns the "Invalid option" message.
 * Area of focus is the "menuMiddleware"
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
$text = $menu->menuMiddleware($text, $sessionId, $db);

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
            echo $menu->sendMoneyMenu($texts, $user, $db);
            break;
        case 2:
            echo $menu->withdrawMoneyMenu($texts);
            break;
        case 3:
            echo $menu->checkBalanceMenu($texts, $user, $db);
            break;
        default:
            $ussd_level = count($texts) - 1;
            $menu->recordTheStageInvalidOptionWasEntered($sessionId, $ussd_level, $db);
            echo "CON Invalid option\n". $menu->mainMenuRegistered($user->getUserName($db));
    }
}
elseif ($text && !$user->isUserRegistered($db)) { // User is not registered and text is not empty (Sub menu)
    $texts = explode('*', $text);

    if ($texts[0] == 1) {
        echo $menu->registerMenu($texts, $phoneNumber, $db);
    }
    else {
        $ussd_level = count($texts) - 1;
        echo "CON Invalid option\n". $menu->mainMenuUnregistered();
    }
}