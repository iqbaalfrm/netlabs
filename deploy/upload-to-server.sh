#!/bin/bash
# ============================================================
# Upload Project ke VPS
# Jalankan dari Windows PowerShell/Git Bash di folder parent netlabs
# ============================================================

IP_VPS="161.35.55.122"
PROJECT_NAME="netlabs"

echo "============================================"
echo "  Upload Netlabs ke VPS"
echo "  Target: deploy@$IP_VPS:/home/deploy/"
echo "============================================"
echo ""

# Check if rsync is available
if command -v rsync &> /dev/null; then
    echo "Using rsync (faster for updates)..."

    rsync -avz --progress \
        --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='.venv' \
        --exclude='venv' \
        --exclude='.git' \
        --exclude='*.log' \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        --exclude='chroma_db' \
        --exclude='.env' \
        --exclude='.DS_Store' \
        --exclude='Thumbs.db' \
        "$PROJECT_NAME/" "deploy@$IP_VPS:/home/deploy/$PROJECT_NAME/"

elif command -v scp &> /dev/null; then
    echo "Using scp (slower but works)..."

    # Create tar archive excluding unnecessary files
    tar --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='.venv' \
        --exclude='venv' \
        --exclude='.git' \
        --exclude='*.log' \
        --exclude='chroma_db' \
        --exclude='.env' \
        -czf /tmp/netlabs-upload.tar.gz "$PROJECT_NAME/"

    echo "Uploading..."
    scp /tmp/netlabs-upload.tar.gz "deploy@$IP_VPS:/tmp/"

    echo "Extracting on server..."
    ssh deploy@$IP_VPS "cd /home/deploy && tar -xzf /tmp/netlabs-upload.tar.gz && rm /tmp/netlabs-upload.tar.gz"
    rm /tmp/netlabs-upload.tar.gz

else
    echo "ERROR: Neither rsync nor scp found. Please install one of them."
    exit 1
fi

echo ""
echo "✓ Upload complete!"
echo ""
echo "Next steps:"
echo "  1. SSH to server: ssh deploy@$IP_VPS"
echo "  2. Setup backend:"
echo "     cd ~/netlabs/backend"
echo "     python3.11 -m venv venv"
echo "     source venv/bin/activate"
echo "     pip install -r requirements.txt gunicorn"
echo "     cp .env.example .env && nano .env"
echo "     sudo systemctl restart netlabs-backend"
echo ""
echo "  3. Setup Laravel:"
echo "     cd ~/netlabs/web-laravel"
echo "     composer install --no-dev --optimize-autoloader"
echo "     npm install && npm run build"
echo "     cp .env.example .env && nano .env"
echo "     php artisan key:generate"
echo "     sudo systemctl restart netlabs-queue"
echo ""
