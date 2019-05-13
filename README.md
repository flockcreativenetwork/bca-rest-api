

```php

$CBA_API = new CBA_API();
$payload = array(
        'account_number' => 'tt', // 'bn', 'e-rate', 'tt', 'tc'
        'symbol_currency' => 'IDR' // 'IDR','AUD','SGD'
    );

echo $CBA_API->getForex($payload);
$payload = array(
        'corporate_id' => 'BCAAPI2016',
        'account_number' => '0201245680',
        'start_date' => '2016-09-01',
        'end_date' => '2016-09-01'
    );
echo $CBA_API->getTransactions($payload);