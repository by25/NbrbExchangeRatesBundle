Парсер официального курса валют НБРБ
====================================

Парсер официальных курсов валют Национального банка Республики Беларусь ([Источник данных](http://www.nbrb.by/statistics/Rates/XML/))

## Возможности:

- Получение курсов валют по коду валюты (UAH, USD).
- Получение динамики официального курса белорусского рубля к заданной валюте периодом не более чем за 365 дней.
- Все данные обернуты в объекты
- Кэширование данных (файловый кэш)


## Установка
### Composer

В composer.json:

```json
{
    "require": {
        "submarine/nbrb-exchange-rates-bundle": "~0.1"
    }
}
```



### Регистрация бандла:

```php
<?php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
		// ...
		new Submarine\NbrbExchangeRatesBundle\SubmarineNbrbExchangeRatesBundle(),
	);
	// ...
}
```

### Конфигурация

```yml
# Значения по умолчанию
submarine_nbrb_exchange_rates:
    cache:
        enable: true                    # Включить кэш
        dir: %kernel.cache_dir%/file    # Директория для файлового кэша
        lifetime: 10800                 # Время жизни кэша в секундах
    source:                             # Урлы xml-данных
        url_exchange_rates: 'http://www.nbrb.by/Services/XmlExRates.aspx'
        url_exchange_rates_dynamic: 'http://www.nbrb.by/Services/XmlExRatesDyn.aspx'
    exception: false                    # Выкидывать исключения?
    scaled_name: false                  # Имя валюты в виде: true => "1 доллар США", false => "Доллар США"
```


## Использование

### Получение текущего курса

```php
$data = $container->get('nbrb_exchange_rates.provider')
    ->getRateExchange('USD', new \DateTime()); //За текущую дату
```


Несколько валют:

```php
// Выбранные валюты
$data = $container->get('nbrb_exchange_rates.provider')
    ->getRatesExchanges(['UAH', 'USD', 'EUR'], new \DateTime());
// Все валюты
$data = $container->get('nbrb_exchange_rates.provider')
    ->getAllRatesExchanges(new \DateTime());
```


### Динамика изменения курса

 Период не более чем за 365 дней.

```php
$container->get('nbrb_exchange_rates.provider')
    ->getRatesExchangesDynamic(
        'USD', 
        new \DateTime('2014-01-01'), 
        new \DateTime('2014-05-01')
    );
```
