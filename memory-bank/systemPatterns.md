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

2.  **Guard `admin`:**
    *   **Modelo:** `App\Models\Admin`
    *   **Responsabilidad:** Gestiona a los administradores del sistema que acceden a un panel de control separado.
    *   **Autenticación:** Manejada a través de controladores y rutas personalizadas bajo el prefijo `/admin`.

## Gestión de Roles y Permisos

-   **Paquete Utilizado:** `spatie/laravel-permission`
-   **Implementación:** Ambos modelos, `User` y `Admin`, utilizan el trait `HasRoles`. Esto permite una gestión granular de permisos y roles para cada guard de forma independiente.
-   **Estructura:**
    -   Se definirán roles claros (ej. `Super-Admin`, `Agent`).
    -   Se asignarán permisos específicos a cada rol (ej. `manage-users`, `publish-properties`).

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
