# Kobopoint - Nigerian Fintech Platform

Kobopoint is a Nigerian-focused fintech platform designed to provide simple, fast, and accessible digital financial services for both rural and urban users. The system consists of a Laravel backend, ReactJS frontend, and a mobile app, with a strong focus on offline availability, local transactions, and micro-finance tools.

## 🎯 Project Goal

To build an inclusive financial ecosystem that empowers individuals, small businesses, rural merchants, and transport operators with tools that help them save, pay, borrow, and transact easily — even with limited internet access.

## ⚙️ Core Features

1. **Digital Payments** - Send & receive money instantly, multi-bank transfer support, wallet-to-wallet payments, offline transaction queue
2. **Merchant Tools** - POS-like wallet system, offline payment codes, QR payment, invoice/payment links
3. **Thrift & Savings (Ajo / Esusu)** - Rotational contributions, auto-debit & auto-credit, group savings, daily/weekly cash collections, micro-savings goals
4. **Bills & Utilities** - Airtime & data, electricity, TV subscriptions, water & transport tokens
5. **Micro-Credit** (Optional Future Feature) - Wallet-based micro loans, transaction scoring, repayment reminders

## 🧩 Technology Stack

- **Backend**: Laravel 12, REST API, MySQL, Laravel Sanctum authentication
- **Frontend**: ReactJS (admin dashboard)
- **Mobile**: React Native or Flutter (Android first)
- **Queue**: Database/Redis
- **Cache**: Redis/Database

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM (for frontend)
- Redis (optional, for production)

## 🚀 Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd kobo
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure environment variables

Edit `.env` file and set the following required variables:

#### Database Configuration
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kobopoint
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### BellBank API Configuration
```
BELLBANK_API_URL=https://api.bellbank.com
BELLBANK_API_KEY=your_api_key
BELLBANK_SECRET_KEY=your_secret_key
BELLBANK_WEBHOOK_SECRET=your_webhook_secret
BELLBANK_DIRECTOR_BVN=your_director_bvn
```

#### SMS Configuration (Twilio or local provider)
```
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM_NUMBER=your_twilio_number
```

#### Mail Configuration
```
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@kobopoint.com
MAIL_FROM_NAME="Kobopoint"
```

#### Redis Configuration (optional, for production)
```
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Monitoring (optional)
```
SENTRY_LARAVEL_DSN=your_sentry_dsn
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Create storage link

```bash
php artisan storage:link
```

### 7. Start the development server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/     # API Controllers
│   └── Requests/        # Form Request Validation
├── Services/            # Business Logic Layer
├── Repositories/        # Data Access Layer
├── Jobs/                # Queue Jobs
├── Policies/           # Authorization Policies
├── Observers/          # Model Observers
└── Models/             # Eloquent Models

routes/
├── api.php             # API Routes (versioned: /api/v1/...)
└── web.php             # Web Routes

database/
├── migrations/         # Database Migrations
├── seeders/           # Database Seeders
└── factories/         # Model Factories
```

## 🔌 API Endpoints

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/logout` - Logout user
- `POST /api/v1/auth/verify-otp` - Verify OTP
- `POST /api/v1/auth/resend-otp` - Resend OTP

### Wallet
- `GET /api/v1/wallet/balance` - Get wallet balance
- `POST /api/v1/wallet/fund` - Fund wallet
- `POST /api/v1/wallet/transfer` - Transfer funds
- `GET /api/v1/wallet/virtual-account` - Get virtual account
- `GET /api/v1/wallet/transactions` - Get transaction history

### Thrift (Ajo/Esusu)
- `POST /api/v1/thrift/create` - Create thrift group
- `POST /api/v1/thrift/join` - Join thrift group
- `POST /api/v1/thrift/contribute` - Make contribution
- `GET /api/v1/thrift/{id}` - Get thrift group details

### Savings
- `POST /api/v1/savings/create` - Create savings account
- `POST /api/v1/savings/deposit` - Deposit to savings
- `POST /api/v1/savings/withdraw` - Withdraw from savings
- `GET /api/v1/savings` - List savings accounts

### Payments
- `POST /api/v1/payments/send` - Send money
- `POST /api/v1/payments/bank-transfer` - Bank transfer
- `POST /api/v1/payments/qr` - QR payment
- `GET /api/v1/payments/status/{id}` - Payment status

### KYC
- `POST /api/v1/kyc/submit` - Submit KYC documents
- `GET /api/v1/kyc/status` - Get KYC status
- `POST /api/v1/kyc/verify-bvn` - Verify BVN

### Offline Transactions
- `POST /api/v1/offline/generate` - Generate offline voucher
- `POST /api/v1/offline/sync` - Sync offline transactions

### Webhooks
- `POST /api/v1/webhooks/bellbank` - BellBank webhook handler

## 🔒 Security Features

- Encrypted wallet balances
- Transaction signatures
- Rate limiting
- Device verification
- JWT token-based authentication (Laravel Sanctum)
- Two-factor authentication support
- HTTPS enforcement
- HSTS and CSP headers

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## 📊 Queue Workers

For production, set up queue workers:

```bash
php artisan queue:work --tries=3
```

Or use supervisor for process management.

## 🔄 Scheduled Tasks

The application uses Laravel's task scheduler. Add this to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks include:
- Thrift auto-debit processing
- Daily settlement runs
- BellBank reconciliation
- Clear stale sessions
- Interest computation

## 📦 Deployment (cPanel)

### Zero-Deploy Steps

1. Upload project files to server root
2. Set `public` folder as `public_html` or adjust `index.php` path
3. Update `.env` with production values
4. Run migrations:
   ```bash
   php artisan migrate --force
   ```
5. Optimize for production:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
6. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

## 🛠️ Artisan Commands

```bash
# Seed sample data
php artisan kobopoint:seed-sample

# Reconcile transactions
php artisan kobopoint:reconcile

# Generate offline code
php artisan kobopoint:generate-offline-code
```

## 📝 Environment Variables Reference

See `.env.example` for a complete list of all environment variables.

### Key Variables:

- **Database**: `DB_*` variables
- **BellBank**: `BELLBANK_*` variables
- **SMS**: `SMS_*`, `TWILIO_*` variables
- **Mail**: `MAIL_*` variables
- **Cache/Queue**: `CACHE_*`, `QUEUE_*`, `REDIS_*` variables
- **Security**: `SANCTUM_*`, `SESSION_*` variables
- **Monitoring**: `SENTRY_*` variables

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software. All rights reserved.

## 🆘 Support

For support, email support@kobopoint.com or open an issue in the repository.

## 🔗 Links

- API Documentation: `/api/documentation` (when Swagger is set up)
- Health Check: `/api/v1/healthz`
