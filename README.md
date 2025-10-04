# cP## 🔐 Security & Password Protection

The tool includes optional password protection to prevent unauthorized access to your directory structure.

### Setting Up Password Protection
1. Open the `du.php` file in a text editor
2. Find the line: `define('APP_PASSWORD', '');`
3. Change the empty string to your desired password: `define('APP_PASSWORD', 'your_secure_password');`
4. Save the file and upload to your server

### Setting Up Email Reports (Optional - Command Line Only)
For automated email reports when using command line mode:
1. Find the line: `define('APP_EMAIL', '');`
2. Change to your email address: `define('APP_EMAIL', 'your@email.com');`
3. This is only used when running from command line
4. Web interface does not require email configuration

### Security Enforcement
- **⚠️ Empty Password Protection:** If `APP_PASSWORD` is left empty (`''`), the tool will show a security warning and refuse to run
- **Mandatory Configuration:** You must set a password before the tool can be used
- **No Bypass:** The security check cannot be bypassed - a password must be configured

### Default Password
- **Default password:** Empty (`''`) - **must be changed before use**
- **Security Warning:** Tool will not function until password is set

## 💻 Command Line Usage

The tool can also be run from the command line for automated reporting and cron jobs.

### Basic Command Line Usage
```bash
php -f cdua.php
```

### Setting Up Cron Jobs
Add to your crontab for automated reports:

```bash
# Daily report at 6 AM
0 6 * * * /usr/bin/php -f /path/to/cdua.php

# Weekly report every Sunday at midnight
0 0 * * 0 /usr/bin/php -f /path/to/cdua.php

# Monthly report on the 1st at 3 AM
0 3 1 * * /usr/bin/php -f /path/to/cdua.php
```

### Command Line Features
- **Console Output:** Displays top 20 largest directories/files
- **Summary Statistics:** Shows breakdown by file type
- **Email Reports:** Automatically emails results if `APP_EMAIL` is configured
- **No Password Required:** Command line mode bypasses web authentication
- **Cron Friendly:** Perfect for automated monitoring

### Email Configuration
- **Only needed for command line usage** - web interface doesn't use email
- Set `APP_EMAIL` to receive automated email reports from command line
- Leave empty if you only use the web interface
- Reports include detailed directory analysis
- Email sent automatically when running from command line
- **Uses PHP's built-in `mail()` function** - requires proper PHP mail configuration on server
- **Note:** Many shared hosting providers have mail() disabled or require additional SMTP setupge Analyzer

**A single-file PHP disk usage analyzer for cPanel hosting accounts.**

## � Security & Password Protection

The tool includes optional password protection to prevent unauthorized access to your directory structure.

### Setting Up Password Protection
1. Open the `du.php` file in a text editor
2. Find the line: `define('APP_PASSWORD', '');`
3. Change the empty string to your desired password: `define('APP_PASSWORD', 'your_secure_password');`
4. Save the file and upload to your server

### Security Enforcement
- **⚠️ Empty Password Protection:** If `APP_PASSWORD` is left empty (`''`), the tool will show a security warning and refuse to run
- **Mandatory Configuration:** You must set a password before the tool can be used
- **No Bypass:** The security check cannot be bypassed - a password must be configured

### Default Password
- **Default password:** Empty (`''`) - **must be changed before use**
- **Security Warning:** Tool will not function until password is set

### How It Works
- First visit requires password entry
- Once authenticated, you can use the tool freely
- Session persists until browser is closed or you logout
- Logout link appears in the top-right corner
- Password protects against unauthorized directory browsing

### Disabling Password Protection
To disable password protection completely:
1. Find the line: `define('APP_PASSWORD', 'cpanel123');`
2. Change it to: `define('APP_PASSWORD', '');` (empty string)
3. Save and upload - no password will be required

## �🚀 Quick Start

