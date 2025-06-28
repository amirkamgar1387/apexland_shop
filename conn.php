<?php
session_start();

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Your database username, 'root' is common for XAMPP
define('DB_PASSWORD', ''); // Your database password, empty is common for XAMPP
define('DB_NAME', 'apexland_shop');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn->connect_error){
    die("ERROR: Could not connect. " . $conn->connect_error);
}

// Set charset to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");

/**
 * Fetches the current USD to IRR exchange rate.
 *
 * @return float The exchange rate.
 */
function getDollarToIrrRate() {
    // --- Placeholder ---
    // In a real application, you would fetch this from a reliable API.
    // For example, using cURL to connect to an exchange rate API endpoint.
    // For now, we'll use a static value for development purposes.
    $staticRate = 580000; // Example: 1 USD = 580,000 IRR
    return $staticRate;
}

/**
 * Formats a number as Iranian Rial.
 *
 * @param float $number The number to format.
 * @return string The formatted price with the unit.
 */
function format_irr($number) {
    return number_format($number, 0) . ' ریال';
}

/**
 * دریافت نرخ لحظه‌ای دلار به ریال از وان سرویس (one-api.ir)
 * خروجی: نرخ دلار به ریال یا false در صورت خطا
 */
function getDollarToIrrRateFromOneApi() {
    $token = '960543:68529ca2dc229';
    $url = "https://one-api.ir/DigitalCurrency/?token={$token}";
    $response = @file_get_contents($url);
    if ($response === false) return false;
    $data = json_decode($response, true);
    if (!isset($data['status']) || $data['status'] != 200) return false;
    foreach ($data['result'] as $item) {
        if (isset($item['symbol']) && $item['symbol'] === 'usdt') {
            return floatval($item['current_price']);
        }
    }
    return false;
}

function getDollarToIrrRateDynamic() {
    $apiKey = '0af5d635302754992b240c09'; // کلید API شما
    $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD";
    $response = @file_get_contents($url);
    if ($response === false) return false;
    $data = json_decode($response, true);
    if (isset($data['conversion_rates']['IRR'])) {
        return floatval($data['conversion_rates']['IRR']);
    }
    return false;
}

function getIranMarketDollarRate() {
    $url = "https://api.tgju.org/v1/price/latest";
    $response = @file_get_contents($url);
    if ($response === false) return false;
    $data = json_decode($response, true);
    if (isset($data['usd']['p'])) {
        // حذف کاما و تبدیل به عدد
        return intval(str_replace(',', '', $data['usd']['p']));
    }
    return false;
}

function getNavasanDollarRate() {
    $apiKey = 'free8bgFD4vyFXRvmbrEeTIm2NjotKtV'; // کلید API شما
    $url = "http://api.navasan.tech/latest/?api_key={$apiKey}";
    $response = @file_get_contents($url);
    if ($response === false) return false;
    $data = json_decode($response, true);
    // نرخ فروش دلار بازار آزاد (usd_sell)
    if (isset($data['usd_sell']['value'])) {
        return intval(str_replace(',', '', $data['usd_sell']['value']));
    }
    return false;
}

$dollar_rate = getNavasanDollarRate();
if ($dollar_rate === false) {
    $dollar_rate = 830000; // مقدار پیش‌فرض
} else {
    $dollar_rate = $dollar_rate * 10; // تبدیل تومان به ریال
}

require_once 'conn.php';

?> 