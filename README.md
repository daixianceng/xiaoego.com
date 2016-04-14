xiaoego.com 网站
===============================

该项目包含xiaoego.com主要源码，使用Yii2开发。

若您想学习Yii2请至http://www.yiiframework.com/doc-2.0/guide-index.html

安装
----------

```
# clone the project
git clone https://github.com/daixianceng/xiaoego.com.git

cd xiaoego.com

# install the composer asset plugin globally, if you haven't done so before
composer global require "fxp/composer-asset-plugin:~1.1.1"

# install the dependent composer packages
composer install

# initialize the application, choose "development"
./init
```

服务器配置
----------
建议按照以下方式配置virtual host：

* `xiaoego.local`         => `xiaoego.com/frontend/web`
* `backend.xiaoego.local` => `xiaoego.com/backend/web`
* `store.xiaoego.local`   => `xiaoego.com/store/web`
* `m.xiaoego.local`       => `xiaoego.com/m/web`
* `image.xiaoego.local`   => `xiaoego.com/image`

数据库
----------
该项目使用Mysql数据库开发，数据库文件：`xiaoego.com/common/data/db/db.sql`

项目配置
----------
配置文件在每个模块的`config/`目录下，这并不是全部：

* `xiaoego.com/m/web/index.php`：移动端入口脚本，内有微信公众号`AppId`和`appSecret`配置；
* `xiaoego.com/vendor/payment/wxpay/lib/WxPay.Config.php`：微信支付配置文件；
* `xiaoego.com/vendor/payment/alipay/alipay.config.php`：支付宝配置文件。

信息
----------
* 后端管理员帐号：用户名：`admin` 密码：`123123`；
* 填充数据顺序：添加学校->添加建筑->添加营业点->添加营业点用户->添加商品；
* 商品封面图片大小：400\*400px；商品详情图片大小：600\*400px。

目录
----------

```
common
    config/              包含全局配置文件
    data/                包含项目需要的数据
        db/              包含数据库文件
    mail/                包含e-mail视图文件
    models/              包含共有的model类
console
    config/              包含console配置文件
    controllers/         包含console控制器(命令行)
    migrations/          包含数据库migrations
    models/              包含console需要的model类
    runtime/             包含console运行时生成的文件
backend
    assets/              包含后端资源类
    config/              包含后端配置文件
    controllers/         包含后端控制器
    models/              包含后端需要的model类
    runtime/             包含后端运行时生成的文件
    views/               包含后端视图文件
    web/                 包含后端入口脚本和web资源
    widgets/             包含后端小部件
frontend
    assets/              包含前端资源类
    config/              包含前端配置文件
    controllers/         包含前端控制器
    models/              包含前端需要的model类
    runtime/             包含前端运行时生成的文件
    themes/              包含前端主题文件
    web/                 包含前端入口脚本和web资源
    widgets/             包含前端小部件
m
    common/              包含移动端默认的控制器、模型文件
    config/              包含移动端配置文件
    modules/             包含版本化模块
    runtime/             包含移动端运行时生成的文件
    web/                 包含移动端web资源（ionic）
        api/             包含移动端入口脚本
store
    assets/              包含店铺端资源类
    config/              包含店铺端配置文件
    controllers/         包含店铺端控制器
    models/              包含店铺端需要的model类
    runtime/             包含店铺端运行时生成的文件
    views/               包含店铺端视图文件
    web/                 包含店铺端入口脚本和web资源
    widgets/             包含店铺端小部件
image
    cover/               包含商品封面图片（400*400）
    goods/               包含商品详情图片（600*400）
vendor/                  包含第三方依赖包
environments/            包含环境初始化覆盖文件
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```

二次开发
----------
请联系 `424464282@qq.com`

技术链接
----------
* [Yii Framework](http://www.yiiframework.com/)
* [Bootstrap](http://getbootstrap.com/)
* [AngularJS](https://angularjs.org/)
* [Ionic Framework](http://ionicframework.com/)
