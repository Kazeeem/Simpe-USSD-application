<?php

namespace App;

use App\Bases\DB;
use App\User;

class Menu
{
    public function mainMenuRegistered($name): string
    {
        $response = "Hello ".ucwords($name).", what do you want to do today? \n";
        $response .= "1. Send Money\n";
        $response .= "2. Withdraw\n";
        $response .= "3. Check Balance\n";

        return $response;
    }

    public function mainMenuUnregistered(): string
    {
        $response = "CON Welcome to this app. Please choose an option. \n";
        $response .= "1. Register\n";

        return $response;
    }

    public function registerMenu($textArray, $phone, $db): string
    {
        $level = count($textArray);

        switch ($level) {
            case 1: // Cold
                return "CON Please enter your full name:";
            case 2: // Approaching
                return 'CON Please set your PIN:';
            case 3: // Replied
                return 'CON Confirm your PIN:';
            case 4: // Interested
                $name = $textArray[1];
                $pin = $textArray[2];
                $confirm_pin = $textArray[3];

                if ($pin != $confirm_pin) {
                    return 'END Your pins do not match. Please try again.';
                }
                else {
                    try {
                        // Register the user
                        $user = new User($phone);
                        $registration_status = $user->setName($name)
                            ->setPin($pin)
                            ->setBalance(Utility::USER_INITIAL_BALANCE)
                            ->register($db);

                        if (is_string($registration_status)) {
                            return 'END ' . $registration_status;
                        } elseif ($registration_status) {
                            // Send SMS
                            return 'END Your registration was successful.';
                        } else {
                            return 'END Sorry, we could not process your request at this time. Please try again later.';
                        }
                    }
                    catch (\Exception $e) {
                        return "END ".$e->getMessage();
                    }
                }
            default:
                return 'END Invalid request';
        }
    }

    public function sendMoneyMenu($textArray)
    {
        $level = count($textArray);

        switch ($level) {
            case 1: // Cold
                return 'CON Enter mobile number of the receiver:';
            case 2: // Approaching
                return 'CON Enter amount to send:';
            case 3: // Replied
                return 'CON Enter your PIN:';
            case 4: // Interested
                $response = "CON Send ".number_format($textArray[2])." to ".$textArray[1]."\n";
                $response .= "1. Confirm\n";
                $response .= "2. Cancel\n";
                $response .= Utility::GO_BACK." Back\n";
                $response .= Utility::GO_TO_MAIN_MENU." Main Menu";

                return $response;
            case 5:
                if ($textArray[4] == 1) { // Confirm
                    // Logic for sending the money.
                    return "END Your transaction is being processed.";
                }
                elseif ($textArray[4] == 2) { // Cancel
                    return "END You have cancelled the transaction. Thank you for using our service.";
                }
                elseif ($textArray[4] == Utility::GO_BACK) {
                    return "END You have requested to go back - PIN";
                }
                elseif ($textArray[4] == Utility::GO_TO_MAIN_MENU) {
                    return "END You have requested to go to main menu.";
                }
                else {
                    return "END Invalid request";
                }
            default:
                return 'END Invalid request';
        }
    }

    public function withdrawMoneyMenu($textArray): string
    {
        $level = count($textArray);

        switch ($level) {
            case 1:
                return "CON Enter agent number:";
            case 2:
                return "CON Enter amount:";
            case 3:
                return "CON Enter your PIN:";
            case 4:
                $response = "CON Confirm withrawal of ".$textArray[2]." from agent ".$textArray[1]."\n";
                $response .= "1. Confirm\n";
                $response .= "2. Cancel";

                return $response;
            case 5:
                if ($textArray[4] == 1) { // User confirms withdrawal
                    // Logic to process withdrawal.
                    return "END Your withdrawal is being processed. You will receive your money soon.";
                }
                elseif ($textArray[4] == 2) {
                    return "END You have canceled the withdrawal.";
                }
                else {
                    return "END Invalid request";
                }
            default:
                return "END Invalid request";
        }
    }

    public function checkBalanceMenu($textArray)
    {
        $level = count($textArray);

         switch ($level) {
            case 1:
                return "CON Enter your PIN:";
            case 2:
                // Process the request
                return "END Your balance check was successful. You will receive an SMS shortly.";
            default:
                return "END Invalid request";
        }
    }

    public function menuMiddleware($text):string
    {
        // Remove entries for going back and going to the main menu
        return $this->goBack($this->goToMainMenu($text));
    }

    public function goBack($text):string
    {
        $textArray = explode("*", $text);

        while(array_search(Utility::GO_BACK, $textArray) != false) {
            $first_index =  array_search(Utility::GO_BACK, $textArray);

            array_splice($textArray, $first_index - 1, 2);
        }

        return join("*", $textArray);
    }

    public function goToMainMenu($text):string
    {
        $textArray = explode("*", $text);

        while(array_search(Utility::GO_TO_MAIN_MENU, $textArray) != false) {
            $first_index =  array_search(Utility::GO_TO_MAIN_MENU, $textArray);

            $textArray = array_slice($textArray, $first_index + 1);
        }

        return join("*", $textArray);
    }

    public function retainMenuForInvalidEntry($sessionId, $user, $ussd_level, $db)
    {
        $stmt = $db->prepare("INSERT INTO ussd_sessions (gateway_session_id, ussd_level, user_id) VALUES (?,?,?)");
        $stmt->execute($sessionId, $ussd_level, $user->getUserId($db));
        $stmt = null;
    }
}