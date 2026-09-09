#!/bin/bash
# ============================================
# Swaratani Deploy Script
# Jalankan di server: bash deploy.sh
# ============================================

set -e

PROJECT_DIR="/opt/docker-apps/swaratani"
CONTAINER="swaratani_app"

echo "🔄 Pulling latest code from GitHub..."
cd "$PROJECT_DIR/src"
git pull origin main

echo "🔨 Building & restarting Docker..."
cd "$PROJECT_DIR"
docker compose up -d --build

echo "⏳ Waiting for container to start..."
sleep 5

echo "🗄️ Running migrations..."
docker exec $CONTAINER php artisan migrate --force

echo "⚡ Caching config..."
docker exec $CONTAINER php artisan config:cache
docker exec $CONTAINER php artisan route:cache
docker exec $CONTAINER php artisan view:cache

echo "🔄 Restarting background workers via supervisor..."
docker exec $CONTAINER supervisorctl restart mqtt-listener
docker exec $CONTAINER supervisorctl restart queue-worker || true

echo ""
echo "📡 Checking Supervisor status:"
docker exec $CONTAINER supervisorctl status

echo ""
echo "✅ Deploy selesai! Cek: https://swaratani.id"
echo ""
docker ps | grep $CONTAINER
