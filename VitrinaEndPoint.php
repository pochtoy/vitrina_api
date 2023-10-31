<?php

namespace VitrinaApi;

class VitrinaEndPoint implements iVitrinaEndPoint
{

    public function processRequest(){
        $action=$_POST['method'];
        $param_json=$_POST['param'];

        if($action=="successPaymentCallback"){
            $param=json_decode($param_json);
            $ret=$this->successPaymentCallback($param);
            echo json_encode($ret);
            exit;
        }
    }

    /**
     * @inheritDoc
     */
    function successPaymentCallback($param)
    {
        $ret=new apiAnswer();
        $ret->status="ok";
        // TODO: Implement successPaymentCallback() method.
        // $param->payment_id - передает идентификатор платежа, который был возвращен в методе getPayUrl
        return $ret;
    }
}