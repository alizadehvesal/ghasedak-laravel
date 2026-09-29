# Ghasedak Laravel

پکیج ساده و قابل توسعه Laravel برای ارسال پیامک از طریق **وب‌سرویس قالب‌های آماده قاصدک**.

این نسخه روی سرویس زیر تمرکز دارد:

`POST http://api.ghasedaksms.com/v2/send/verify`

امکانات فعلی:

- ارسال پیامک با قالب آماده
- پشتیبانی از `param1` تا `param3`
- پشتیبانی از چندین قالب در یک پروژه با کلیدهای منطقی دلخواه
- ارسال مستقیم با نام قالب یا از طریق کلید قالب تنظیم‌شده در `config/ghasedak.php`
- ارسال `checkingids`
- پشتیبانی از `type=1` پیامک و `type=2` پیام صوتی
- مدیریت خطاهای HTTP و خطاهای API قاصدک
- Laravel HTTP Client و بدون وابستگی HTTP اضافه
- تست‌های PHPUnit با `Http::fake()`

## نصب

```bash
composer require vestra/ghasedak-laravel
```

سپس تنظیمات را منتشر کنید:

```bash
php artisan vendor:publish --tag=ghasedak-config
```

## تنظیم API Key

در `.env`:

```env
GHASEDAK_API_KEY=your-api-key
GHASEDAK_BASE_URL=http://api.ghasedaksms.com/v2
GHASEDAK_TIMEOUT=10
GHASEDAK_CONNECT_TIMEOUT=5
```

## تعریف چند قالب

یک پیامک می‌تواند در پروژه چند قالب مختلف داشته باشد. نام قالب‌ها را در `config/ghasedak.php` تعریف کنید:

```php
'templates' => [
    'verification' => 'login_code',
    'invoice' => 'invoice_code',
    'order' => 'order_status',
],
```

کلید سمت چپ متعلق به پروژه شماست و مقدار سمت راست باید دقیقاً نام قالب ساخته‌شده در پنل قاصدک باشد.

## استفاده

### ارسال با کلید قالب

```php
use Vestra\Ghasedak\Facades\Ghasedak;

$result = Ghasedak::sendUsingTemplate(
    '09122222222',
    'verification',
    ['123456'],
);
```

### ارسال با سه پارامتر

```php
$result = Ghasedak::sendUsingTemplate(
    '09122222222',
    'invoice',
    ['INV-1024', '2500000', 'Vestra'],
);
```

### ارسال مستقیم با نام قالب قاصدک

```php
$result = Ghasedak::sendTemplate(
    '09122222222',
    'login_code',
    ['123456'],
);
```

### checkingids

برای جلوگیری از ارسال مجدد در شرایطی مانند Timeout، مقدار `checkingids` را ارسال کنید تا در صورت نیاز بعداً وضعیت پیام را از سرویس بررسی کنید:

```php
$result = Ghasedak::sendUsingTemplate(
    '09122222222',
    'verification',
    ['123456'],
    'unique-request-id',
);
```

### بررسی نتیجه

```php
if ($result->successful()) {
    $messageId = $result->firstMessageId();
}
```

همچنین:

```php
$result->status();
$result->result();
$result->messageIds();
$result->message();
$result->json();
```

## مدیریت خطا

پکیج در خطاهای HTTP یا پاسخ‌هایی که `result` آن‌ها `success` نیست، `GhasedakException` پرتاب می‌کند:

```php
use Vestra\Ghasedak\Exceptions\GhasedakException;

try {
    Ghasedak::sendUsingTemplate('09122222222', 'verification', ['123456']);
} catch (GhasedakException $e) {
    logger()->error('Ghasedak SMS failed', [
        'message' => $e->getMessage(),
        'code' => $e->ghasedakCode,
        'payload' => $e->payload,
    ]);
}
```

## خطاهای مستندشده قاصدک

| کد | توضیح |
|---:|---|
| 1 | نام کاربری یا رمز عبور معتبر نیست. |
| 2 | آرایه‌ها خالی است. |
| 3 | طول آرایه بیشتر از 100 است. |
| 4 | طول آرایه فرستنده، گیرنده و متن یکسان نیست. |
| 5 | امکان گرفتن پیام جدید وجود ندارد. |
| 6 | حساب کاربری غیرفعال یا اطلاعات وب‌سرویس نادرست است. |
| 7 | امکان دسترسی به خط موردنظر وجود ندارد. |
| 8 | شماره گیرنده نامعتبر است. |
| 9 | اعتبار ریالی کافی نیست. |
| 10 | خطای سیستمی؛ دوباره تلاش کنید. |
| 11 | IP نامعتبر است. |
| 20 | شماره مخاطب فیلتر شده است. |
| 21 | ارتباط با سرویس‌دهنده قطع است. |

## محدودیت پارامترها

قالب‌های این سرویس باید حداقل یک پارامتر داشته باشند و حداکثر سه پارامتر قابل ارسال است:

- `%param1%` الزامی
- `%param2%` اختیاری
- `%param3%` اختیاری

در این پکیج آرایه پارامترها به ترتیب به `param1`، `param2` و `param3` تبدیل می‌شود.

## تست

```bash
composer install
composer test
```

## توسعه‌های آینده

این نسخه عمداً کوچک نگه داشته شده تا API آن ساده باشد. قابلیت‌هایی مثل بررسی وضعیت `checkingids`، ارسال گروهی، گزارش وضعیت پیام، retry هوشمند و abstraction برای چند سرویس پیامکی می‌توانند در نسخه‌های بعدی اضافه شوند.

## License

MIT
"# ghasedak-laravel" 
