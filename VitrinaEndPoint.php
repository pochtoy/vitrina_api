<?php
//абстрактный базовый класс
//для реализации надо создать новый класс, унаследованный от этого и реализовать в нем методы интерфейса
//далее у обьекта этого класса вызвать метод processRequest, далее он уже сам вызовет соответсвующую функцию


namespace VitrinaApi;

abstract class VitrinaEndPoint implements iVitrinaEndPoint
{
    public function processRequest(){
        $action=$_POST['method'];
        $param_json=$_POST['param'];
        $param=json_decode($param_json);
        $ret=null;

        //проверяем на то что в post[method] передается название функции из интерфейса
        $r=new \ReflectionClass($this);
        foreach ($r->getInterfaces() as $iname=>$interface){
            if($iname=='VitrinaApi\iVitrinaEndPoint'){
                try {
                    $m=$interface->getMethod($action);
                    //ВАЖНО, методы должны возвращать обьект apiAnswer
                    $ret=call_user_func([$this,$action],$param);
                }catch (\ReflectionException $ex){
                    //метод не найден
                    continue;
                }
            }
        }

        if($ret==null){
            $ret=new apiAnswer();
            $ret->status="error";
            $ret->error_description="method not found";
        }

        echo json_encode($ret);
        exit;
    }
}