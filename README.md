

```php

$BCA = new BCA_API();
$payload = array(
        'account_number' => 'tt', // 'bn', 'e-rate', 'tt', 'tc'
        'symbol_currency' => 'IDR' // 'IDR','AUD','SGD'
    );

echo $BCA->getForex($payload);
$payload = array(
        'corporate_id' => 'BCAAPI2016',
        'account_number' => '0201245680',
        'start_date' => '2016-09-01',
        'end_date' => '2016-09-01'
    );
echo $BCA->getStatements($payload);