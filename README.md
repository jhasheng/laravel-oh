# Laravel Openssl Helper
> 基于 laravel 的证书生成辅助工具

## 功能

- 生成 CA 根证书
- 生成多级 CA 证书
- 生成 Web 服务器证书

## 环境要求

- PHP7
- openssl 扩展
- Laravel 5.*

## 安装

```bash
# 安装依赖
composer reqiure jhasheng/laravel-oh

# 生成配置
php artisan vendor:publish

# 添加 SP 到 config/app.php
\Purple\OpensslHelper\OpensslHelperServiceProvider::class
```

## 命令

```bash
# 生成根证书
php artisan oh:ca

# 生成中间商 CA 证书
php artisan oh:ca -T intermediate

# 生成 web 服务端证书
php artisan oh:ca -T server -A example.com -D *.example.com -D *.foo.com -D foo.com -I 192.168.1.123
# 生成的证书可用于 exmaple.com 及其所有的二级域名，foo.com 及基所有的二级域名，IP 地址为 192.168.1.123
```

## 参数说明

```bash
-U, --organizationUnitName[=ORGANIZATIONUNITNAME]  组织单位名称 [default: "Test"]
-C, --countrName[=COUNTRNAME]                      国家缩写 [default: "CN"]
-N, --name[=NAME]                                  CA 别名，用于存储路径 [default: "Test"]
-T, --type[=TYPE]                                  类型 [default: "ca"]
-R, --rootCA[=ROOTCA]                              根 CA 名称 [default: "Test"]
-A, --commonName[=COMMONNAME]                      通用名称，签发 web 服务器时为主域名 [default: "example.com"]
-I, --IP[=IP]                                      可选 IP (multiple values allowed)
-D, --DNS[=DNS]                                    可选 DNS (multiple values allowed)
-L, --URL[=URL]                                    可选 URL (multiple values allowed)
-O, --organizationName[=ORGANIZATIONNAME]          组织名称 [default: "Test"]
```

## 签发步骤

- 生成根 CA
- 生成中间商 CA （可选）
- 签发证书（可以选择根 CA 进行签发，也可以选择中间商 CA）

## 注意

> 此程序产生的证书仅用于本地开发使用
