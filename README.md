# API службы доставки СДЭК для Yii2

Реализация JSON-протокола обмена данными СДЭК версии 1.5

Чтобы минимизировать число обращений к серверу СДЭК, запросы выполняются с кэшированием,
параметры которого могут быть настроены в компоненте `CdekApi` который наследует `CachingClient`
из пакета `dicr/yii2-http`.

## Конфигурация
Компонент `CdekApi` настраивается в конфиге приложения.

```php
return [
    'components' => [
        'cdek' => [
            'class' => dicr\cdek\CdekApi::class,
            // для тестирования используем тестовые url, логин и пароль
            'baseUrl' => dicr\cdek\CdekApi::URL_INTEGRATION_TEST,
            'login' => dicr\cdek\CdekApi::LOGIN_TEST,
            'password' => dicr\cdek\CdekApi::PASSWORD_TEST,
            // конфиг запроса стоимости доставки по-умолчанию
            'calcRequestConfig' => [
                // город отправителя у нас всегда один, поэтому пропишем его в конфиг по-умолчанию
                'senderCityPostCode' => 614087, // Пермь
                // здесь список тарифов, которые мы выбираем для доставки (можно настроить один в tariffId)
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
/** @var dicr\cdek\CdekApi $api */
$api = Yii::$app->get('cdek');

// запрос списка регионов
$regions = $api->regionRequest()->send();

// запрос списка городов
$cities = $api->cityRequest([
    'countryCode' => 'ru'
])->send();

// запрос списка пунктов самовывоза
$pvz = $api->cityRequest([
    'citypostcode' => 614087
])->send();

// расчет стоимости доставки (город отправителя и список тарифов заданы в конфиге компонента)
$result = $api->calcRequest([
    // город получателя можно либо код СДЭК, либо индекс в `receiverCityPostCode`
    'receiverCityId' => 44, // Москва,
     // из списка настроенных тарифов выбираем тарифы с доставкой от склада до двери
    'modeId' => dicr\cdek\CdekApi::DELIVERY_SKLAD_DOOR, 
    // характеристики посылок (у нас всего одна) 
    'goods' => [
        ['weight' => 0.24, 'volume' => 0.001]
    ]
])->send();
```

Пример настройки и запросов можно посмотреть в тестах `phpunits` (папка `tests`).

Детальная документация по параметрам запроса - в [базе знаний СДЭК](https://confluence.cdek.ru/display/documentation).
