# Panduan Deployment Netlabs ke VPS

## Spesifikasi Server
- **IP**: 161.35.55.122
- **OS**: Ubuntu
- **CPU**: 4 cores
- **RAM**: 4 GB

## Arsitektur
```
Internet → Nginx (port 80)
              ↓
    ┌─────────────────────────┐
    │                         │
    ↓                         ↓
FastAPI Backend          Laravel Web Admin
(port 8000)              (PHP-FPM)
    ↓                         ↓
    └─────────────────────────┘
              ↓
         Supabase DB
```

## Langkah 1: Upload Script Setup ke Server

Dari **PowerShell/CMD di Windows** (di folder project netlabs):

```bash
scp deploy/setup-server.sh root@161.35.55.122:/root/
```

Atau kalau pakai SSH client lain, upload file `deploy/setup-server.sh` ke `/root/` di server.

## Langkah 2: Jalankan Setup Server

SSH ke server sebagai root:
```bash
ssh root@161.35.55.122
```

Jalankan script:
```bash
chmod +x /root/setup-server.sh
bash /root/setup-server.sh
```

Script ini akan:
- Install Python 3.11, PHP 8.2, Node.js 20, Nginx, Composer
- Buat user `deploy` dengan sudo privileges
- Configure firewall (UFW)
- Setup systemd services untuk backend dan queue worker
- Configure Nginx reverse proxy

## Langkah 3: Upload Project ke Server

Dari **Windows PowerShell** (di folder parent netlabs):

```bash
scp -r netlabs deploy@161.35.55.122:/home/deploy/
```

Atau pakai rsync (lebih cepat untuk update):
```bash
rsync -avz --exclude='node_modules' --exclude='vendor' --exclude='.venv' --exclude='venv' \
  netlabs/ deploy@161.35.55.122:/home/deploy/netlabs/
```

**Note**: Exclude folder `node_modules`, `vendor`, `venv`, `.venv` karena akan diinstall di server.

## Langkah 4: Setup Backend (FastAPI)

SSH sebagai user deploy:
```bash
ssh deploy@161.35.55.122
```

### 4a. Buat virtual environment dan install dependencies
```bash
cd ~/netlabs/backend
python3.11 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
pip install gunicorn
```

### 4b. Buat file .env
```bash
cp .env.example .env
nano .env
```

Isi dengan:
```env
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your-anon-key
SUPABASE_SERVICE_KEY=your-service-role-key
ANTHROPIC_API_KEY=your-anthropic-api-key
JWT_SECRET=random-string-minimal-32-karakter
CHROMA_PATH=/home/deploy/netlabs/backend/chroma_db
APP_ENV=production
CORS_ORIGINS=http://161.35.55.122
```

**Penting**: 
- Ganti semua value dengan credentials asli dari Supabase dan Anthropic
- Generate JWT_SECRET random: `openssl rand -base64 48`

### 4c. Start backend service
```bash
sudo systemctl enable netlabs-backend
sudo systemctl start netlabs-backend
```

### 4d. Test backend
```bash
curl http://127.0.0.1:8000/
```

Harusnya muncul: `{"message":"Netlabs API berjalan!","version":"1.0.0"}`

## Langkah 5: Setup Web Admin (Laravel)

### 5a. Install PHP dependencies
```bash
cd ~/netlabs/web-laravel
composer install --no-dev --optimize-autoloader
```

### 5b. Install Node dependencies dan build
```bash
npm install
npm run build
```

### 5c. Setup .env
```bash
cp .env.example .env
nano .env
```

Ubah yang penting:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://161.35.55.122

DB_CONNECTION=sqlite

SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_KEY=your-service-role-key
BACKEND_URL=http://127.0.0.1:8000
```

### 5d. Generate app key dan set permissions
```bash
php artisan key:generate
chmod -R 775 storage bootstrap/cache
sudo chown -R deploy:www-data storage bootstrap/cache
```

### 5e. Start queue worker
```bash
sudo systemctl enable netlabs-queue
sudo systemctl start netlabs-queue
```

## Langkah 6: Test Deployment

### Test Backend via Nginx
```bash
curl http://161.35.55.122/api/
```

### Test Web Admin
Buka browser: `http://161.35.55.122`

Login dengan:
- Username: `GURU001`
- Password: `guru123`

### Test API Docs
Buka: `http://161.35.55.122/docs`

## Langkah 7: Update Mobile App

Edit `mobile/lib/app/services/api_service.dart`:

```dart
static const String _baseUrlDefault = 'http://161.35.55.122';
```

Rebuild app:
```bash
cd ~/netlabs/mobile
flutter build apk --release
```

## Monitoring & Troubleshooting

### Cek status services
```bash
sudo systemctl status netlabs-backend
sudo systemctl status netlabs-queue
sudo systemctl status nginx
```

### Lihat logs
```bash
# Backend logs
sudo journalctl -u netlabs-backend -f

# Queue worker logs
sudo journalctl -u netlabs-queue -f

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Backend access/error logs
sudo tail -f /var/log/netlabs-backend-access.log
sudo tail -f /var/log/netlabs-backend-error.log
```

### Restart services
```bash
sudo systemctl restart netlabs-backend
sudo systemctl restart netlabs-queue
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## Update Code

Untuk update code setelah perubahan:

```bash
# Upload perubahan
rsync -avz --exclude='node_modules' --exclude='vendor' --exclude='.venv' \
  netlabs/ deploy@161.35.55.122:/home/deploy/netlabs/

# Restart services
ssh deploy@161.35.55.122
sudo systemctl restart netlabs-backend
sudo systemctl restart netlabs-queue
```

## Security Notes

1. **Firewall sudah aktif** - hanya port 22 (SSH) dan 80 (HTTP) yang terbuka
2. **Jangan expose port 8000** - backend hanya bisa diakses via Nginx reverse proxy
3. **Ganti password default** setelah login pertama
4. **Pertimbangkan SSL/TLS** - install Let's Encrypt setelah punya domain
5. **Backup database** - setup cron job untuk backup Supabase secara berkala

## Next Steps (Optional)

### Setup Domain & SSL
Setelah punya domain:
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### Setup Auto-deploy dengan Git
```bash
cd ~/netlabs
git init
git remote add origin your-repo-url
```

### Setup Monitoring
Install tools monitoring seperti:
- htop (resource monitoring)
- fail2ban (brute force protection)
- logwatch (log analysis)
