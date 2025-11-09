# Patrones del Sistema

## Arquitectura del Sistema

El proyecto sigue una arquitectura monolítica con una clara separación de responsabilidades entre el backend y el frontend.

-   **Backend (Laravel):** Gestiona la lógica de negocio, las interacciones con la base de datos y los endpoints de la API. Sigue el patrón Modelo-Vista-Controlador (MVC), donde Inertia.js actúa como el puente en la capa de "Vista".
-   **Frontend (React/TypeScript):** Administra la interfaz de usuario y el estado del lado del cliente. Los componentes están organizados por funcionalidad y tipo (ej. `ui`, `pages`, `layouts`).

## Arquitectura de Autenticación: Multi-Guard

La aplicación implementa un sistema de autenticación "multi-guard" para separar completamente los dos tipos principales de usuarios:

1.  **Guard `web`:**
    *   **Modelo:** `App\Models\User`
    *   **Responsabilidad:** Gestiona a los usuarios del frontend (clientes). Estos usuarios tendrán roles como `Owner`, `Agent`, o `Bussines`.
    *   **Autenticación:** Manejada por Laravel Fortify.
    *   **Implementación:** Utiliza las configuraciones por defecto de Fortify para el guard `web`.

2.  **Guard `admin`:**
    *   **Modelo:** `App\Models\Admin`
    *   **Responsabilidad:** Gestiona a los administradores del sistema que acceden a un panel de control separado.
    *   **Autenticación:** Manejada a través de controladores y rutas personalizadas bajo el prefijo `/admin`.
    *   **Implementación Detallada:**
        *   **`FortifyServiceProvider.php`**: Se ha modificado el método `loginView` para renderizar `admin/auth/login` si la ruta actual es `admin.*`, permitiendo una vista de login específica para administradores.
        *   **`AdminAuthenticatedSessionController.php`**: Un controlador personalizado que extiende `Controller` y maneja la lógica de autenticación para el guard `admin`. Sobrescribe el guard de Fortify (`config(['fortify.guard' => 'admin', 'fortify.passwords' => 'admins'])`) durante el proceso de login para asegurar que Fortify autentique contra el modelo `Admin`.
        *   **`routes/admin.php`**: Contiene las rutas específicas para el login (`admin.login`, `admin.login.store`), logout (`admin.logout`) y el dashboard (`admin.dashboard`) de administradores, agrupadas bajo el prefijo `/admin` y protegidas por el middleware `guest:admin` o `auth:admin`.
        *   **`AdminLoginResponse.php`**: Una respuesta de login personalizada que redirige a `admin.dashboard` después de una autenticación exitosa para administradores.
        *   **`AdminLogoutResponse.php`**: Una respuesta de logout personalizada que redirige a `admin.login` después de que un administrador cierra sesión.

## Decisiones Técnicas Clave

-   **Inertia.js:** Elegido para construir una experiencia de aplicación de página única (SPA).
-   **TypeScript:** Utilizado para la seguridad de tipos en el frontend.
-   **Tailwind CSS:** Un framework de CSS "utility-first" para un desarrollo rápido de la UI.

## Relación entre Componentes y Convenciones de Nomenclatura

-   **`app.blade.php`:** Punto de entrada para la aplicación de Inertia.
-   **`app.tsx`:** Punto de entrada principal del frontend.
-   **`layouts/*.tsx`:** Diseños de página reutilizables. Se crearán layouts separados para el guard `web` y el panel de `admin`.
-   **`pages/**/*.tsx`:** Componentes de página individuales.
-   **`resources/js/components`:** Directorio principal para componentes reutilizables.
    *   **Convención de Nomenclatura:** Todas las carpetas y componentes dentro de `resources/js/components` deben seguir el mismo patrón de nombrado (ej. `camelCase` o `kebab-case` consistente) para mantener la uniformidad y evitar problemas de resolución de módulos y errores de casing en TypeScript.

## Relaciones de Modelos

-   **`User` y `UserProfile`:** Se ha establecido una relación uno a uno (`hasOne` en `User`, `belongsTo` en `UserProfile`). Cada `User` tiene un `UserProfile` asociado que contiene información detallada como `full_name`, `lastname`, `address`, `type`, `dni`, `ruc`, `phone`, `image_path`, `regular_publications` y `featured_publications`.
-   **`Admin` y `AdminProfile`:** Se ha establecido una relación uno a uno (`hasOne` en `Admin`, `belongsTo` en `AdminProfile`). Cada `Admin` tiene un `AdminProfile` asociado que contiene información como `full_name`, `dni`, `phone`, `address` y `image_path`.
