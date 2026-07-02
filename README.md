# Hướng Dẫn Triển Khai Chạy 24/7 Bằng Docker Trên Máy Ảo (VM)

Tài liệu này hướng dẫn từng bước cách cài đặt, chạy và quản lý ứng dụng **Quản Lý Thu Phí** trên máy ảo sử dụng Docker để hệ thống tự động duy trì hoạt động liên tục 24/7.

---

## 1. Yêu Cầu Chuẩn Bị Trên Máy Ảo (VM)

### Cài đặt Docker & Docker Compose (Ví dụ cho Ubuntu Server)

Nếu máy ảo của bạn chưa cài đặt Docker, hãy SSH vào máy ảo và chạy các lệnh sau:

```bash
# Cập nhật danh sách gói tin
sudo apt update -y && sudo apt upgrade -y

# Cài đặt các gói phụ thuộc để thêm kho lưu trữ Docker
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Thêm khóa GPG chính thức của Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Thêm kho lưu trữ Docker vào APT sources
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Cài đặt Docker Engine và Docker Compose
sudo apt update -y
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Khởi động và kích hoạt Docker khởi động cùng hệ thống
sudo systemctl start docker
sudo systemctl enable docker

# Cho phép chạy docker mà không cần sudo (Tùy chọn - Cần relogin sau khi chạy)
sudo usermod -aG docker $USER
```

---

## 2. Triển Khai Ứng Dụng

### Bước 1: Tải mã nguồn lên máy ảo
Bạn có thể sử dụng `git clone` hoặc sử dụng các công cụ truyền file (SFTP/SCP như FileZilla) để tải toàn bộ thư mục dự án lên máy ảo (ví dụ đặt tại `/home/ubuntu/QuanLyThuPhi`).

### Bước 2: Khởi chạy Docker Compose
Truy cập vào thư mục chứa dự án trên máy ảo và chạy lệnh sau để build image và chạy ứng dụng dưới chế độ nền (daemon):

```bash
docker compose up -d --build
```

**Lưu ý:**
- Lần đầu chạy sẽ mất khoảng 1-3 phút để tải base image, cài đặt extension PHP và import cơ sở dữ liệu ban đầu từ file `code/database.sql`.
- Thiết lập `restart: always` trong `docker-compose.yml` đảm bảo hệ thống sẽ **tự động khởi động lại** ngay cả khi hệ điều hành máy ảo bị crash, khởi động lại (reboot), hoặc các container bị lỗi đột ngột.

---

## 3. Truy Cập Ứng Dụng

Sau khi các container khởi chạy thành công:
- **Trang chủ Frontend:** `http://<IP_MÁY_ẢO>:8080/fe/`
- **Backend API:** `http://<IP_MÁY_ẢO>:8080/be/`

*Nếu không truy cập được, hãy kiểm tra Firewall trên máy ảo hoặc Security Group của nhà cung cấp Cloud (AWS, Azure, Google Cloud, DigitalOcean...) để đảm bảo các cổng **8080** (Web) và **3306** (Database - nếu cần) đã được mở.*

---

## 4. Quản Lý Hệ Thống 24/7

### Kiểm tra trạng thái các container
```bash
docker compose ps
```

### Xem log hoạt động (Hữu ích khi debug lỗi)
```bash
# Xem toàn bộ logs
docker compose logs -f

# Xem logs riêng của dịch vụ Web
docker compose logs -f web

# Xem logs riêng của Cơ sở dữ liệu
docker compose logs -f db
```

### Dừng dịch vụ
```bash
docker compose down
```

### Khởi động lại dịch vụ
```bash
docker compose restart
```

---

## 5. Sao Lưu Và Phục Hồi Dữ Liệu (Backup & Restore)

Dữ liệu CSDL MySQL được lưu trữ bền vững tại Docker Volume có tên `db_data` (không bị mất khi restart hay xóa container). Để an toàn, bạn nên lập lịch backup định kỳ.

### Sao lưu Cơ sở dữ liệu (Export)
Chạy lệnh sau trên máy ảo để xuất file SQL backup ra thư mục hiện tại:
```bash
docker exec -t quanlythuphi-db mysqldump -u student_user -pSecurePassword2026! student_fee_management > backup_$(date +%F).sql
```

### Phục hồi Cơ sở dữ liệu (Import)
Chạy lệnh sau để import dữ liệu từ file SQL vào container:
```bash
docker exec -i quanlythuphi-db mysql -u student_user -pSecurePassword2026! student_fee_management < backup_file.sql
```
