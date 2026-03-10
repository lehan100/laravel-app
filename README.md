<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
<p align="center">
  <img src="https://lh3.googleusercontent.com/d/1fubhfCqQyIbQVGfQKXXM3s--nzyQcoDG" width="800" alt="Admin Dashboard">
</p>
 
## 📝 Giới thiệu APP

Lập trình full-stack CMS Admin, sử dụng Laravel 12 & Blade Engine.
- **Frontend:** Laravel/Blade kết hợp Bootstrap 5.
- **Backend:** Sử dụng template AdminLTE 2, tập trung tối ưu xử lý logic nghiệp vụ.

## 🗺️ SiteMaps

- **Home:** Dashboard, Quản lý Sitemap, Xem trang chủ.
- **Systems:** Cấu hình trang, Quản lý Cache, Tỉnh thành (Provinces).
- **Catalog:** Danh mục (Category), Sản phẩm (Product), Tin tức (News).
- **Orders:** Quản lý đơn hàng, Tồn kho, Tracking vận chuyển.
- **Store Products:** Thuộc tính sản phẩm (Attributes), Đánh giá (Ratings).
- **Store Promotions:** Mã giảm giá (Coupon), Thiết lập Khuyến mãi (Sales).
- **Media:** Quản lý Vị trí (Positions), Banner quảng cáo.
- **Others:** Liên hệ, Cấu hình khác...
- **Imports/Reports:** *(Đang phát triển)*

## 🛠 Tech Stack

- **Framework:** Laravel 12
- **Database:** MySQL 8.4
- **Search Engine:** Meilisearch (Laravel Scout)
- **Environment:** Docker Sail (PHP 8.4)
- **Cache:** Redis / File
- **Tools:** phpMyAdmin tích hợp sẵn.n.

## 🚀 Project Setup


```bash
# Khởi động môi trường Docker
./vendor/bin/sail up -d
./vendor/bin/sail artisan serv

# Cài đặt ứng dụng

./vendor/bin/sail composer install
./vendor/bin/sail php artisan key:generate
./vendor/bin/sail php artisan migrate --seed

# Cấu hình Meilisearch (Search Engine)

Dể tính năng tìm kiếm sản phẩm hoạt động chính xác (hỗ trợ gõ sai "nuot" -> "nuoc"), hãy chạy lệnh cấu hình:

curl -X PATCH 'http://localhost:7700/indexes/products/settings' \
  -H 'Content-Type: application/json' \
  --data-binary '{
    "filterableAttributes": ["status", "price", "attr_color", "attr_size"],
    "sortableAttributes": ["price", "id"],
    "typoTolerance": { "minWordSizeForTypos": { "oneTypo": 3 } }
  }'

# Sau đó import dữ liệu:
./vendor/bin/sail php artisan scout:import "App\Models\Product"

```
## 🔗 Truy cập nhanh

**Frontend**: http://localhost
**Backend** : http://localhost/admin
**phpMyAdmin**: http://localhost:8080
**Meilisearch Dashboard**: http://localhost:7700
