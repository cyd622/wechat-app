<?php
/**
 * WechatApp
 * User:  tony
 * Email: luffywang622@gmail.com
 * Date:  2017/8/24 024
 * Time:  11:48
 *
 */

namespace WechatApp;

class WechatAppAuth
{

    private $appId;
    private $secret;
    private $code2session_url;
    private $sessionKey;

    public function __construct($appId,$secret)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->code2session_url = "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code";
    }


    /**
     * @param $code
     * @return array|bool|mixed
     */
    public function getLoginInfo($code)
    {
        return $this->authCodeAndCode2session($code);
    }


    /**
     * 解密用户信息
     * @param $encryptedData
     * @param $iv
     * @return array|string
     */
    public function getUserInfo($encryptedData, $iv)
    {
        $dataCrypt = new WXBizDataCrypt($this->appId, $this->sessionKey);
        $decodeData = "";
        $errCode = $dataCrypt::decryptData($encryptedData, $iv, $decodeData);
        if ($errCode != 0) {
            return [
                'code' => 10001,
                'message' => 'encryptedData 解密失败'
            ];
        }
        // 解密后返回的是JSON,这里统一转换Array
        return json_decode($decodeData,1);
    }

    /**
     * 根据 code 获取 session_key 等相关信息
     * @param $code
     * @return array|bool|mixed
     */
    private function authCodeAndCode2session($code)
    {
        $code2session_url = sprintf($this->code2session_url, $this->appId, $this->secret, $code);
        $userInfo = $this->httpRequest($code2session_url);
        if (!isset($userInfo['session_key'])) {
            return [
                'code' => 10000,
                'message' => '获取 session_key 失败',
            ];
        }
        $this->sessionKey = $userInfo['session_key'];
        return $userInfo;
    }

    private function httpRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if ($output === FALSE) {
            return false;
        }
        curl_close($curl);
        return json_decode($output, JSON_UNESCAPED_UNICODE);
    }

}