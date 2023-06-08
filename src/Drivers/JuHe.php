<?php
namespace TimeShow\Sms\Drivers;

use TimeShow\Sms\Contracts\Driver;
use Illuminate\Support\Arr;

class JuHe extends Driver
{
    protected $config = [];

    private $key;

    private $host = 'http://v.juhe.cn';
    private $singleSendUrl = '/sms/send?';

    public function __construct($config)
    {
        $this->config = $config;
        $this->transformConfig();
    }

    protected function transformConfig()
    {
        $this->key = Arr::pull($this->config, 'key');
        $this->templateId = Arr::pull($this->config, 'templateId');
        $this->setSignature();
    }

    public function send($mobile, $send = true)
    {
        $url = $this->host . $this->singleSendUrl;

        $postText = $this->content;

        $postData = [
            'tpl_value' => $postText,
            'key' => $this->key,
            'mobile' => $mobile,
            'tpl_id' => $this->templateId,
        ];

        $paramsString = http_build_query($postData);

        // 发起接口网络请求
        $response = null;
        try {
            if ($send) {
                $response = $this->curl($url, $paramsString, 1);
            } else {
                return $paramsString;
            }
        } catch (Exception $e) {
            var_dump($e);
            //此处根据自己的需求进行自身的异常处理
        }
        return $response;
    }

    /**
     * 发起网络请求函数
     * @param string $url 请求的URL
     * @param bool $params 请求的参数内容
     * @param int $ispost 是否POST请求
     * @return bool|string 返回内容
     */
    function curl($url, $params = false, $ispost = 0)
    {
        $httpInfo = [];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            // echo "cURL Error: ".curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        $result = $this->transformerResponse($response);
        curl_close($ch);
        return $result;
    }

    protected function transformerResponse($response)
    {
        if (!$response) {
            return ['status' => false,'time' => time(), 'msg' => "请求异常"];
        }
        $result = json_decode($response, true);
        if (!$result) {
            return ['status' => false,'time' => time(), 'msg' => "请求异常"];
        }
        $errorCode = $result['error_code'];
        if ($errorCode === 0) {
            $data = $result['result'];
            $result = ['status' => true, 'time' => time(), 'msg' => "操作成功", 'data' => $data];
        } else {
            // 请求异常
            $result = ['status' => true, 'time' => time(), 'msg' => "请求异常", 'data' => "请求异常:{$errorCode}_{$result["reason"]}"];
        }

        return $result;
    }
}
