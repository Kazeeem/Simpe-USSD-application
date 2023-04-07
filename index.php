<?php

/**
 *
 * This script is meant for Africastalking.com USSD Gateway.
 *
 */

header('Content-type: text/plain');
//header('Content-type: application/x-www-form-urlencoded');

require_once('Menu.php');

// Read the variables sent via POST from our API
$sessionId = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];

$menu = new Menu();
$text = $menu->menuMiddleware($text);

$isRegistered = true;

if (!$text && !$isRegistered) { // User is not registered and text is empty (1st menu which is to register)
    echo $menu->mainMenuUnregistered();
}
elseif (!$text && $isRegistered) { // User is registered and text is empty (1st menu which is to choose what they want to do between (Check balance, Withdraw, Transfer)
    echo $menu->mainMenuRegistered();
}
elseif ($text && $isRegistered) { // User is registered and text is not empty (Sub mmenu)
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
            echo "END Invalid request. Please try again";
    }
}
elseif ($text && !$isRegistered) { // User is not registered and text is not empty (Sub menu)
    $texts = explode('*', $text);

    if ($texts[0] == 1) {
        echo $menu->registerMenu($texts);
    }
    else {
        echo "END Invalid request.";
    }
}