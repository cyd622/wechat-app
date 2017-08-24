<?php
/**
 * WXBizDataCrypt
 * User:  tony
 * Email: luffywang622@gmail.com
 * Date:  2017/8/24 024
 * Time:  11:48
 *
 */

namespace WechatApp;


class WXBizDataCrypt
{
    private static $appid;
    private static $sessionKey;

    /**
     * 构造函数
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     * @param $appid string 小程序的appid
     */
    public function __construct($appid, $sessionKey)
    {
        self::$appid = $appid;
        self::$sessionKey = $sessionKey;
    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public static function decryptData($encryptedData, $iv, &$data)
    {
        if (strlen(self::$sessionKey) != 24) {
            return ErrorCode::$IllegalAesKey;
        }
        $aesKey = base64_decode(self::$sessionKey);


        if (strlen($iv) != 24) {
            return ErrorCode::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $prpcrypt = new Prpcrypt($aesKey);
        $result = $prpcrypt::decrypt($aesCipher, $aesIV);

        if ($result[0] != 0) {
            return $result[0];
        }

        $dataObj = json_decode($result[1]);
        if ($dataObj == NULL) {
            return ErrorCode::$IllegalBuffer;
        }
        if ($dataObj->watermark->appid != self::$appid) {
            return ErrorCode::$IllegalBuffer;
        }
        $data = $result[1];
        return ErrorCode::$OK;
    }
}