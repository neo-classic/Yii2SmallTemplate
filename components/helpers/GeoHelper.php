<?php
namespace app\components\helpers;

class GeoHelper
{
    public static function getYandexCoords($address)
    {
        $params = [
            'geocode' => str_replace(" ", "+", $address),
            // адрес
            'format' => 'json',
            // формат ответа
            'results' => 1,
            // количество выводимых результатов
            'key' => 'AJGwhFIBAAAAvLDQXgIAgQnEMS5bagfJWqpQb21xBoVOBaoAAAAAAAAAAAC38k5HuNgVfNPQdTgbDjeZ72MZPw==',
            // ваш api key
        ];
        $response = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($params)));

        if ($response && $response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0) {
            return $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
        }
    }
} 