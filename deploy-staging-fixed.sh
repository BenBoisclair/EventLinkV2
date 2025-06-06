#!/bin/bash

# STAGING DEPLOYMENT SCRIPT
# =========================
# For development/testing environment

echo "🚧 Starting STAGING deployment..."

# Step 1: Optional maintenance mode (less critical for staging)
echo "📝 Enabling maintenance mode..."
php artisan down --render="errors::503" --secret="staging-secret-123"

# Step 2: Pull from DEV branch
echo "📦 Pulling latest code from DEV branch..."
git pull origin dev

# Step 3: Install PHP dependencies (including dev dependencies for debugging)
echo "🔧 Installing Composer dependencies (with dev packages)..."
composer install --no-dev --optimize-autoloader --no-interaction

# Step 4: Run database migrations FIRST (before any cache operations)
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Step 5: Install Node.js dependencies
echo "📦 Installing PNPM dependencies..."
pnpm install --frozen-lockfile

# Step 6: Build assets for staging (might include source maps for debugging)
echo "🏗️  Building frontend assets for staging..."
pnpm run build

# Step 7: Clear and rebuild caches (AFTER migrations)
echo "🧹 Clearing and rebuilding caches..."
php artisan cache:clear
php artisan config:cache  # Use cache instead of clear for better performance
php artisan route:cache   # Use cache instead of clear for better performance
php artisan view:cache    # Use cache instead of clear for better performance
php artisan event:cache   # Cache events for better performance

# Step 8: Optimize application
echo "⚡ Optimizing application..."
php artisan optimize

# Step 9: Restart queue workers if you have any
echo "🔄 Restarting queue workers..."
php artisan queue:restart || true

# Step 10: Disable maintenance mode
echo "✅ Disabling maintenance mode..."
php artisan up

echo "🎉 STAGING deployment completed!"

# Health check for staging
# echo "🔍 Running staging health check..."
# curl -f http://staging.yourdomain.com/health || echo "⚠️  Staging health check failed - please verify manually"

echo "✨ Staging environment is ready for testing!"