<?php
namespace TimeShow\Sms\Drivers;

use TimeShow\Sms\Contracts\Driver;
use Illuminate\Support\Arr;

class ALiYun extends Driver
{
    private $config = [];

    private $appKey;
    private $appSecret;

    private $host = 'http://dysmsapi.aliyuncs.com/?';
    private $region = "cn-hangzhou";
    private $singleSendUrl = 'SendSms';
    private $method = "GET";

    public function __construct($config)
    {
        $this->config = $config;
        $this->transformConfig();
    }

    protected function transformConfig()
    {
        $this->appKey = Arr::pull($this->config, 'appKey');
        $this->appSecret = Arr::pull($this->config, 'appSecret');
        $this->setSignature();
        $this->setTemplateId(Arr::pull($this->config, 'templateId'));
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    private function computeSignature($parameters)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = $this->method . '&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->appSecret . '&', true));

        return $signature;
    }

    public function send($mobile, $send = true)
    {
        $url = $this->host;

        $requestParams = array(
            'TemplateParam' => json_encode($this->templateVar),
            'PhoneNumbers' => $mobile,
            'Signature' => $this->signature,
            'TemplateCode' => $this->templateId
        );

        if ($send) {
            return $this->curl($url, $requestParams);
        }

        return $requestParams;
    }

    /**
     * @param $url
     * @param array $requestParams
     * @return array $result
     * @return int $result[].code 返回0则成功，返回其它则错误
     * @return string $result[].msg 返回消息
     * @return mixed $result[].verifyCode 验证码
     */
    protected function curl($url, $requestParams)
    {
        // 注意使用GMT时间
        $timezone = date_default_timezone_get();
        date_default_timezone_set("GMT");
        $timestamp = date('Y-m-d\TH:i:s\Z');
        date_default_timezone_set($timezone);

        // 其他请求参数公共参数
        $publicParams = array(
            'RegionId' => $this->region,
            'Format' => 'JSON',
            'Version' => '2017-05-25',
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(),
            'AccessKeyId' => $this->appKey,
            'Timestamp' => $timestamp,
            'Action' => $this->singleSendUrl
        );

        $publicParams = array_merge($requestParams, $publicParams);
        $signature = $this->computeSignature($publicParams);
        $publicParams = array_merge(['Signature' => $signature], $publicParams);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . http_build_query($publicParams));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpResponse = $this->httpResponse($ch);
        $result = $this->transformerResponse($httpResponse);
        curl_close($ch);

        return $result;
    }

    protected function transformerResponse($httpResponse)
    {
        if (empty($httpResponse['error'])) {
            $response = json_decode($httpResponse['jsonData'], true);
            if ($response['Code'] == 'OK') {
                $result = ['code' => 0, 'msg' => '发送成功', 'verifyCode' => $this->verifyCode];
            } else {
                $result = ['code' => time(), 'msg' => $response['Code']];
            }
            unset($response);
        } else {
            $result = ['code' => time(), 'msg' => $httpResponse['error']];
        }

        return $result;
    }
}



