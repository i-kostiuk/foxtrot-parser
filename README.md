# Підключення:

`composer require i-kostiuk/foxtrot-parser `

- ##### laravel:

```php
use Foxtrot\Foxtrot;

$parser = new Foxtrot('https://www.foxtrot.com.ua/uk/shop/led_televizory_samsung_ue43t5300auxua.html');
```
- ##### php:

```php
use Foxtrot\Foxtrot;

require __DIR__ . "/vendor/autoload.php";

$parser = new Foxtrot('https://www.foxtrot.com.ua/uk/shop/led_televizory_samsung_ue43t5300auxua.html');
```

# Використання:

При створення екземпляру класу Foxtrot, в конструктор передаємо лінк на сторінку. Зараз доступний парсинг сторінок:
- Картка товару
- (Незабаром будуть інші)

```php 
$parser = new Foxtrot('https://www.foxtrot.com.ua/uk/shop/led_televizory_samsung_ue43t5300auxua.html');
```

Під капотом у конструкторі робляться деякі попередні перевірки та одразу доступні наступні методи:
- getStatusCode - повертає код відповіді. Успішною вважається 200
```php
$statusCode = $parser->getStatusCode(); // ex: 200
```

- getBody - повертає тіло відповіді. Зазвичай це html, json, xml.
```php
$body = $parser->getBody(); // ex: <!DOCTYPE html><html.......html>
```

- getHeaders - повертає заголовки відповіді
```php
$headers = $parser->getHeaders();
```

- parse - парсить сторінку та повертає результат
```php
$response = $parser->parse();

// Product

/*
Array
(
    [type] => Product
    [data] => Array
        (
            [title] => Телевізор SAMSUNG UE43T5300AUXUA
            [price] => 12999
            [currency] => UAH
            [availability] => 1
            [brand] => SAMSUNG
            [rating] => 4
            [description] => &#12304;&#1058;&#1077;&#1083;&#1077;&#1074;&#1110;&#1079;&#1086;&#1088; SAMSUNG UE43T5300AUXUA&#12305;&#1082;&#1091;&#1087;&#1080;&#1090;&#1080; &#1079;&#1072; 12999 &#1075;&#1088;&#1085; &#9665; &#1060;&#1054;&#1050;&#1057;&#1058;&#1056;&#1054;&#1058; &#9655; &#1110;&#1085;&#1090;&#1077;&#1088;&#1085;&#1077;&#1090;-&#1084;&#1072;&#1075;&#1072;&#1079;&#1080;&#1085; &#8470; &#10102; &#1074; &#1050;&#1080;&#1108;&#1074;&#1110; &#1090;&#1072; &#1059;&#1082;&#1088;&#1072;&#1111;&#1085;&#1110; &#10004; &#1043;&#1072;&#1088;&#1072;&#1085;&#1090;&#1110;&#1103; &#10004; &#1064;&#1074;&#1080;&#1076;&#1082;&#1072; &#1076;&#1086;&#1089;&#1090;&#1072;&#1074;&#1082;&#1072; &#9742; 0-800-300-353
            [images] => Array
                (
                    [0] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_0.jpg
                    [1] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_3.jpg
                    [2] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_6.jpg
                    [3] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_1.jpg
                    [4] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_4.jpg
                    [5] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_8.jfif
                    [6] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_5.jpg
                    [7] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_2.jpg
                    [8] => https://files.foxtrot.com.ua/PhotoNew/img_0_977_3587_1_1_638005703139736423.jpg
                )

            [sku] => 6557914
        )

    [errors] => Array
        (
        )
)
*/
```

- getErrors - повертає всі помилки. Можна отримати на різних етапах (до та після парсингу)
```php
$parser = new Foxtrot('https://www.foxtrot.com.ua/uk/shop/led_telua.html');
$response = $parser->parse();
$errors = $parser->getErrors();

Array
(
    [0] => response_code
)
```

- getErrorBySlug - Повертає текст помилки українською мовою по слагу, який можна отримати із getErrors або parse

```php
$error = $parser->getErrorBySlug('response_code');

// string(47) "Помилковий код відповіді."
```