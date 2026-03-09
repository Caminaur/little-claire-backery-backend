# Laravel 12 + Sanctum — SPA Auth (Admin)

> Autenticación de administrador en un backend API-only usando Sanctum con cookies/sesión.  
> Compatible con React SPA y Postman.

---

## Índice

1. [Modelo Admin](#1-modelo-admin)
2. [Seeder de admin](#2-seeder-de-admin)
3. [Instalar Sanctum](#3-instalar-sanctum)
4. [Configuración auth.php](#4-configuración-authphp)
5. [Variables de entorno](#5-variables-de-entorno)
6. [Configuración CORS](#6-configuración-cors)
7. [Middleware en Laravel 12](#7-middleware-en-laravel-12)
8. [Request de login](#8-request-de-login)
9. [Controller de auth](#9-controller-de-auth)
10. [Rutas API](#10-rutas-api)
11. [Cómo funciona internamente](#11-cómo-funciona-internamente)
12. [Prueba en Postman](#12-prueba-en-postman)
13. [Script automático para Postman](#13-script-automático-para-postman)
14. [Errores comunes](#14-errores-comunes)
15. [Flujo en React](#15-flujo-en-react)
16. [Comandos útiles](#16-comandos-útiles)
17. [Resumen final](#17-resumen-final)

---

## 1. Modelo Admin

El modelo `Admin` debe extender `Authenticatable` y usar `HasApiTokens`.

```php
// app/Models/Admin.php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table    = 'admins';
    protected $fillable = ['email', 'password'];
    protected $hidden   = ['password'];
}
```

---

## 2. Seeder de admin

Crear el seeder:

```bash
php artisan make:seeder AdminSeeder
```

```php
// database/seeders/AdminSeeder.php

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email'    => 'admin@cafeteria.test'],
            ['password' => Hash::make('admin123')]
        );
    }
}
```

Registrar en `DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([AdminSeeder::class]);
}
```

Ejecutar:

```bash
php artisan db:seed
```

---

## 3. Instalar Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

## 4. Configuración auth.php

Como no hay users, el guard `web` debe apuntar al provider `admins`.

```php
// config/auth.php

'defaults' => [
    'guard'     => env('AUTH_GUARD', 'web'),
    'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
],

'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'admins',  // ← clave
    ],
],

'providers' => [
    'admins' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Admin::class,
    ],
],
```

Limpiar caché después de cualquier cambio en este archivo:

```bash
php artisan optimize:clear
```

---

## 5. Variables de entorno

> ⚠️ **Importante:** usar un solo host siempre. No mezclar `localhost` con `127.0.0.1`.

```env
APP_URL=http://localhost:8000
SESSION_DOMAIN=localhost
SESSION_SECURE_COOKIE=false
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000
```

```bash
php artisan optimize:clear
```

---

## 6. Configuración CORS

```php
// config/cors.php

return [
    'paths'                => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'      => ['*'],
    'allowed_origins'      => ['http://localhost:5173', 'http://localhost:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers'      => ['*'],
    'exposed_headers'      => [],
    'max_age'              => 0,
    'supports_credentials' => true,
];
```

```bash
php artisan optimize:clear
```

---

## 7. Middleware en Laravel 12

Laravel 12 no usa `Kernel.php`. Todo se configura en `bootstrap/app.php`.

```php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(HandleCors::class);

        $middleware->appendToGroup('api', EnsureFrontendRequestsAreStateful::class);
        $middleware->appendToGroup('api', EncryptCookies::class);
        $middleware->appendToGroup('api', AddQueuedCookiesToResponse::class);
        $middleware->appendToGroup('api', StartSession::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

```bash
php artisan optimize:clear
```

---

## 8. Request de login

```bash
php artisan make:request Admin/LoginAdminRequest
```

```php
// app/Http/Requests/Admin/LoginAdminRequest.php

class LoginAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
```

---

## 9. Controller de auth

```bash
php artisan make:controller AdminAuthController
```

```php
// app/Http/Controllers/AdminAuthController.php

class AdminAuthController extends Controller
{
    public function login(LoginAdminRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        $request->session()->regenerate();

        return response()->noContent();
    }

    public function me(Request $request)
    {
        $admin = $request->user();

        return response()->json([
            'id'    => $admin->id,
            'email' => $admin->email,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
```

---

## 10. Rutas API

> ℹ️ Las rutas de auth deben ir en `routes/api.php`, **no** en `web.php`.

```php
// routes/api.php

use App\Http\Controllers\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    // Público
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Protegido
    Route::middleware('auth:sanctum')->group(function () {
        Route::get ('/me',     [AdminAuthController::class, 'me']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);
    });

});
```

---

## 11. Cómo funciona internamente

```
Cliente                          Laravel
  │                                 │
  │── GET /sanctum/csrf-cookie ────▶│
  │◀─ cookies: XSRF-TOKEN ─────────│
  │           laravel_session       │
  │                                 │
  │── POST /api/admin/login ───────▶│ Auth::attempt() → guard web → provider admins
  │◀─ 204 No Content ──────────────│
  │                                 │
  │── GET /api/admin/me ───────────▶│ auth:sanctum valida sesión
  │◀─ { id, email } ───────────────│
  │                                 │
  │── POST /api/admin/logout ──────▶│ invalida sesión + regenera token
  │◀─ 204 No Content ──────────────│
```

---

## 12. Prueba en Postman

> ⚠️ **Requisitos:** `Disable cookies` debe estar en **OFF**. Usar siempre el mismo host. Enviar `Origin` y `X-XSRF-TOKEN`.

### Paso 1 — Pedir CSRF cookie

| Campo   | Valor                                    |
|---------|------------------------------------------|
| Method  | `GET`                                    |
| URL     | `http://localhost:8000/sanctum/csrf-cookie` |
| Accept  | `application/json`                       |
| Origin  | `http://localhost:5173`                  |

Laravel debe devolver las cookies `XSRF-TOKEN` y `laravel_session`.

### Paso 2 — Login

| Campo          | Valor                                  |
|----------------|----------------------------------------|
| Method         | `POST`                                 |
| URL            | `http://localhost:8000/api/admin/login` |
| Accept         | `application/json`                     |
| Origin         | `http://localhost:5173`                |
| X-XSRF-TOKEN   | valor decodificado de la cookie        |

Body JSON:

```json
{
  "email": "admin@cafeteria.test",
  "password": "admin123"
}
```

### Paso 3 — Ver usuario actual

| Campo   | Valor                               |
|---------|-------------------------------------|
| Method  | `GET`                               |
| URL     | `http://localhost:8000/api/admin/me` |
| Accept  | `application/json`                  |
| Origin  | `http://localhost:5173`             |

### Paso 4 — Logout

| Campo          | Valor                                     |
|----------------|-------------------------------------------|
| Method         | `POST`                                    |
| URL            | `http://localhost:8000/api/admin/logout`  |
| Accept         | `application/json`                        |
| Origin         | `http://localhost:5173`                   |
| X-XSRF-TOKEN   | valor decodificado de la cookie           |

No requiere body.

---

## 13. Script automático para Postman

Agregar este **Pre-request Script** en login y logout para no copiar el token manualmente:

```js
const xsrf = pm.cookies.get('XSRF-TOKEN');

if (!xsrf) {
    throw new Error('Missing XSRF-TOKEN cookie. Call /sanctum/csrf-cookie first.');
}

pm.request.headers.upsert({ key: 'X-XSRF-TOKEN', value: decodeURIComponent(xsrf) });
pm.request.headers.upsert({ key: 'Origin',        value: 'http://localhost:5173' });
pm.request.headers.upsert({ key: 'Accept',        value: 'application/json' });
pm.request.headers.remove('X-CSRF-TOKEN');
```

---

## 14. Errores comunes

| Error | Causa y solución |
|-------|-----------------|
| `Session store not set on request` | Falta `StartSession` en el grupo `api` de `bootstrap/app.php`. |
| `Unauthenticated.` | El guard `web` no apunta a `admins`, o Sanctum no lee la sesión correcta. |
| `Invalid credentials.` | `web` sigue apuntando a `users`, o no se limpió caché tras cambiar `auth.php`. |
| `CSRF token mismatch` | No se envía `X-XSRF-TOKEN`, o hay mismatch de host (`localhost` vs `127.0.0.1`). |
| Cookies no se guardan en Postman | `Disable cookies` activado, o mismatch entre dominio de la cookie y URL usada. |

---

## 15. Flujo en React

Con axios:

```js
import axios from 'axios';

axios.defaults.withCredentials = true;

// 1. CSRF cookie
await axios.get('http://localhost:8000/sanctum/csrf-cookie');

// 2. Login
await axios.post('http://localhost:8000/api/admin/login', {
    email:    'admin@cafeteria.test',
    password: 'admin123',
});

// 3. Me
const { data } = await axios.get('http://localhost:8000/api/admin/me');

// 4. Logout
await axios.post('http://localhost:8000/api/admin/logout');
```

---

## 16. Comandos útiles

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
php artisan make:seeder AdminSeeder
php artisan make:request Admin/LoginAdminRequest
php artisan make:controller AdminAuthController
php artisan db:seed
php artisan optimize:clear
php artisan tinker
```

---

## 17. Resumen final

| Componente     | Implementación                              |
|----------------|---------------------------------------------|
| Modelo         | `Admin extends Authenticatable`             |
| Guard          | `web` → provider `admins`                  |
| Autenticación  | Sanctum con cookies/sesión                  |
| Middleware     | `stateful` + `session` en grupo `api`       |
| Rutas          | `routes/api.php`                            |
| Login          | `Auth::attempt()`                           |
| Protección     | `auth:sanctum` en `/me` y `/logout`         |
| Postman        | `Origin` + `X-XSRF-TOKEN`                  |
