<?php
namespace VitrinaApi;

interface iPochtoyEndPoint{

    /** Получение списка пунктов выдачи для страны
     * @param pGetPVZList $param
     * @return aGetPVZList
     */
    function getPVZList($param);

    /** Метод создания нового заказа в магазине
     * Метод должен вызываться после оплаты клиентом заказа на витрине
     * В случае если это первый заказ клиента (идентифицируем по номеру телефона), то на почтой происходит его регистрация
     * $order->payed_sum передается сумма в долларах, которую заплатил клиент
     *
     * возвращает номер заказа в системе почтой, который можно использовать в методе getOrderStatus, для получения информации о заказе
     * а так же список товаров с внутренними номерами товаров на почтой
     *
     * @param pAddOrder $param
     * @return aPayOrder
     */
    function addOrder($param);

    /** Метод получения информации о заказе
     * сразу после создания заказа (addOrder) в ответе будет пустое поле aGetOrderStatus::$packages,
     * а поле aGetOrderStatus::$positions будет список переданных товаров со статусом "wait"
     * после выкупа товаров их статус будет меняться на "purchased"
     * после того как товар приходит на склад, статус меняется на "delivered"
     * после того, как товары пакуют в посылку, информация о товаре переносится с поля aGetOrderStatus::$positions в соответсвующую посылку
     *
     * @param pGetOrderStatus $param
     * @return aGetOrderStatus
     */
    function getOrderStatus($param);

    /**
     * @param pSetPassport $param
     * @return apiAnswer
     */
    function setPassport($param);

    /** Метод получения курса валют относительно доллара
     * @param pGetCurrencies $param в параметре указывается код валюты или оставить пустым для всех возможных
     * @return aGetCurrencies
     */
    function getCurrencies($param);

    /**
     * @param pPackBoxes $param
     * @return aPackBoxes
     */
    function packBoxes($param);

    /**
     * @param pCalc $param
     * @return aCalc
     */
    function calcPVZ($param);

    /**
     * @param pCalc $param
     * @return aCalc
     */
    function calcExpress($param);
}

interface iVitrinaEndPoint{

}

abstract class apiParam{
    public $ip;
}


/**
 * status = "ok"|"error"
 * error numbers:
 *  1 - отсутсвует обязательное поле или неверный формат
 *  2 - внутрення ошибка
 */
class apiAnswer{
    /** @var string */
    public $status;
    /** @var int */
    public $error_number;
    /** @var string */
    public $error_description;
    /** @var string */
    public $error_field;
}

class pGetPVZList extends apiParam{
    /** @var string $country 2-digits ISO code */
    public $country;
}

class aGetPVZList extends apiAnswer {
    /** @var PVZ[]  */
    public $points;
}

class pGetOrderStatus extends apiParam {
    /** @var int */
    public $order_id;
}

class aGetOrderStatus extends apiAnswer{
    /** @var ShopPosition[] список товаров еще не упакованных в исходящую посылку */
    public $positions;
    /** @var Package[] готовые к отправке|отправленные посылки с товарами из этого заказа */
    public $packages;

    /** @var string */
    public $vitrina_id;

    /** @var string */
    public $vitrina_data;

}

class pAddOrder extends apiParam {
    /** @var User $user */
    public $user;

    /** @var Address $address */
    public $address;

    /** @var ShopPosition[] $positions */
    public $positions;

    /** @var float $payed_sum */
    public $payed_sum;

    /** @var string */
    public $vitrina_id;

    /** @var string */
    public $vitrina_data;

}

class aPayOrder extends apiAnswer {
    /** @var int */
    public $order_id;
    /** @var ShopPosition[] */
    public $positions;
}

class User{
    /** @var string $name */
    public $name;
    /** @var string $surname */
    public $surname;
    /** @var string $patronomic */
    public $patronomic;

    /** @var string $email */
    public $email;

    /** @var string $phone */
    public $phone;
}

class ShopPosition{
    /** @var int $id номер товара в системе почтой */
    public $id; //

    /** @var string $name название товара в оригинальном магазине */
    public $name;

    /** @var string $url адрес товара в оригинальном магазине */
    public $url;

