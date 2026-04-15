# PHP 留言板

以 PHP + MySQL 實作的留言板系統，支援會員管理、角色權限控管、App 金鑰管理與 REST API。

## 功能

- 會員註冊 / 登入 / 登出
- 留言新增 / 編輯 / 刪除（依權限控制）
- 修改暱稱
- 分頁瀏覽留言
- 公告跑馬燈
- App 金鑰管理（申請 app_key / app_secret）
- 管理後台：角色管理、權限指派、會員管理
- REST API（JWT 驗證）

## 技術

- PHP（無框架）
- MySQL / MySQLi
- JWT（自行實作，HMAC-SHA256）
- Apache `.htaccess` URL Rewrite
- CSRF Token 防護

## 安裝與設定

### 1. 複製設定檔範本

```bash
cp conn.example.php conn.php
cp config.example.php config.php
```

### 2. 編輯 `conn.php`，填入資料庫連線資訊

```php
$servername = 'localhost';
$username   = 'your_db_username';
$password   = 'your_db_password';
$dbname     = 'your_db_name';
```

### 3. 編輯 `config.php`，設定 JWT 密鑰

```php
define('SECRET_KEY', 'your-strong-secret-key');
```

> 建議使用隨機產生的長字串，例如 `bin2hex(random_bytes(32))`

### 4. 匯入資料庫 Schema

將 SQL schema 匯入至你的 MySQL 資料庫（需包含 `users`、`comments`、`roles`、`permissions`、`role_permissions`、`app_keys`、`announcements` 資料表）。

### 5. 確認 Apache 已啟用 `mod_rewrite`

`api/` 目錄下的 `.htaccess` 使用 URL Rewrite，需確保 Apache 已啟用此模組。

## API 端點

所有 API 路徑皆位於 `api/` 目錄下。

### 取得 App Token

```
POST /api/auth/app_token
Content-Type: application/x-www-form-urlencoded

appKey=xxx&appSecret=xxx
```

回應：

```json
{ "ok": true, "token": "<JWT>" }
```

### 留言相關

| 方法 | 路徑 | 說明 | 驗證方式 |
|------|------|------|----------|
| GET | `/api/comments` | 取得留言列表 | App Token |
| POST | `/api/add_comment` | 新增留言 | App Token |
| PUT | `/api/edit_comment` | 編輯留言 | User Token |
| DELETE | `/api/delete_comment` | 刪除留言 | User Token |

請求時在 Header 帶入：

```
X-App-Token: Bearer <app_token>
X-User-Token: Bearer <user_token>
```

## 目錄結構

```
.
├── api/                  # REST API
│   ├── auth/             # Token 發放
│   ├── comments.php
│   ├── add_comment.php
│   ├── edit_comment.php
│   ├── delete_comment.php
│   └── .htaccess
├── plugin/               # 前端打包工具（node_modules 已 gitignore）
├── images/               # 靜態圖片
├── conn.php              # 資料庫連線（gitignore，請用 conn.example.php）
├── config.php            # 密鑰設定（gitignore，請用 config.example.php）
├── utils.php             # 共用函式
├── index.php             # 留言板首頁
├── login.php             # 登入頁
├── register.php          # 註冊頁
├── apps.php              # App 管理頁
├── admin_role.php        # 後台：角色管理
├── admin_user.php        # 後台：會員管理
└── ...
```

## 注意事項

- `conn.php` 與 `config.php` 已加入 `.gitignore`，請勿直接提交含有帳密的版本
- SECRET_KEY 請勿使用弱字串，建議上線前一定要更換
