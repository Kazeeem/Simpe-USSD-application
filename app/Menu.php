<?php

namespace App;


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
                            if (Utility::SEND_SMS) { // Send SMS
                                $sms = new Sms($phone);
                                $message = "Dear " . $name . ",\nWe warmly welcome you to ".Utility::COMPANY_NAME.".\nYour registration was successful.";
                                $sms->sendSMS($message);
                            }

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

    public function sendMoneyMenu($textArray, $sender, $db)
    {
        $level = count($textArray);
        $receiver = null;
        $receiver_name = null;
        $response = "";

        switch ($level) {
            case 1: // Cold
                return 'CON Enter mobile number of the receiver:';
            case 2: // Approaching
                return 'CON Enter amount to send:';
            case 3: // Replied
                return 'CON Enter your PIN:';
            case 4: // Interested
                $receiver_phone = $textArray[1];
                $receiver_mobile_with_country_code = $this->addCountryCodeToPhone($receiver_phone);

                $receiver = new User($receiver_mobile_with_country_code);
                $receiver_name = $receiver->getUserName($db);

                $response .= "CON Send ".number_format($textArray[2])." to ".ucwords($receiver_name)." - ".$receiver_phone."\n";
                $response .= "1. Confirm\n";
                $response .= "2. Cancel\n";
                $response .= Utility::GO_BACK." Back\n";
                $response .= Utility::GO_TO_MAIN_MENU." Main Menu";

                return $response;
            case 5:
                if ($textArray[4] == 1) { // Confirm
                    // Logic for sending the money.
                    $pin = $textArray[3];
                    $amount = $textArray[2];
                    $sender->setPin($pin);
                    $sender_balance = $sender->checkBalance($db) - $amount - Utility::TRANSACTION_FEE;

                    $receiver_phone = $textArray[1];
                    $receiver_mobile_with_country_code = $this->addCountryCodeToPhone($receiver_phone);

                    $receiver = new User($receiver_mobile_with_country_code);
                    $receiver_balance = $receiver->checkBalance($db) + $amount;

                    if (!$sender->correctPin($db)) {
                        return "END Incorrect PIN";
                    }

                    $transaction = new Transaction($amount);
                    $result = $transaction->sendMoney($db, $sender->getUserId($db), $receiver->getUserId($db), $sender_balance, $receiver_balance);

                    if (is_string($result)) {
                        return $result;
                    }
                    else {
                        if (Utility::SEND_SMS) { // Send SMS
                            $sms = new Sms($sender->getPhone());
                            $message = "Dear " . $sender->getName() . ",\nYour money transfer is being processed. Your new account balance at this moment is " . number_format($sender_balance) . "\nThank you for choosing " . Utility::COMPANY_NAME;
                            $sms->sendSMS($message);
                        }

                        return "END Your transaction is being processed. You will receive an SMS shortly.";
                    }
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

    public function withdrawMoneyMenu($textArray, $user, $db): string
    {
        $level = count($textArray);

        switch ($level) {
            case 1:
                return "CON Enter agent number:";
            case 2:
                $agent = new Agent($textArray[1]);
                $agent_name = $agent->getAgentName($db);

                if (!$agent_name) {
                    return "END Invalid agent number.";
                }

                return "CON Enter amount:";
            case 3:
                return "CON Enter your PIN:";
            case 4:
                $user->setPin($textArray[3]);

                if (!$user->correctPin($db)) {
                    return "END Incorrect PIN";
                }

                $agent = new Agent($textArray[1]);
                $agent_name = $agent->getAgentName($db);

                $response = "CON Confirm withrawal of ".number_format($textArray[2])." from agent ".ucwords($agent_name)."\n";
                $response .= "1. Confirm\n";
                $response .= "2. Cancel";

                return $response;
            case 5:
                if ($textArray[4] == 1) { // User confirms withdrawal
                    // Logic to process withdrawal.
                    $agent_no = $textArray[1];
                    $amount = $textArray[2];

                    $balance = $user->checkBalance($db);

                    if (($amount + Utility::TRANSACTION_FEE) > $balance) {
                        return "END Insufficient balance. Your available balance is ".number_format($balance);
                    }

                    $new_balance = $balance - $amount - Utility::TRANSACTION_FEE;

                    $transaction = new Transaction($amount);
                    $agent = new Agent($agent_no);

                    $result = $transaction->withdrawCash($db, $user->getUserId($db), $agent->getAgentId($db), $new_balance);

                    if (is_string($result)) {
                        return $result;
                    }

                    if (Utility::SEND_SMS) { // Send SMS
                        $sms = new Sms($user->getPhone());
                        $message = "Dear " . $user->getName() . ",\nYour withdrawal is being processed. Your new account balance at this moment is " . number_format($new_balance) . "\nThank you for choosing " . Utility::COMPANY_NAME;
                        $sms->sendSMS($message);
                    }

                    return "END Your withdrawal of ".number_format($amount)." is being processed. You will receive your money soon.";
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

    public function checkBalanceMenu($textArray, $user, $db)
    {
        $level = count($textArray);

         switch ($level) {
            case 1:
                return "CON Enter your PIN:";
            case 2:
                // Process the request
                $user->setPin($textArray[1]);

                if (!$user->correctPin($db)) {
                    return "END Incorrect PIN";
                }

                $balance = $user->checkBalance($db);

                if (Utility::SEND_SMS) { // Send SMS
                    $sms = new Sms($user->getPhone());
                    $message = "Dear " . $user->getName() . ",\nYour account balance at this moment is " . number_format($balance) . "\nThank you for choosing " . Utility::COMPANY_NAME;
                    $sms->sendSMS($message);
                }

                return "END Your wallet balance is NGN ".number_format($balance);
            default:
                return "END Invalid request";
        }
    }

    public function menuMiddleware($text, $session_id, $pdo):string
    {
        // Remove entries for going back and going to the main menu
        return $this->removeInvalidContentFromMessage($this->goBack($this->goToMainMenu($text)), $session_id, $pdo);
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

    public function recordTheStageInvalidOptionWasEntered($sessionId, $ussd_level, $pdo): void
    {
        $stmt = $pdo->prepare("INSERT INTO ussd_sessions (gateway_session_id, ussd_level) VALUES (?,?)");
        $stmt->execute([$sessionId, $ussd_level]);
        $stmt = null;
    }

    public function removeInvalidContentFromMessage($ussd_string, $session_id, $pdo): string
    {
        $stmt = $pdo->prepare("SELECT ussd_level FROM ussd_sessions WHERE gateway_session_id=?");
        $stmt->execute([$session_id]);
        $result = $stmt->fetchAll();

        if (count($result) == 0) { // If there was no wrong option chosen, there won't be any record in this table.
            return $ussd_string;
        }

        $array_of_inputs = explode("*", $ussd_string);

        // Remove the unwanted value from the string
        if (count($array_of_inputs) > 1) {
            foreach ($result as $value) {
                unset($array_of_inputs[$value['ussd_level']]);
            }
        }

        $array_of_inputs = array_values($array_of_inputs);

        return join("*", $array_of_inputs);
    }

    public function addCountryCodeToPhone($phone): string
    {
        return Utility::COUNTRY_CODE.substr($phone, 1);
    }
}