<?php
namespace TimeShow\Sms\Drivers;

use TimeShow\Sms\Contracts\Driver;
use Illuminate\Support\Arr;

class YunPian extends Driver
{
    protected $config = [];

    private $apiKey;

    private $host = 'https://sms.yunpian.com';
    private $singleSendUrl = '/v2/sms/single_send.json';

    public function __construct($config)
    {
        $this->config = $config;
        $this->transformConfig();
    }

    protected function transformConfig()
    {
        $this->apiKey = Arr::pull($this->config, 'apiKey');
        $this->setSignature();
    }

    public function send($mobile, $send = true)
    {
        $url = $this->host . $this->singleSendUrl;

        $postText = "【{$this->signature}】" . $this->content;

        $postData = [
            'text' => $postText,
            'apikey' => $this->apiKey,
            'mobile' => $mobile,
        ];

        if ($send) {
            return $this->curl($url, $postData);
        }

        return $postData;
    }

    /**
     * @param $url
     * @param array $postData
     * @return array $result
     * @return int $result[].code 返回0则成功，返回其它则错误
     * @return string $result[].msg 返回消息
     * @return mixed $result[].verifyCode 验证码
     */
    protected function curl($url, $postData)
    {
        $headers = array(
            'Accept:text/plain;charset=utf-8',
            'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $httpResponse = $this->httpResponse($ch);
        $result = $this->transformerResponse($httpResponse);
        curl_close($ch);

        return $result;
    }

    protected function transformerResponse($httpResponse)
    {
        if (empty($httpResponse['error'])) {
            $result = array_except(
                json_decode($httpResponse['jsonData'], true),
                ['count', 'fee', 'sid', 'mobile', 'unit']
            );
            $result = array_merge(['code' => 0, 'time' => time(), 'verifyCode' => $this->verifyCode], $result);
        } else {
            $result = ['code' => 1, 'time' => time(), 'message' => $httpResponse['error']];
        }

        return $result;
    }
}
