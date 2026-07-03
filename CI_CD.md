# CI/CD cho QuanLyThuPhi

File workflow: `.github/workflows/ci-cd.yml`

## CI lam gi?

CI chay tu dong khi push len bat ky branch nao va khi tao pull request vao `main` hoac `master`.

Pipeline gom cac buoc:

1. Lay source code tu repository.
2. Cai PHP 8.2.
3. Kiem tra cu phap tat ca file `.php` trong thu muc `code`.
4. Kiem tra file `docker-compose.yml` va `docker-compose.ci.yml` co hop le khong.
5. Build Docker image cua ung dung.
6. Chay stack bang Docker Compose.
7. Doi MySQL san sang.
8. Goi thu trang login tai `http://localhost:18080/fe/index.php?action=login`.
9. Tat stack va xoa volume tam cua CI.

CI dung file `docker-compose.ci.yml` rieng de doi ten container va port test. Viec nay giup pipeline khong bi dung voi container production hoac container dang chay tren may dev.

## CD lam gi?

CD chi chay sau khi CI thanh cong va chi tren branch `main` hoac `master`.

Neu ban cau hinh du GitHub Secrets, GitHub Actions se SSH vao VM, vao thu muc du an, pull code moi nhat, sau do chay:

```bash
docker compose up -d --build --remove-orphans
```

Lenh nay build lai image, khoi dong container moi va giu ung dung chay nen theo cau hinh Docker Compose hien tai.

## Can cau hinh GitHub Secrets nao?

Vao repository tren GitHub:

`Settings -> Secrets and variables -> Actions -> New repository secret`

Them cac secret sau:

| Secret | Y nghia | Vi du |
| --- | --- | --- |
| `DEPLOY_HOST` | IP hoac domain cua VM | `203.0.113.10` |
| `DEPLOY_USER` | User SSH tren VM | `ubuntu` |
| `DEPLOY_PORT` | Cong SSH, co the bo trong neu dung 22 | `22` |
| `DEPLOY_SSH_KEY` | Private key dung de SSH vao VM | Noi dung file private key |
| `DEPLOY_PATH` | Thu muc du an tren VM | `/home/ubuntu/QuanLyThuPhi` |

## Chuan bi VM

Tren VM can co san:

1. Docker va Docker Compose plugin.
2. Repository da duoc clone vao `DEPLOY_PATH`.
3. Public key tuong ung voi `DEPLOY_SSH_KEY` da nam trong `~/.ssh/authorized_keys` cua `DEPLOY_USER`.
4. User deploy co quyen chay Docker.

Vi du:

```bash
sudo usermod -aG docker ubuntu
```

Sau lenh tren, dang xuat va dang nhap lai SSH de nhom `docker` co hieu luc.

## Luu y bao mat

Mat khau database hien dang nam truc tiep trong `docker-compose.yml`. Cach nay chay duoc cho bai tap hoac demo, nhung khi len production nen chuyen sang `.env` hoac GitHub/VM secrets de tranh lo thong tin nhay cam.
