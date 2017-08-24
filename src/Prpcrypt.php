<?php
/**
 * Prpcrypt加密解密
 * User:  tony
 * Email: luffywang622@gmail.com
 * Date:  2017/8/24 024
 * Time:  11:48
 *
 */

namespace WechatApp;

use Exception;

class Prpcrypt
{
    public static $key;

    public function __construct($k)
    {
        self::$key = $k;
    }

    /**
     * 对密文进行解密
     * @param string $aesCipher 需要解密的密文
     * @param string $aesIV 解密的初始向量
     * @return array 解密得到的明文
     */
    public static function decrypt($aesCipher, $aesIV)
    {

        try {

            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

            mcrypt_generic_init($module, self::$key, $aesIV);

            //解密
            $decrypted = mdecrypt_generic($module, $aesCipher);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return [ErrorCode::$IllegalBuffer, null];
        }

        try {
            //去除补位字符
            $result = PKCS7Encoder::decode($decrypted);

        } catch (Exception $e) {
            return [ErrorCode::$IllegalBuffer, null];
        }
        return [0, $result];
    }
}