# Laravel 5 微信小程序扩展

## 小程序API接口

* 用户登录：[wx.login](https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html)
* 获取用户信息:[wx.getUserInfo](https://mp.weixin.qq.com/debug/wxadoc/dev/api/open.html#wxgetuserinfoobject)

## 安装

执行以下命令安装最新稳定版本:

```bash
composer require cyd622/WechatApp
```

或者添加如下信息到你的 `composer.json` 文件中 :

```json
"cyd622/WechatApp": "1.*"
```

然后注册服务提供者到 Laravel中 具体位置：`/config/app.php` 中的 `providers` 数组:

```php
WechatApp\WechatAppServiceProvider::class,
```
发布配置文件: 

```bash
php artisan vendor:publish --tag=wechatApp
```
命令完成后，会添加一个`wechatApp.php`配置文件到您的配置文件夹 如 : `/config/wechatApp.php`。

生成配置文件后，将小程序的 `AppID` 和 `AppSecret` 填写到 `/config/wechatApp.php` 文件中

## 在Laravel 5控制器中使用 (示例)

```php
...

use WechatApp\WechatAppAuth;

class WechatAppController extends Controller
{
    protected $WechatApp;

    function __construct(WechatAppAuth $WechatApp)
    {
        $this->WechatApp = $WechatApp;
    }

    
    public function getWxUserInfo()
    {
        //code 在小程序端使用 wx.login 获取
        $code = request('code', '');
        //encryptedData 和 iv 在小程序端使用 wx.getUserInfo 获取
        $encryptedData = request('encryptedData', '');
        $iv = request('iv', '');

        //根据 code 获取用户 session_key 等信息, 返回用户openid 和 session_key
        $userInfo = $this->WechatApp->getLoginInfo($code);

        //获取解密后的用户信息
        return $this->WechatApp->getUserInfo($encryptedData, $iv);
    }
}
```

用户信息返回格式:

```
{
    "openId": "xxxx",
    "nickName": "xxx",
    "gender": 1,
    "language": "zh_CN",
    "city": "",
    "province": "Shanghai",
    "country": "CN",
    "avatarUrl": "http://wx.qlogo.cn/mmopen/xxxx",
    "watermark": {
        "timestamp": 1495867603,
        "appid": "your appid"
    }
}
```

## 小程序端获取 code、iv、encryptedData 向服务端发送请求示例代码：

```javascript
//调用登录接口
wx.login({
    success: function (response) {
        var code = response.code
        wx.getUserInfo({
            success: function (resp) {
                wx.request({
                    url: 'your domain',
                    data: {
                        code: code,
                        iv: resp.iv,
                        encryptedData: resp.encryptedData
                    },
                    success: function (res) {
                        console.log(res.data)
                    }
                })
            }
        })
    },
    fail:function(){
        ...
    }
})
```

> 如有bug，请在 [Issues](https://github.com/cyd622/wechat_app/issues) 中反馈，非常感谢！
