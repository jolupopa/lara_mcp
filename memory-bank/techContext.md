# Contexto Técnico

## Tecnologías Utilizadas

-   **Backend:** Laravel (PHP Framework)
-   **Frontend:** React.js con TypeScript
-   **UI Framework:** Tailwind CSS
-   **SPA Bridge:** Inertia.js
-   **Autenticación:** Laravel Fortify (personalizado para multi-guard)

## Configuración de Desarrollo

-   **Entorno:** PHP 8.2+, Node.js 18+, Composer, npm/Yarn.
-   **Servidor Web:** Laravel Sail (Docker) o Nginx/Apache.
-   **Base de Datos:** MySQL/PostgreSQL.
-   **Gestión de Dependencias:** Composer (PHP), npm/Yarn (JavaScript).
-   **Bundler:** Vite.js

## Restricciones Técnicas

-   El proyecto se adhiere a la filosofía de "monorepo" con Laravel y React/Inertia en el mismo repositorio.
-   Se evita el uso de Ziggy para la gestión de rutas en el frontend, pasando las URLs directamente como props de Inertia.

## Patrones de Uso de Herramientas

-   **Laravel Artisan:** Utilizado para la generación de código (modelos, migraciones, controladores, seeders), gestión de la base de datos y otras tareas de backend.
-   **Vite:** Empleado para la compilación y hot-reloading de los assets de frontend.
-   **Composer:** Para la gestión de dependencias de PHP.
-   **npm/Yarn:** Para la gestión de dependencias de JavaScript.

## Implementación de Autenticación Multi-Guard (Detalles Técnicos)

### 1. `config/auth.php`
Se han definido los guards `web` y `admin`, y sus respectivos providers (`users` y `admins`).

```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
],
```

### 2. `app/Providers/FortifyServiceProvider.php`
El método `boot` ha sido modificado para personalizar la vista de login de Fortify, permitiendo que se renderice `admin/auth/login` si la ruta actual es para administradores.

```php
// app/Providers/FortifyServiceProvider.php
Fortify::loginView(fn (Request $request) => Inertia::render(
    $request->routeIs('admin.*')
        ? 'admin/auth/login'
        : 'auth/login',
    [
        'canResetPassword' => Features::enabled(Features::resetPasswords()),
        'canRegister' => Features::enabled(Features::registration()),
        'status' => $request->session()->get('status'),
    ]
));
```

### 3. `app/Http/Controllers/Admin/AdminAuthenticatedSessionController.php`
Este controlador maneja la autenticación para el guard `admin`. Es crucial que en el método `store`, se sobrescriba la configuración de Fortify para usar el guard `admin` y el provider `admins`.

```php
// app/Http/Controllers/Admin/AdminAuthenticatedSessionController.php
class AdminAuthenticatedSessionController extends Controller
{
    // ... constructor y otros métodos ...

    public function store(LoginRequest $request)
    {
        // Sobrescribe el guard y los passwords de Fortify para el contexto admin
        config(['fortify.guard' => 'admin', 'fortify.passwords' => 'admins']);

        return $this->loginPipeline($request)->then(function ($request) {
            return app(AdminLoginResponse::class);
        });
    }

    public function destroy(Request $request): LogoutResponseContract
    {
        $this->guard->logout(); // Asegura que el guard 'admin' sea el que se desloguea

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return app(AdminLogoutResponse::class);
    }
}
```

### 4. `routes/admin.php`
Las rutas para el login y logout de administradores están definidas en este archivo, agrupadas bajo el prefijo `/admin` y utilizando el middleware `guest:admin` para el login y `auth:admin` para el dashboard y logout.

```php
// routes/admin.php
Route::group(['prefix' => 'admin'], function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware(['auth:admin'])->group(function () { // 'verified' puede ser añadido si se implementa verificación de email para admins
        Route::get('dashboard', function () {
            return Inertia::render('admin/dashboard');
        })->name('admin.dashboard');
        Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
    });
});
```

### 5. `app/Http/Responses/AdminLoginResponse.php`
Esta clase implementa `LoginResponseContract` y define la redirección post-login para administradores.

```php
// app/Http/Responses/AdminLoginResponse.php
class AdminLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(route('admin.dashboard'));
    }
}
```

### 6. `app/Http/Responses/AdminLogoutResponse.php`
Esta clase implementa `LogoutResponseContract` y define la redirección post-logout para administradores.

```php
// app/Http/Responses/AdminLogoutResponse.php
class AdminLogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect(route('admin.login'));
    }
}
