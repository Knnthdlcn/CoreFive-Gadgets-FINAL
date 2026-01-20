# Local Admin Host Testing (Option 1)

This project supports Option 1: **one Laravel app** but admin pages can be restricted to a **separate host** (example: `admin.corefive.test`).

This works because admin routes are protected by middleware that checks `ADMIN_HOST`.

## 1) One-time Apache setup (XAMPP)

### A. Add local domains to Windows hosts file

1. Open Notepad **as Administrator**
2. Open: `C:\Windows\System32\drivers\etc\hosts`
3. Add these lines:

```
127.0.0.1 corefive.test
127.0.0.1 admin.corefive.test
```

4. Save the file

### B. Enable VirtualHosts in Apache

1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Ensure this line is enabled (remove `#` if present):

```
Include conf/extra/httpd-vhosts.conf
```

### C. Create two VirtualHosts pointing to the SAME Laravel /public

1. Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add:

```apache
<VirtualHost *:80>
    ServerName corefive.test
    DocumentRoot "C:/xampp/htdocs/E-Commerce-Laravel-main/public"
    <Directory "C:/xampp/htdocs/E-Commerce-Laravel-main/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName admin.corefive.test
    DocumentRoot "C:/xampp/htdocs/E-Commerce-Laravel-main/public"
    <Directory "C:/xampp/htdocs/E-Commerce-Laravel-main/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Restart Apache in XAMPP

## 2) Project .env settings

In your `.env`:

```
APP_URL=http://corefive.test
ADMIN_HOST=admin.corefive.test
```

Then run:

```
php artisan optimize:clear
```

## 3) Verify

- Storefront should work:
  - `http://corefive.test/`

- Admin should work ONLY on admin host:
  - `http://admin.corefive.test/admin/login`

- This should be blocked (404):
  - `http://corefive.test/admin/login`

## Notes

- This local URL testing requires Apache VirtualHosts (it will not work reliably with `php artisan serve`).
- For production, use real DNS + HTTPS and keep `ADMIN_HOST` set to your admin domain.
