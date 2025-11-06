# Contexto Activo

## Enfoque de Trabajo Actual

-   La implementación de la arquitectura de autenticación multi-guard (`web` y `admin`) ha sido completada y verificada.
-   El siguiente paso es integrar el paquete `spatie/laravel-permission` para la gestión de roles y permisos.

## Cambios Recientes

-   Se ha configurado correctamente el sistema multi-guard de autenticación para `User` y `Admin`.
-   **`app/Providers/FortifyServiceProvider.php`**: Modificado para manejar las vistas de login de `admin` y `web` de forma condicional.
-   **`app/Http/Controllers/Admin/AdminAuthenticatedSessionController.php`**: Creado para manejar la autenticación de administradores, sobrescribiendo el guard y los passwords de Fortify para el contexto `admin`.
-   **`routes/admin.php`**: Definidas las rutas de login, logout y dashboard para el guard `admin`.
-   **`app/Http/Responses/AdminLoginResponse.php`**: Creado para redirigir a los administradores al dashboard de `admin` después del login.
-   **`app/Http/Responses/AdminLogoutResponse.php`**: Creado para redirigir a la página de login de `admin` después del logout.

## Próximos Pasos

1.  **Instalar `spatie/laravel-permission`:** Añadir el paquete al proyecto.
2.  **Configurar `laravel-permission`:** Publicar migraciones y configuración.
3.  **Ejecutar Migraciones:** Correr `php artisan migrate` para crear las tablas de roles y permisos.
4.  **Crear Seeder de Roles y Permisos:** Desarrollar un seeder para roles y permisos iniciales.
5.  **Integrar Roles en Modelos:** Añadir el trait `HasRoles` a los modelos `User` y `Admin`.

## Decisiones y Consideraciones Activas

-   La separación de la lógica de autenticación para `admin` en un controlador dedicado (`AdminAuthenticatedSessionController`) y respuestas personalizadas (`AdminLoginResponse`, `AdminLogoutResponse`) asegura una clara distinción y evita conflictos con la autenticación de usuarios `web`.
-   La configuración dinámica del guard de Fortify en `AdminAuthenticatedSessionController@store` es crucial para que Fortify use el guard `admin` durante el proceso de autenticación de administradores.

## Aprendizajes y Perspectivas del Proyecto

-   La implementación de un sistema multi-guard con Fortify requiere una cuidadosa configuración de los guards, providers y la personalización de las respuestas de login/logout para cada tipo de usuario.
-   La capacidad de Fortify para ser extendido y personalizado a través de contratos y acciones permite una gran flexibilidad en escenarios de autenticación complejos.

## Manejo de Rutas en Laravel + Inertia + React (Sin Ziggy)

### 1. Qué cambió en el nuevo kit de Laravel + Inertia + React

Antes (Laravel ≤ 11 con Ziggy):

```javascript
post(route('users.store'))
```

Ahora (Laravel 12 + Inertia v2 Starter Kit):

```javascript
post(route('users.store'))
// ❌  Ziggy ya no existe
// ✅  Se pasa la URL desde el backend como prop, o se usa el helper del controller (store.form)
```

Laravel ahora expone las rutas al frontend a través de props de Inertia en lugar de usar Ziggy.
El frontend no necesita saber los nombres de rutas, solo usa los objetos que vienen del backend.

### 2. Cómo se ve esto en el backend (Laravel Controller)

Ejemplo: `UserController.php`

```php
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::all(),
            'store' => [
                'form' => route('users.store'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        User::create($validated);

        return redirect()->back()->with('success', 'Usuario creado');
    }
}
```

Aquí:

El controlador pasa un objeto `store` con la URL de `users.store`.

El frontend accede a eso como `store.form`.

### 3. Cómo se usa en el componente React

```javascript
import { useForm } from '@inertiajs/react';

export default function UsersIndex({ users, store }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    email: '',
  });

  const submit = (e) => {
    e.preventDefault();
    post(store.form, {
      onSuccess: () => reset(),
    });
  };

  return (
    <div>
      <h1>Usuarios</h1>

      <form onSubmit={submit}>
        <input
          type="text"
          placeholder="Nombre"
          value={data.name}
          onChange={(e) => setData('name', e.target.value)}
        />
        {errors.name && <div>{errors.name}</div>}

        <input
          type="email"
          placeholder="Correo"
          value={data.email}
          onChange={(e) => setData('email', e.target.value)}
        />
        {errors.email && <div>{errors.email}</div>}

        <button type="submit" disabled={processing}>
          {processing ? 'Guardando...' : 'Guardar'}
        </button>
      </form>

      <ul>
        {users.map(user => (
          <li key={user.id}>{user.name} ({user.email})</li>
        ))}
      </ul>
    </div>
  );
}
```

✅ Observa:

No se usa `route('users.store')` (no Ziggy).

Se usa la URL que viene como prop (`store.form`).

Esto es compatible con la nueva estructura del Laravel React Starter Kit (Laravel Breeze + Inertia v2).

### 4. Qué pasa con los `<Link>`

El principio es el mismo:
Laravel pasa las URLs (rutas generadas) al frontend.

Por ejemplo:

```php
return Inertia::render('Users/Show', [
    'user' => $user,
    'links' => [
        'edit' => route('users.edit', $user),
        'destroy' => route('users.destroy', $user),
    ],
]);
```

Y en React:

```javascript
import { Link } from '@inertiajs/react';

export default function UserShow({ user, links }) {
  return (
    <div>
      <h1>{user.name}</h1>
      <Link href={links.edit}>Editar</Link>
      <Link href={links.destroy} method="delete" as="button">
        Eliminar
      </Link>
    </div>
  );
}
```

💡 Ya no necesitas `route()` en el frontend.
El backend genera las URLs reales, el frontend solo las usa.

### 5. Ventajas del nuevo enfoque

| Ventaja                 | Explicación                                                              |
| :---------------------- | :----------------------------------------------------------------------- |
| 🔒 Seguridad y control total | El frontend no conoce los nombres ni estructura de rutas.                |
| ⚡ Sin dependencias externas | Ya no necesitas Ziggy.                                                   |
| 🔁 Reutilizable         | Las URLs se pasan en objetos (`store`, `links`, etc.) y se pueden compartir entre componentes. |
| 🧼 Limpieza             | Las rutas se definen una sola vez (en Laravel) y se consumen como props. |

### 6. Resumen general del nuevo flujo

```
[Laravel Controller]
   ↓
return Inertia::render('Componente', [
   'store' => ['form' => route('users.store')],
   'links' => ['edit' => route('users.edit', $user)],
]);
   ↓
[Frontend React]
   ↓
post(store.form) / <Link href={links.edit}>
   ↓
[Inertia v2]
   ↓
Realiza la visita SPA a Laravel y devuelve nuevos props
```

✅ Conclusión práctica

Ziggy ya no se usa en el nuevo kit de Laravel + React.

Las rutas se inyectan desde Laravel como props (`store.form`, `links.edit`, etc.).

Desde el frontend:

Navegas con `<Link href={props.links.algo}>`.

Envías formularios con `post(props.store.form)` o `put(props.update.form)`.
