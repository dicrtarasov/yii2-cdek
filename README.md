# API службы доставки СДЭК для Yii2

## Конфигурация
Конмпонент `CdekApi` настраивается в конфиге приложения.

```php
return [
    'components' => [
        'cdek' => [
            'class' => dicr\cdek\CdekApi::class,
            'baseUrl' => dicr\cdek\CdekApi::URL_TEST,
            'login' => dicr\cdek\CdekApi::LOGIN_TEST,
            'password' => dicr\cdek\CdekApi::PASSWORD_TEST,
            'calcRequestConfig' => [
                'senderCityPostCode' => 614087, // Пермь
                'tariffList' => [
                    ['id' => dicr\cdek\CdekApi::TARIF_POST_S_S],
                    ['id' => dicr\cdek\CdekApi::TARIF_POST_S_D],
                    ['id' => dicr\cdek\CdekApi::TARIF_ECOPOST_S_D],
                    ['id' => dicr\cdek\CdekApi::TARIF_ECOPOST_S_S]
                ],
            ]   
       ]
    ]
];
```

## Запросы к API

```php
use dicr\cdek\CdekApi;

/** @var CdekApi $api */
$api = Yii::$app->cdek;

// запрос списка регионов
$regions = $api->createRegionRequest()->send();

// запрос списка городов
$cities = $api->createCityRequest([
    'countryCode' => 'ru'
])->send();

// запрос списка пунктов самовывоза
$pvz = $api->createCityRequest([
    'citypostcode' => 614087
])->send();

// рассчет стоимости доставки (город отправителя исписок тарифов заданы в конфиге компонента)
$result = $api->createCalcRequest([
    // город получателя можно либо код СДЭК, либо индекс в `receiverCityPostCode`
    'receiverCityId' => 44, // Москва,
     // из списка настроенных тарифов выбираем тарифы с доставкой от склада до двери
    'modeId' => CdekApi::DELIVERY_SKLAD_DOOR, 
    // характеристики посылок (у нас всего одна) 
    'goods' => [
        ['weight' => 0.24, 'volume' => 0.001]
    ]
])->send();
```
Пример настройки и запросов можно посмотреть в тестах `phpunits` (папка `tests`).
