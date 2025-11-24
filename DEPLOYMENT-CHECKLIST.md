# DEPLOYMENT CHECKLIST

## ✅ Pre-Deployment (Máy Dev)

- [ ] Kiểm tra tất cả migrations đã chạy: `php artisan migrate:status`
- [ ] Kiểm tra seeders đã đầy đủ trong `DatabaseSeeder.php`
- [ ] Test migrations mới: `php artisan migrate:fresh --seed` trên DB test
- [ ] Build assets: `npm run build`
- [ ] Commit tất cả changes
- [ ] Push lên Git: `git push origin main`

## 📋 Deployment (Máy Mới / VPS)

### 1. Clone Repository
```bash
git clone https://github.com/phalconsupply/binhan.git
cd binhan
```

### 2. Cấu hình Environment
```bash
# Copy .env.example
cp .env.example .env

# Sửa file .env với thông tin:
# - DB_DATABASE=binhan_db
# - DB_USERNAME=root
# - DB_PASSWORD=your_password
# - APP_URL=http://your-domain.com
```

### 3. Tạo Database
```sql
CREATE DATABASE binhan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Chạy Deployment Script

**Windows:**
```bash
deploy.bat
```

**Linux/Mac:**
```bash
chmod +x deploy.sh
./deploy.sh
```

**Hoặc thủ công:**
```bash
composer install
npm install
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm run build
php artisan optimize:clear
php artisan permission:cache-reset
```

### 5. Verify Installation
```bash
# Check roles (expected: 8)
php artisan tinker --execute="echo \Spatie\Permission\Models\Role::count();"

# Check admin permissions (expected: 28)
php artisan tinker --execute="echo \App\Models\User::find(1)->getAllPermissions()->count();"

# If wrong, run:
php artisan fix:all-roles
```

### 6. Set Permissions (Linux/VPS only)
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 7. Configure Web Server

**Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/binhan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache (.htaccess already configured):**
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/binhan/public

    <Directory /var/www/binhan/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 8. Start Application

**Development:**
```bash
php artisan serve
```

**Production:**
- Nginx/Apache should handle requests
- Use supervisor for queue workers if needed

## 🔐 Test Accounts

Login at: `http://your-domain.com/login`

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@binhan.com | password |
| Dispatcher | dispatcher@binhan.com | password |
| Accountant | accountant@binhan.com | password |
| Driver | driver@binhan.com | password |

**⚠️ CHANGE PASSWORDS IN PRODUCTION!**

## 🔧 Troubleshooting

### Menu không hiển thị:
```bash
php artisan permission:cache-reset
php artisan cache:clear
# Logout và login lại
```

### Roles rỗng:
```bash
php artisan fix:all-roles
```

### Assets không load:
```bash
npm run build
php artisan view:clear
```

### Database seeding lỗi:
```bash
# Chạy lại từ đầu
php artisan migrate:fresh --seed
```

## 📊 Verification Checklist

- [ ] Website accessible at configured URL
- [ ] Login page loads correctly
- [ ] Can login as admin@binhan.com
- [ ] Admin sees all menu items (8 items)
- [ ] Dashboard displays correctly
- [ ] Can create new vehicle
- [ ] Can create new incident
- [ ] Can create new transaction
- [ ] Can view reports
- [ ] Can export Excel/PDF

## 🔒 Production Security

- [ ] Change all default passwords
- [ ] Set `APP_ENV=production` in .env
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Configure HTTPS/SSL
- [ ] Set up firewall rules
- [ ] Configure backup schedule
- [ ] Set up monitoring/logging
- [ ] Review file permissions
- [ ] Enable CSRF protection (already enabled)
- [ ] Configure rate limiting

## 📝 Post-Deployment

- [ ] Create real admin user
- [ ] Delete test accounts (optional)
- [ ] Import initial data (locations, partners, vehicles)
- [ ] Test all major features
- [ ] Train users
- [ ] Document any custom configurations
