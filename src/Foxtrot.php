<?php

namespace Foxtrot;

use GuzzleHttp\Exception\ClientException;
use Foxtrot\common\Constants;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Foxtrot
{

    protected string $url;

    protected array $errors = [];

    protected Client $client;

    /*
     * Поля для видачі користувачеві
     * */
    protected int $statusCode;

    protected ?string $body;

    protected ?array $headers;

    /*
     * Поля, які будуть використовуватись при парсингу
     * */
    protected Crawler $crawler;

    protected string $parseType;
    protected array $parseData = [];

    public function __construct(string $url) {
        $this->url = $url;
        $this->prepareParse();
    }

    private function prepareParse() : void
    {
        if (!$this->url) {
            $this->errors[] = Constants::ERROR_URL_IS_EMPTY;
        } else {
            $this->getClient();
            try {
                $response = $this->client->request('GET', $this->url);
            } catch (ClientException $e) {
                $response = $e->getResponse();
            }
            $this->statusCode = $response->getStatusCode();
            $this->body = $response->getBody();
            $this->headers = $response->getHeaders();
            if (!in_array($this->statusCode, Constants::SUCCESS_STATUS_CODES)) {
                $this->errors[] = Constants::ERROR_RESPONSE_CODE;
            }

        }
    }

    private function getClient() : void
    {
        $this->client = new Client();
    }

    /*
     * Ґеттери start
     * */
    public function getStatusCode() : ?int
    {
        return $this->statusCode;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

    public function getErrorBySlug(string $slug) : ?string
    {
        return Constants::ERRORS[$slug] ?? false;
    }

    /*
     * Ґеттери end
     * */
    public function parse() : array
    {
        // спочатку потрібно зрозуміти яку сторінку парсимо, це буде таким собі - типом.
        $this->crawler = new Crawler($this->body);

        if ($this->isProductCard()) { // якщо це сторінка товару - парсимо товар
            $this->parseProductCard();
        }

        return [
            'type' => $this->parseType,
            'data' => $this->parseData,
            'errors' => $this->errors,
        ];
    }

    private function isProductCard() : bool
    {
        $this->parseType = 'Undefined';
        // перевірка чи є заголовок товару
        $isProductTitle = (bool)$this->crawler->filter('h1.page__title')->count();
        // чи є на сторінці опції товару
        $isProductTabs = (bool)$this->crawler->filter('#section-properties')->count();
        return $isProductTitle && $isProductTabs;
    }

    private function parseProductCard() : void
    {
        $this->parseType = 'Product';
        // потрібен заголовок товару
        if ($this->crawler->filter('h1.page__title')->count()) {
            $this->parseData['title'] = trim($this->crawler->filter('h1.page__title')->text());
        }

        $jsonData = [];

        if ($this->crawler->filter('[name="product_structured_data"]')->count()) {
            $jsonData = $this->crawler->filter('[name="product_structured_data"]')->text();
            $jsonData = json_decode($jsonData, true);
        }

        if (isset($jsonData['offers'])) {
            $offers = $jsonData['offers'];
            if ($offers['price']) {
                $this->parseData['price'] = $offers['price'];
            }
            if ($offers['priceCurrency']) {
                $this->parseData['currency'] = $offers['priceCurrency'];
            }
            if (isset($offers['availability'])) {
                $this->parseData['availability'] = $offers['availability'] == 'http://schema.org/InStock';
            }
        }

        if (isset($jsonData['brand'])) {
            $brand = $jsonData['brand'];
            if ($brand['name']) {
                $this->parseData['brand'] = $brand['name'];
            }
        }

        if (isset($jsonData['aggregateRating'])) {
            $rating = $jsonData['aggregateRating'];
            $this->parseData['rating'] = $rating['ratingValue'];
        }

        if (isset($jsonData['description'])) {
            $this->parseData['description'] = $jsonData['description'];
        }

        if (isset($jsonData['image'])) {
            $this->parseData['images'] = $jsonData['image'];
        }

        if (isset($jsonData['url'])) {
            $this->parseData['url'] = $jsonData['url'];
        }

        if (isset($jsonData['sku'])) {
            $this->parseData['sku'] = $jsonData['sku'];
        }
    }

}