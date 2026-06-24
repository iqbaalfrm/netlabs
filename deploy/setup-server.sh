#!/bin/bash
# ============================================================
# NETLABS VPS Setup Script
# Untuk Ubuntu 22.04 / 24.04 — 1 vCPU, 2 GB RAM
# IP: 161.35.55.122
#
# CATATAN: Spek 1 core / 2GB cukup untuk FastAPI + Laravel.
# ChromaDB + sentence-transformers TIDAK diinstall di server
# karena terlalu berat (~1GB RAM). AI Tutor tetap jalan
# via Anthropic API saja (tanpa RAG lokal).
# ============================================================
set -e

IP_VPS="161.35.55.122"
PROJECT_DIR="/home/deploy/netlabs"

echo "============================================"
echo "  NETLABS VPS Setup"
echo "  IP: $IP_VPS"
echo "  Spek: 1 vCPU, 2 GB RAM"
echo "============================================"
echo ""

# ============================================================
# STEP 1: Swap file (penting untuk 2GB RAM!)
# ============================================================
echo "[1/8] Setting up 2GB swap file..."
if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' | tee -a /etc/fstab
    # Optimize swap usage untuk low-RAM server
    sysctl vm.swappiness=10
    echo 'vm.swappiness=10' | tee -a /etc/sysctl.conf
    echo "✓ 2GB swap created"
else
    echo "✓ Swap already exists"
fi

# ============================================================
# STEP 2: System Update & Install Dependencies
# ============================================================
echo "[2/8] Installing system dependencies..."
apt update && apt upgrade -y

# Python 3.11
apt install -y python3.11 python3.11-venv python3-pip

# PHP 8.2
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2-fpm php8.2-cli php8.2-mbstring php8.2-xml \
    php8.2-curl php8.2-zip php8.2-sqlite3 php8.2-bcmath php8.2-gd

# Nginx
apt install -y nginx

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Git & utilities
apt install -y git unzip

echo "✓ System dependencies installed"

# ============================================================
# STEP 3: Optimize PHP-FPM for 2GB RAM
# ============================================================
echo "[3/8] Optimizing PHP-FPM for low RAM..."
PHP_FPM_CONF="/etc/php/8.2/fpm/pool.d/www.conf"

# Backup original
cp "$PHP_FPM_CONF" "${PHP_FPM_CONF}.bak"

# Tune for 2GB: max 5 children, each ~50MB
sed -i 's/pm = dynamic/pm = ondemand/' "$PHP_FPM_CONF"
sed -i 's/pm.max_children = .*/pm.max_children = 5/' "$PHP_FPM_CONF"
sed -i 's/;pm.max_requests = .*/pm.max_requests = 200/' "$PHP_FPM_CONF"
sed -i 's/pm.process_idle_timeout = .*/pm.process_idle_timeout = 10s/' "$PHP_FPM_CONF"

systemctl restart php8.2-fpm
echo "✓ PHP-FPM optimized (max 5 children, ondemand)"

# ============================================================
# STEP 4: Create deploy user
# ============================================================
echo "[4/8] Creating deploy user..."
if ! id "deploy" &>/dev/null; then
    adduser --disabled-password --gecos "" deploy
    usermod -aG sudo deploy
    echo "deploy ALL=(ALL) NOPASSWD:ALL" | tee /etc/sudoers.d/deploy
    # Copy SSH keys from root
    mkdir -p /home/deploy/.ssh
    cp /root/.ssh/authorized_keys /home/deploy/.ssh/ 2>/dev/null || true
    chown -R deploy:deploy /home/deploy/.ssh
fi
echo "✓ Deploy user ready"

# ============================================================
# STEP 5: Setup project directory
# ============================================================
echo "[5/8] Setting up project directory..."
mkdir -p $PROJECT_DIR
chown -R deploy:deploy $PROJECT_DIR
echo "✓ Project directory ready at $PROJECT_DIR"

# ============================================================
# STEP 6: Firewall
# ============================================================
echo "[6/8] Configuring firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
echo "y" | ufw enable 2>/dev/null || true
echo "✓ Firewall configured"

# ============================================================
# STEP 7: Create systemd services (optimized for 1 core)
# ============================================================
echo "[7/8] Creating services..."

# Backend — 1 worker saja (1 core)
cat > /etc/systemd/system/netlabs-backend.service << 'EOF'
[Unit]
Description=Netlabs FastAPI Backend
After=network.target

[Service]
User=deploy
WorkingDirectory=/home/deploy/netlabs/backend
Environment=PATH=/home/deploy/netlabs/backend/venv/bin:/usr/bin
ExecStart=/home/deploy/netlabs/backend/venv/bin/gunicorn main:app \
    --workers 1 \
    --worker-class uvicorn.workers.UvicornWorker \
    --bind 127.0.0.1:8000 \
    --timeout 120 \
    --access-logfile /var/log/netlabs-backend-access.log \
    --error-logfile /var/log/netlabs-backend-error.log
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Queue worker
cat > /etc/systemd/system/netlabs-queue.service << 'EOF'
[Unit]
Description=Netlabs Laravel Queue Worker
After=network.target

[Service]
User=deploy
WorkingDirectory=/home/deploy/netlabs/web-laravel
ExecStart=/usr/bin/php8.2 artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
echo "✓ Services created (1 Gunicorn worker)"

# ============================================================
# STEP 8: Configure Nginx
# ============================================================
echo "[8/8] Configuring Nginx..."
cat > /etc/nginx/sites-available/netlabs << EOF
server {
    listen 80;
    server_name $IP_VPS;

    root /home/deploy/netlabs/web-laravel/public;
    index index.php index.html;

    # Max upload size (untuk PDF modul)
    client_max_body_size 15M;

    # FastAPI backend — reverse proxy
    location /api/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_read_timeout 120s;
    }

    # FastAPI docs
    location /docs {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host \$host;
    }
    location /openapi.json {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host \$host;
    }

    # Laravel — PHP-FPM
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block sensitive files
    location ~ /\.(ht|git|env) {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Gzip compression (hemat bandwidth)
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;
    gzip_min_length 256;
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/netlabs /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
echo "✓ Nginx configured"

# ============================================================
# DONE
# ============================================================
echo ""
echo "============================================"
echo "  SERVER SETUP COMPLETE!"
echo "  (1 core / 2GB + 2GB swap)"
echo "============================================"
echo ""
echo "  Upload project ke server:"
echo "    scp -r netlabs deploy@$IP_VPS:/home/deploy/"
echo ""
echo "  Lalu SSH ke server:"
echo "    ssh deploy@$IP_VPS"
echo ""
echo "  Setup Backend:"
echo "    cd ~/netlabs/backend"
echo "    python3.11 -m venv venv"
echo "    source venv/bin/activate"
echo "    pip install -r requirements.txt gunicorn"
echo "    cp .env.example .env && nano .env"
echo "    sudo systemctl enable --now netlabs-backend"
echo ""
echo "  Setup Laravel:"
echo "    cd ~/netlabs/web-laravel"
echo "    composer install --no-dev --optimize-autoloader"
echo "    npm install && npm run build"
echo "    cp .env.example .env && nano .env"
echo "    php artisan key:generate"
echo "    chmod -R 775 storage bootstrap/cache"
echo "    sudo chown -R deploy:www-data storage bootstrap/cache"
echo "    sudo systemctl enable --now netlabs-queue"
echo ""
echo "============================================"
