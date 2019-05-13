<?php
require 'guzzle/vendor/autoload.php';
class CBA_API{
    private static $hostUrl = 'https://sandbox.bca.co.id';
    private static $clientID = '09fda548-09a1-42f2-be27-6782c2d2bf64';
    private static $clientSecret = '05342fa3-d6c4-47e1-bd4e-5f7dd5c06cf2';
    private static $APIKey = 'b8b4b8e1-1ab9-4e89-89b0-e2ffd2ad7bc5';
    private static $APISecret = '346d83cc-e0e6-44ae-b581-46a10199b2d0';
    private static $corporate_id = 'BCAAPI2016'; //  Corporate ID. CBAAPI2016 is Sandbox ID
    private static $account_number = '0201245680'; // Account Number. 0201245680 is Sandbox Account
    private static $accessToken = null;
    private static $timeStamp = null;
    private static $client;
    private static $signature = null;

    public function __construct(){
        self::$timeStamp = date('o-m-d').'T'.date('H:i:s').'.'. substr(date('u'), 0, 3).date('P');
        self::$client = new \GuzzleHttp\Client;
        $this->initialToken();
    }

    private function initialToken(){
        $output = self::$client->request('POST', self::$hostUrl . '/api/oauth/token', [
            'verify' => false,
            'headers' => [
                 'Content-Type'  => 'application/x-www-form-urlencoded',
                 'Authorization' => 'Basic '.base64_encode(self::$clientID.':'.self::$clientSecret)
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);
        $output = json_decode($output->getBody(), true);
        return self::$accessToken = $output['access_token'];
    }

    private function getSignature($HTTPMethod, $relativeUrl, $RequestBody = ''){
        $RequestBody = strtolower(hash('sha256', $RequestBody));
        $StringToSign = $HTTPMethod . ":" . $relativeUrl . ":" . self::$accessToken . ":" . $RequestBody . ":" . self::$timeStamp;
        $signature = hash_hmac('sha256', $StringToSign, self::$APISecret);
        return $signature;
    }

    public function getTransactions($payload = array()){

        $path = '/banking/v2/corporates/'. $payload['corporate_id'] .
                '/accounts/' . $payload['account_number'] .
                '/statements?' .
                'EndDate=' . $payload['end_date'] .
                '&StartDate=' . $payload['start_date'];
        $method = 'GET';

        $output = self::$client->request($method, self::$hostUrl . $path, [
            'verify' => false,
            'headers' => [
                 'Authorization' => 'Bearer ' . self::$accessToken,
                 'Content-Type' => 'application/json',
                 'Origin' => $_SERVER['SERVER_NAME'],
                 'X-BCA-Key' => self::$APIKey,
                 'X-BCA-Timestamp' => self::$timeStamp,
                 'X-BCA-Signature' => $this->getSignature($method, $path),
            ]
        ]);
        // echo '<pre>';
        // print_r(json_decode($output->getBody(), true));
        // echo '</pre>';
        // echo $output->getBody(); // response
        // exit;
        return $output->getBody();
    }

    public function getForex($payload = array()){

        $RateType = (empty($payload['rate_type'])) ? 'E-RATE' : $payload['rate_type'];
        $Currency = (empty($payload['symbol_currency'])) ? 'USD' : $payload['symbol_currency'];

        $path = '/general/rate/forex?Currency=' . $Currency . '&RateType=' . $RateType;
        $method = 'GET';

        $output = self::$client->request($method, self::$hostUrl . $path, [
            'verify' => false,
            'headers' => [
                 'Authorization' => 'Bearer ' . self::$accessToken,
                 'Content-Type' => 'application/json',
                 'Origin' => $_SERVER['SERVER_NAME'],
                 'X-BCA-Key' => self::$APIKey,
                 'X-BCA-Timestamp' => self::$timeStamp,
                 'X-BCA-Signature' => $this->getSignature($method, $path),
            ]
        ]);
        // echo '<pre>';
        // print_r(json_decode($output->getBody(), true));
        // echo '</pre>';
        // echo $output->getBody(); // response
        return $output->getBody();
    }

}


$CBA = new CBA_API();
$payload_trans = array(
        'corporate_id' => 'BCAAPI2016',
        'account_number' => '0201245680',
        'start_date' => '2016-08-29',
        'end_date' => '2016-09-01'
    );

$payload_forex = array(
        'rate_type' => 'tt',
        'symbol_currency' => 'IDR'
    );

echo '<pre>';
echo $CBA->getTransactions($payload_trans);
echo '<pre>';
echo $CBA->getForex($payload_forex);

