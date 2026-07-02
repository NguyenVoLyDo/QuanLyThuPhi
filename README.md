# 🎓 Hệ Thống Quản Lý Thu Phí Học Sinh

[![CI/CD Pipeline](https://github.com/NguyenVoLyDo/QuanLyThuPhi/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/NguyenVoLyDo/QuanLyThuPhi/actions/workflows/ci-cd.yml)
[![Docker](https://img.shields.io/badge/Docker-Enabled-blue?logo=docker)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)](https://www.mysql.com/)

Hệ thống **Quản Lý Thu Phí** là một ứng dụng web toàn diện được thiết kế để số hóa và tự động hóa quy trình quản lý, theo dõi và thu học phí tại các cơ sở giáo dục. Dự án áp dụng kiến trúc hiện đại với quy trình **CI/CD tự động** và triển khai bằng **Docker** nhằm đảm bảo tính ổn định cao nhất.

---

## ✨ Tính Năng Nổi Bật

- 👥 **Quản lý Học Sinh & Lớp Học:** Thêm mới, cập nhật, tìm kiếm thông tin học sinh và phân bổ lớp học dễ dàng.
- 💰 **Quản lý Các Khoản Thu:** Định nghĩa và theo dõi các khoản phí (học phí, bảo hiểm, quỹ lớp...).
- 📊 **Thống Kê & Báo Cáo:** Xuất báo cáo trực quan về tình hình đóng học phí, danh sách nợ phí.
- 🔐 **Bảo Mật:** Phân quyền người dùng chặt chẽ, kiểm tra bảo mật (Security Scan) định kỳ.
- 🚀 **Automation (CI/CD):** Tự động kiểm thử, tự động build và tự động triển khai (Zero-downtime deploy).

---

## 🛠️ Công Nghệ Sử Dụng

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla/jQuery)
- **Backend:** PHP 8.2 (Thuần)
- **Database:** MySQL 8.0
- **DevOps:** 
  - Docker & Docker Compose
  - GitHub Actions (CI/CD)
  - Self-hosted Runner
- **Kiểm thử tự động:** Cypress (E2E Testing)

---

## 🚀 Hướng Dẫn Cài Đặt (Local Development)

Yêu cầu máy tính đã cài đặt **Docker** và **Docker Compose**.

1. **Clone mã nguồn về máy:**
   ```bash
   git clone https://github.com/NguyenVoLyDo/QuanLyThuPhi.git
   cd QuanLyThuPhi
   ```

2. **Khởi chạy hệ thống bằng Docker:**
   ```bash
   docker compose up -d --build
   ```
   *(Lần chạy đầu tiên sẽ mất 1-2 phút để tải Base Image và khởi tạo Database).*

3. **Truy cập ứng dụng:**
   - **Frontend:** [http://localhost:8080/fe/](http://localhost:8080/fe/)
   - **Backend API:** [http://localhost:8080/be/](http://localhost:8080/be/)

---

## ☁️ Quy Trình CI/CD Automation

Dự án áp dụng quy trình **CI/CD hoàn toàn tự động** thông qua GitHub Actions:

### 1. Continuous Integration (CI)
Mỗi khi có code mới được Push hoặc Pull Request vào nhánh `main`:
- Tự động chạy **PHP Linting** để phát hiện lỗi cú pháp.
- Kiểm tra tính toàn vẹn của cấu trúc file và cơ sở dữ liệu (`database.sql`).
- Nếu có lỗi, Pipeline sẽ lập tức chặn việc deploy.

### 2. Continuous Deployment (CD)
Khi CI báo xanh (thành công):
- GitHub Actions tự động build **Docker Image** và đẩy (push) lên **Docker Hub**.
- Kích hoạt **Self-hosted Runner** trên máy chủ Production để tự động kéo Image mới về.
- Tự động khởi động lại Container web mà **không làm gián đoạn Database** (Zero-downtime).
- Chạy Health Check tự động, nếu lỗi sẽ tự động rollback.

---

## 🛡️ Hướng Dẫn Quản Trị Server (Production)

Hệ thống được thiết kế để chạy 24/7 trên máy ảo (VM) Linux.

### Xem trạng thái hệ thống:
```bash
docker compose ps
docker compose logs -f web    # Xem log của ứng dụng PHP
docker compose logs -f db     # Xem log của MySQL
```

### Sao lưu (Backup) Database:
Dữ liệu được lưu trữ an toàn trong Docker Volume. Để export file `.sql`:
```bash
docker exec -t quanlythuphi-db mysqldump -u student_user -pSecurePassword2026! student_fee_management > backup_$(date +%F).sql
```

### Khôi phục (Restore) Database:
```bash
docker exec -i quanlythuphi-db mysql -u student_user -pSecurePassword2026! student_fee_management < backup_file.sql
```

---

## 👨‍💻 Đội Ngũ Phát Triển
**Nhóm 10 - Quản Lý Thu Phí**
- Bùi Trường Quyền
- Nguyễn Dương Sơn
- Phạm Tiến Đạt
- Phạm Kiên Trung

*Phát triển cho đồ án môn học - Đảm bảo chất lượng phần mềm và Triển khai tự động.*
