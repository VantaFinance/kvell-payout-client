# Kvell Payout Client


Клиент для интеграции с сервисом [kvell.group](https://docs.stage.kvell.group/payout/)


## Установка

Минимальная версия PHP: 8.2

1. Запустите команду ```composer require vanta/kvell-payout-client```
2. Установите psr совместимый клиент



## Пример использования:


### Создать билдер

```php

$key     = file_get_contents(filename: '<ваш приватный ssl ключ>');
$builder = RestClientBuilder::create(client: new Client(config: []), signKey: new SignKey(value: $key), apiKey: '<Ваш ApiKey>', secretKey: '<Ваш секретный ключ>')
    ->withEnvironmentStage()
;
```




### Проверка возможности выплаты


```php
$key     = file_get_contents(filename: '<ваш приватный ssl ключ>');
$builder = RestClientBuilder::create(client: new Client(config: []), signKey: new SignKey(value: $key), apiKey: '<Ваш ApiKey>', secretKey: '<Ваш секретный ключ>')
    ->withEnvironmentStage()
;

$result = $builder->createSbpPayoutClient()
     ->startCheckingPossibleToPay(
            new PossibleToPay(
                'Шашков Владислав Максимович',
                '100000000004',
                PhoneNumber::parse('+79994652397'),
                new MoneyPositive(Money::RUB(5000))
            ),
       );
```



### Получить статус проверки возможности выплаты


```php
$key     = file_get_contents(filename: '<ваш приватный ssl ключ>');
$builder = RestClientBuilder::create(client: new Client(config: []), signKey: new SignKey(value: $key), apiKey: '<Ваш ApiKey>', secretKey: '<Ваш секретный ключ>')
    ->withEnvironmentStage()
;

$result = $builder->createSbpPayoutClient()->getStatusPossibleToPay('170444');
```



### Получить заказ по transactionId


```php
$key     = file_get_contents(filename: '<ваш приватный ssl ключ>');
$builder = RestClientBuilder::create(client: new Client(config: []), signKey: new SignKey(value: $key), apiKey: '<Ваш ApiKey>', secretKey: '<Ваш секретный ключ>')
    ->withEnvironmentStage()
;

$result = $builder->createClassicPayoutClient()->getPayout('b1e8fdb4-6562-4992-a933-cf029f9e31d3');
```



### Выплаты на карту

```php
$key     = file_get_contents(filename: '<ваш приватный ssl ключ>');
$builder = RestClientBuilder::create(client: new Client(config: []), signKey: new SignKey(value: $key), apiKey: '<Ваш ApiKey>', secretKey: '<Ваш секретный ключ>')
    ->withEnvironmentStage()
;

$result = $builder->createClassicPayoutClient()
    ->createPayoutClassic(
        request: new Payout(
            description: 'Вывод денег',
            transactionId: 'b1e8fdb4-6562-4992-a933-cf029f9e31d3',
            amount: new MoneyPositive(value: Money::RUB(100)),
            card: new \Vanta\Integration\KvellPayout\Request\Card('4718190802359673'),
        )
    );
```

TODO:
- Тесты