#!/bin/bash
# Deployment script for fresh installation

echo "======================================"
echo "Binhan Ambulance Management System"
echo "Fresh Installation Script"
echo "======================================"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    echo "📝 Please copy .env.example to .env and configure database settings"
    exit 1
fi

echo "✓ .env file found"
echo ""

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
if [ $? -ne 0 ]; then
    echo "❌ Composer install failed!"
    exit 1
fi
echo "✓ Composer dependencies installed"
echo ""

echo "📦 Installing NPM dependencies..."
npm install
if [ $? -ne 0 ]; then
    echo "❌ NPM install failed!"
    exit 1
fi
echo "✓ NPM dependencies installed"
echo ""

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✓ Application key generated"
    echo ""
fi

# Run migrations and seeders
echo "🗄️  Running database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "❌ Migration failed!"
    exit 1
fi
echo "✓ Migrations completed"
echo ""

echo "🌱 Running database seeders..."
php artisan db:seed --force
if [ $? -ne 0 ]; then
    echo "❌ Seeding failed!"
    exit 1
fi
echo "✓ Seeders completed"
echo ""

# Build assets
echo "🎨 Building frontend assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "❌ Asset build failed!"
    exit 1
fi
echo "✓ Assets built successfully"
echo ""

# Clear caches
echo "🧹 Clearing caches..."
php artisan optimize:clear
php artisan permission:cache-reset
echo "✓ Caches cleared"
echo ""

# Verify setup
echo "🔍 Verifying installation..."
ROLES_COUNT=$(php artisan tinker --execute="echo \Spatie\Permission\Models\Role::count();")
ADMIN_PERMS=$(php artisan tinker --execute="echo \App\Models\User::find(1)?->getAllPermissions()->count() ?? 0;")

if [ "$ROLES_COUNT" -eq 8 ] && [ "$ADMIN_PERMS" -eq 28 ]; then
    echo "✅ Installation verified successfully!"
    echo ""
    echo "======================================"
    echo "✓ Installation Complete!"
    echo "======================================"
    echo ""
    echo "📝 Test Accounts:"
    echo "   Admin:      admin@binhan.com / password"
    echo "   Dispatcher: dispatcher@binhan.com / password"
    echo "   Accountant: accountant@binhan.com / password"
    echo "   Driver:     driver@binhan.com / password"
    echo ""
    echo "🚀 Start the server with:"
    echo "   php artisan serve"
    echo ""
else
    echo "⚠️  Warning: Some data may be missing"
    echo "   Roles: $ROLES_COUNT (expected: 8)"
    echo "   Admin permissions: $ADMIN_PERMS (expected: 28)"
    echo ""
    echo "💡 Try running: php artisan fix:all-roles"
fi
