#!/bin/bash
# YUNARI-RESERVE デプロイスクリプト
# シン・レンタルサーバー（SSH）で実行する
# 使い方: bash deploy.sh

set -e

DEPLOY_DIR="$(cd "$(dirname "$0")" && pwd)"
PHP="/usr/bin/php8.3"  # シン・レンタルサーバーのPHPパス（バージョンに応じて変更）

echo "=== YUNARI-RESERVE deploy start ==="

# 1. 最新コードを取得
git pull origin master

# 2. Composer パッケージインストール（本番用）
$PHP $(which composer) install --no-dev --optimize-autoloader

# 3. キャッシュクリア
$PHP artisan cache:clear
$PHP artisan config:clear
$PHP artisan route:clear
$PHP artisan view:clear

# 4. マイグレーション実行
$PHP artisan migrate --force

# 5. 本番キャッシュ生成
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

# 6. ストレージリンク（初回のみ実質有効）
$PHP artisan storage:link 2>/dev/null || true

echo "=== deploy complete ==="
