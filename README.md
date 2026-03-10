<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://travis-ci.org"><img src="https://travis-ci.org.svg" alt="Build Status"></a>
  <a href="https://packagist.org"><img src="https://img.shields.io" alt="Total Downloads"></a>
  <a href="https://packagist.org"><img src="https://img.shields.io" alt="Latest Stable Version"></a>
  <a href="https://packagist.org"><img src="https://img.shields.io" alt="License"></a>
</p>

<p align="center">
  <img src="https://lh3.googleusercontent.com" width="800" alt="Admin Dashboard">
</p>

---

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
- **Tools:** phpMyAdmin tích hợp sẵn.

## 🚀 Project Setup

### 1. Khởi động môi trường Docker
```bash
./vendor/bin/sail up -d
