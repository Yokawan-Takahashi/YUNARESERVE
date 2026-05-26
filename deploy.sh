#!/bin/bash
# YUNARI-RESERVE デプロイスクリプト
# シン・レンタルサーバー（SSH）で実行する
# 使い方: bash deploy.sh

set -e

DEPLOY_DIR="$(cd "$(dirname "$0")" && pwd)"
PHP="/usr/bin/php8.3"
NODE_BIN="$HOME/node-v22.14.0-linux-x64/bin"

echo "=== YUNARI-RESERVE deploy start ==="

# 1. 最新コードを取得
git pull origin master

# 2. Composer パッケージインストール（本番用）
$PHP $(which composer) install --no-dev --optimize-autoloader

# 3. フロントエンドビルド
export PATH="$NODE_BIN:$PATH"
npm ci --production=false
npm run build

# 4. キャッシュクリア
$PHP artisan cache:clear
$PHP artisan config:clear
$PHP artisan route:clear
$PHP artisan view:clear

# 5. マイグレーション実行
$PHP artisan migrate --force

# 6. 本番キャッシュ生成
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

# 7. ストレージリンク（初回のみ実質有効）
$PHP artisan storage:link 2>/dev/null || true

echo "=== deploy complete ==="
