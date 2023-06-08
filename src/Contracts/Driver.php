<?php
namespace TimeShow\Sms\Contracts;

abstract class Driver
{

    protected $signature;
    protected $templateId;
    protected $content;
    protected $templateVar = [];
    protected $verifyCode;


    /**
     * @return string
     */
    private function getDriverName()
    {
        $formattedClassName = explode('\\', get_called_class());
        if (count($formattedClassName) > 0) {
            $driverFileName = end($formattedClassName);
            $drivers = config("sms.drivers");
            foreach ($drivers as $key => $value) {
                if (strcmp($value['driverFile'], $driverFileName) == 0)
                    return $key;
            }
        }
        throw new \InvalidArgumentException("Unauthorized access.");
    }

    /**
     * @return string
     */
    protected function getTemplateContentByConfig()
    {
        $name = $this->getDriverName();
        return config("sms.drivers.{$name}.templateContent");
    }

    /**
     * @param integer $time
     */
    public function setContentByVerifyCode($time = null)
    {
        $this->verifyCode = $this->makeRandom();
        if (empty($this->content)) {
            $this->content = $this->getTemplateContentByConfig();
        }
        $this->content = str_replace('{verifyCode}', $this->verifyCode, $this->content);
        if (!empty($time)) {
            $this->content = str_replace('{time}', $time, $this->content);
        }
    }

    /**
     * @param array $templateVar
     */
    public function setContentByCustomVar($templateVar = [])
    {
        if (empty($this->content)) {
            $this->content = $this->getTemplateContentByConfig();
        }

        $count = count($templateVar);
        if (is_array($templateVar) && $count > 0) {
            foreach ($templateVar as $key => $value) {
                $this->content = str_replace("{" . $key . "}", $value, $this->content);
            }
        } else {
            $this->content = '';
        }
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = is_string(trim($content)) ? $content : '';
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array $templateVar
     * @param bool $hasKey
     */
    public function setTemplateVar($templateVar = [], $hasKey = false)
    {
        $count = count($templateVar);

        if (is_array($templateVar) && $count > 0) {
            foreach ($templateVar as $key => $value) {
                if ($hasKey) {
                    if ($value == 'verifyCode') {
                        $this->verifyCode = $this->makeRandom();
                        $this->templateVar[$key] = "{$this->verifyCode}";
                    } else {
                        $this->templateVar[$key] = "{$value}";
                    }
                } else {
                    if ($value == 'verifyCode') {
                        $this->verifyCode = $this->makeRandom();
                        $this->templateVar[] = "{$this->verifyCode}";
                    } else {
                        $this->templateVar[] = "{$value}";
                    }
                }
            }
        } else {
            $this->templateVar = [];
        }
    }

    /**
     * @return string
     */
    public function getTemplateVar()
    {
        return $this->templateVar;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature = null)
    {
        $this->signature = trim($signature) ?: trim(config('sms.signature'), '{}');
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $id
     */
    public function setTemplateId($id = null)
    {
        $this->templateId = $id ?: 1;
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @return void
     */
    abstract protected function transformConfig();

    /**
     * @param string $mobile
     * @param bool $send
     * @return mixed
     */
    abstract protected function send($mobile, $send = true);

    /**
     * @param string $url
     * @param array $params
     * @return array
     */
    abstract protected function curl($url, $params);

    /**
     * @param $ch
     * @return array
     */
    abstract protected function transformerResponse($ch);

    /**
     * @param $ch
     * @return array
     */
    protected function httpResponse($ch)
    {
        $retry = 0;
        do {
            $jsonData = curl_exec($ch);
            $retry++;
        } while (curl_errno($ch) && $retry < 3);

        if (curl_errno($ch)) {
            $response = ['error' => 1, 'msg' => curl_error($ch)];
        } else {
            $response = ['error' => 0, 'jsonData' => $jsonData];
        }

        return $response;
    }

    /**
     * @return int
     */
    protected function makeRandom()
    {
        return random_int(100000, 999999);
    }

    /**
     * 生成随机数 （97-122为小写字母  48-57为0到9 65-90为大写字母 其他请查询ASCII表）
     * @param unknown $length 长度
     * @param unknown $start ASCII字符对应的开始位置
     * @param unknown $end ASCII字符对应的结束位置
     * @return string 随机数
     */
    public function makeCode($length, $start = 48, $end = 57)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= chr(mt_rand($start, $end)); //生成php随机数
        }
        return $code;
    }

    /**
     * 生成随机数（数字  字母（区分大小写））
     * @param $length
     * @return array|string
     */
    public function makeStr($length = 16)
    {
        $str = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($str);
        return implode('', array_slice($str, 0, $length));
    }

}
