### Patrones del Sistema

**Manejo de Sesiones Multi-Guard:**
- **Patrón:** Configuración dinámica de sesiones y guards de autenticación.
- **Descripción:** Se utiliza un middleware (`SetSessionTable`) para interceptar las solicitudes entrantes y modificar la configuración de la sesión y el guard de autenticación de Laravel en tiempo de ejecución. Esto permite que diferentes áreas de la aplicación (ej. `admin/*`) utilicen sus propias tablas de sesión y guards de autenticación, aislando las sesiones de usuarios normales de las sesiones de administradores.
- **Componentes Clave:**
    - `bootstrap/app.php`: Registra el middleware `SetSessionTable` para que se ejecute al inicio de la cadena de middlewares web.
    - `app/Http/Middleware/SetSessionTable.php`: Contiene la lógica para detectar rutas específicas (`admin/*`) y aplicar la configuración de sesión y guard correspondiente (`session.cookie`, `session.table`, `fortify.guard`, `auth.defaults.guard`).
- **Ventajas:**
    - Aislamiento de sesiones: Evita conflictos entre las sesiones de diferentes tipos de usuarios (ej. usuarios regulares y administradores).
    - Flexibilidad: Permite una gestión de autenticación más granular y adaptada a las necesidades de cada sección de la aplicación.
    - Seguridad: Mejora la seguridad al separar los contextos de sesión.