    /** @var float $price цена товара в оригинальном магазине */
    public $price;

    /** @var string $color цвет/материал товара в оригинальном магазине */
    public $color;

    /** @var string $size размер товара в оригинальном магазине */
    public $size;

    /** @var string $status */
    public $status; // "wait"|"declined"|"purchased"|"delivered"|"sended"

    /** @var string */
    public $vitrina_id;

    /** @var string */
    public $vitrina_data;
}

class PVZ{
    /** @var int номер пункта в системе почтой */
    public $id;
    /** @var string уникальный код пункта в системе службы отправки */
    public $code;
    /** @var string */
    public $address;
    /** @var double */
    public $longitude;
    /** @var double */
    public $latitude;
    /** @var string */
    public $phone;
    /** @var string */
    public $city;
    /** @var int максимальные размеры в мм */
    public $x;
    /** @var int максимальные размеры в мм */
    public $y;
    /** @var int максимальные размеры в мм */
    public $z;
    /** @var string $raw_data полная информация, которую мы получили об этом пункте от службы доставки (serialize) */
    public $raw_data;
}

class Address{
    /** @var string */
    public $name;
    /** @var string */
    public $surname;
    /** @var string */
    public $patronomic;

    /** @var string */
    public $email;
    /** @var string */
    public $phone;

    /** @var string 2-digits ISO */
    public $country;
    /** @var string */
    public $region;
    /** @var string */
    public $city;
    /** @var string */
    public $zip_code;
    /** @var string */
    public $street;
    /** @var string */
    public $building;
    /** @var string */
    public $apt;

    /** @var int номер пункта доставки (0 если курьерская доставка) */
    public $pvz_id;

}

class Currency{
    public $code;
    public $sale;
    public $purchase;
}

class pGetCurrencies extends apiParam{
    /** @var string */
    public $code; //оставляем пустым для всех валют
}

class aGetCurrencies extends apiAnswer {
    /** @var Currency[] */
    public $currencies;
}

class Package{
    /** @var int номер посылки на почтой */
    public $id;
    /** @var ShopPosition[] товары в посылке */
    public $positions;
    /** @var string  */
    public $status; //"wait"|"sended"
    /** @var float вес в кг */
    public $weight;
    /** @var int длина*/
    public $x;
    /** @var int ширина*/
    public $y;
    /** @var int высота*/
    public $z;

    /** @var string */
    public $tracking;

    /** @var string время отправки со склада по времени склада ISO 8601 (date('c')) */
    public $send_time;

}

class pPackBoxes extends apiParam{
    /** @var VolumeBox[] */
    public $boxes;
}

class aPackBoxes extends apiAnswer{
    /** @var VolumeBox */
    public $box;
}

class VolumeBox{
    /** @var float */
    public $x;
    /** @var float */
    public $y;
    /** @var float */
    public $z;
}

class pCalc extends apiParam{
    /** @var float вес в кг */
    public $weight;

    /** @var VolumeBox размеры в см */
    public $size;

    /** @var Address */
    public $address;

}

class aCalc extends apiAnswer {
    /** @var цена в долларах */
    public $price;
}

class pSetPassport extends apiParam{
    /** @var int номер заказа */
    public $order_id;
    /** @var Passport */
    public $passport;
}

class OrderInfo{
    /** @var int номер заказа в pochtoy.com */
    public $order_id;

    /** @var string идентификатор заказа в витрине */
    public $vitrina_id;

    /** @var string дополнительная информацмя о заказе в витрине */
    public $vitrina_data;

    /** @var ShopPosition[] */
    public $positions;
}

class Passport{
    /** @var инн номер */
    public $inn;
    /** @var кем выдан */
    public $issued_by;
    /** @var дата выдачи в формате дд.мм.гггг */
    public $issued_date;
}

class pGetPayUrl extends apiParam{
    /** @var float сумма списания в рублях */
    public $rub_amount;

    /** @var string номер телефона клиента */
    public $user_phone;

    /** @var string описание выводимое клиенту */
    public $description;

    /** @var string страница, на которую перенаправлять после оплаты */
    public $redirect_url;


}