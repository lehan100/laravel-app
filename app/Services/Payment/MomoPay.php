<?php

namespace App\Services\Payment;

// header('Content-type: text/html; charset=utf-8');

// use Illuminate\Support\Facades\Http;

class MomoPay
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;
    private $bankCode;
    private $requestType;
    private $orderID;
    private $orderInfo;
    private $extraData;
    private $amount;
    private $requestId;
    private $returnUrl;
    private $notifyurl;
    public function __construct()
    {
        $this->setPartnerCode("MOMOBKUN20180529");
        $this->setAccessKey("klm05TvNBzhg7h7j");
        $this->setSecretKey("at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa");
        $this->setEndpoint("https://test-payment.momo.vn/gw_payment/transactionProcessor");
        $this->setOrderInfo("Thanh toán qua MoMo");
        $this->setExtraData("merchantName=MoMo Partner");
    }
    public function setPartnerCode($partnerCode = "")
    {
        if ($partnerCode != "") {
            $this->partnerCode  = $partnerCode;
        }
        return $this;
    }
    public function setAccessKey($accessKey = "")
    {
        if ($accessKey != "") {
            $this->accessKey  = $accessKey;
        }
        return $this;
    }
    public function setSecretKey($secretKey = "")
    {
        if ($secretKey != "") {
            $this->secretKey  = $secretKey;
        }
        return $this;
    }
    public function setRequestType($requestType = "")
    {
        if ($requestType != "") {
            $this->requestType  = $requestType;
        }
        return $this;
    }
    public function setEndpoint($endpoint = "")
    {
        if ($endpoint != "") {
            $this->endpoint = $endpoint;
        }
        return $this;
    }
    public function setOrderId($orderID = "")
    {
        if ($orderID != "") {
            $this->orderID = $orderID;
        }
        return $this;
    }
    public function setOrderInfo($orderInfo = "")
    {
        if ($orderInfo != "") {
            $this->orderInfo = $orderInfo;
        }
        return $this;
    }
    public function setExtraData($extraData = "")
    {
        if ($extraData != "") {
            $this->extraData = $extraData;
        }
        return $this;
    }
    public function setRequestId($requestId = "")
    {
        if ($requestId != "") {
            $this->requestId = $requestId;
        }
        return $this;
    }
    public function setReturnUrl($returnUrl = "")
    {
        if ($returnUrl != "") {
            $this->returnUrl = $returnUrl;
        }
        return $this;
    }
    public function setNotifyurl($notifyurl = "")
    {
        if ($notifyurl != "") {
            $this->notifyurl = $notifyurl;
        }
        return $this;
    }
    public function setBankCode($bankCode = "")
    {
        if ($bankCode != "") {
            $this->bankCode = $bankCode;
        }
        return $this;
    }
    public function setAmount($amount = 0)
    {
        if ($amount > 0) {
            $this->amount = $amount;
        }
        return $this;
    }
    public function rawHash()
    {
        $query = "partnerCode=" . $this->partnerCode . "&accessKey=" . $this->accessKey . "&requestId=" . $this->requestId . "&amount=" . $this->amount . "&orderId=" . $this->orderID . "&orderInfo=" . $this->orderInfo . "&returnUrl=" . $this->returnUrl . "&notifyUrl=" . $this->notifyurl . "&extraData=" . $this->extraData;
        if ($this->requestType == 'payWithMoMoATM') {
            $query = "partnerCode=" . $this->partnerCode . "&accessKey=" . $this->accessKey . "&requestId=" . $this->requestId . "&bankCode=" . $this->bankCode . "&amount=" . $this->amount . "&orderId=" . $this->orderID . "&orderInfo=" . $this->orderInfo . "&returnUrl=" . $this->returnUrl . "&notifyUrl=" . $this->notifyurl . "&extraData=" . $this->extraData . "&requestType=" . $this->requestType;
        }
        return $query;
    }
    public function signature()
    {
        if ($this->partnerCode == "" || $this->secretKey == "" ||  $this->accessKey == ""  || $this->requestId == "" || $this->amount == 0 || $this->orderID == "" ||  $this->orderInfo == "" || $this->returnUrl == "" || $this->notifyurl == "" || $this->extraData == "") {
            return null;
        }
        if ($this->requestType == 'payWithMoMoATM' &&  $this->bankCode == "") {
            return null;
        }

        return hash_hmac("sha256", $this->rawHash(), $this->secretKey);
    }
    public function executive()
    {
        if ($this->signature() == null) {

            return false;
        }
        $data = array(
            'partnerCode' => $this->partnerCode,
            'accessKey' =>  $this->accessKey,
            'requestId' =>  $this->requestId,
            'amount' =>  $this->amount . "",
            'orderId' => $this->orderID,
            'orderInfo' =>  $this->orderInfo,
            'returnUrl' => $this->returnUrl,
            'notifyUrl' =>  $this->notifyurl,
            'extraData' =>  $this->extraData,
            'requestType' =>  $this->requestType,
            'signature' =>  $this->signature()
        );

        if ($this->requestType == 'payWithMoMoATM') {
            $data['bankCode'] = $this->bankCode;
        }
        $data = json_encode($data);
        $response = $this->execPostRequest($this->endpoint, $data);
        return json_decode($response, true);
        //return $jsonResult['payUrl'];
    }
    function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post

        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
}
