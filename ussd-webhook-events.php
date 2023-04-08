<?php

require_once('vendor/autoload.php');

use App\Bases\DB;

$pdo = new DB();
$db = $pdo->connectToDB();

$date = $_POST['date'];
$session_id = $_POST['sessionId'];
$service_code = $_POST['serviceCode'];
$network_code = $_POST['networkCode'];
$phone = $_POST['phoneNumber'];
$status = $_POST['status'];
$cost = $_POST['cost'];
$duration_milliseconds = $_POST['durationMillis'];
$input = $_POST['input'];
$last_app_response = $_POST['lastAppResponse'];
$error_message = $_POST['errorMessage'];

saveWebhookNotification($db, $date, $session_id, $service_code, $network_code, $phone, $status, $cost, $duration_milliseconds, $input, $last_app_response, $error_message);

function saveWebhookNotification($pdo, $date, $session_id, $service_code, $network_code, $phone, $status, $cost, $duration_milliseconds, $input, $last_app_response, $error_message) {
    $stmt = $pdo->prepare("INSERT INTO webhook_notifications (date, session_id, service_code, network_code, phone, status, cost, duration_milliseconds, `input`, last_response, error_message) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$date, $session_id, $service_code, $network_code, $phone, $status, $cost, $duration_milliseconds, $input, $last_app_response, $error_message]);
    $stmt = null;
}