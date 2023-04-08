<?php


namespace App;

use AfricasTalking\SDK\AfricasTalking;
use App\Bases\DB;
use App\Utility;

$secret_path = __DIR__.'/../secrets.php';
require($secret_path);

class Sms
{
    protected $phone;
    protected $AT;

    public function __construct($phone)
    {
        $this->phone = $phone;
        $this->AT = new AfricasTalking(Utility::API_USERNAME, API_KEY);
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function sendSMS($message)
    {
        $sms = $this->AT->sms();

        // Use the service
        return $sms->send([
            'to'      => $this->phone,
            'message' => $message,
            'from' => SMS_SHORT_CODE
        ]);
    }
}