1. **Download** the `du.php` file
2. **Upload** it to any directory in your cPanel account
3. **Access** it via your browser: `https://yourdomain.com/du.php`
4. **Done!** Start exploring your disk usage

## 📦 Deployment Instructions

### Method 1: cPanel File Manager
1. Log into your cPanel account
2. Open "File Manager"
3. Navigate to where you want to place the tool (e.g., `public_html`)
4. Click "Upload" and select the `du.php` file
5. Access via `https://yourdomain.com/du.php`

### Method 2: FTP/SFTP
1. Connect to your hosting account via FTP
2. Upload `du.php` to your desired directory
3. Set file permissions to 644 (if needed)
4. Access via your browser

### Method 3: Any Subdirectory
- Upload to `public_html/tools/du.php` → Access: `yourdomain.com/tools/du.php`
- Upload to `public_html/admin/du.php` → Access: `yourdomain.com/admin/du.php`
- Works in any web-accessible directory!

## ✨ Features

- 📁 **Complete cPanel scanning** - Analyzes entire hosting account structure, not just public_html
- 🔗 **Smart symlink handling** - Detects and excludes symbolic links to prevent double-counting
- 📱 **Mobile responsive** - Works perfectly on phones, tablets, and desktops
- ⚡ **Performance optimized** - Memory management and execution time limits for large directories
- 🔒 **Secure** - Path validation prevents directory traversal attacks
- 🎯 **Zero dependencies** - Pure PHP, no installation or configuration required
- 🗂️ **Intuitive navigation** - Breadcrumb navigation and drill-down capabilities
- 📊 **Size sorting** - Automatically sorts by size (largest first) for quick identification

## 💡 Usage & Pro Tips

### Basic Navigation
- **Click folder names** to drill down into subdirectories
- **Use breadcrumb navigation** at the top to jump back to any parent directory
- **Click ".. (Parent Directory)"** to go up one level
- **Folders are sorted by size** (largest first) to quickly identify space usage

### Understanding the Display
- 📁 **Blue folders** - Directories you can click to explore
- 📄 **Grey files** - Individual files with their sizes
- 🔗 **Greyed out symlinks** - Symbolic links with target paths shown, excluded from totals
- **Size column** - Human-readable format (B, KB, MB, GB, TB)
- **Total Size** - Shows actual disk usage (excluding symlinks)

### Performance Notes
- The tool may take time to load for directories with many files - this is normal
- Large directories are processed efficiently with memory management
- 5-minute execution time limit prevents timeouts
- 256MB memory limit handles most directory structures

### Best Practices
- Upload to `public_html` for easy access to your entire cPanel account
- Use in subdirectories for focused analysis of specific areas
- Bookmark the URL for quick access to disk usage analysis
- Run periodically to monitor disk usage trends

## 🏗️ What Gets Analyzed

When placed in `public_html`, the tool scans your entire cPanel account including:
- `public_html/` - Your website files
- `mail/` - Email data and attachments
- `logs/` - Access logs, error logs
- `tmp/` - Temporary files
- `etc/` - Configuration files
- `ssl/` - SSL certificates
- `subdomains/` - Subdomain files
- And any other directories in your cPanel account

## 📋 Requirements

- **PHP 7.4+** (most cPanel accounts have this)
- **cPanel hosting account** (shared, VPS, or dedicated)
- **Web browser** (any modern browser)
- **File upload access** (cPanel File Manager or FTP)

## 🔧 Troubleshooting

### Common Issues
- **"Directory not accessible"** - Check file permissions (should be 644)
- **Memory errors** - Tool automatically manages memory for large directories
- **Timeout errors** - Large directories may take time; this is normal
- **Permission denied** - Some system directories may not be readable (expected)

### Security Notes
- Tool is restricted to your cPanel account structure only
- Cannot access files outside your hosting account
- Path traversal attacks are prevented
- No sensitive data is logged or stored

---

**That's it!** One file, unlimited possibilities. Upload anywhere and start analyzing your disk usage immediately.