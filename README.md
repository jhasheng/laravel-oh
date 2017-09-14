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

- 安装依赖

```bash
composer reqiure jhasheng/laravel-oh

php artisan vendor:publish
```

## 命令

```bash
# 生成根证书
php artisan oh:ca

# 生成中间商 CA 证书
php artisan oh:ca -T intermediate

# 生成 web 服务端证书
php artisan oh:ca -T server -A example.com
```
