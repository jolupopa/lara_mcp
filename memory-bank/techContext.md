### Contexto Técnico

**Tecnologías Utilizadas:**
- **Laravel Framework:** Se utiliza una versión reciente de Laravel que soporta la nueva estructura de `bootstrap/app.php`.
- **PHP:** Versión compatible con la instalación de Laravel.
- **Fortify:** Se emplea para gestionar la autenticación. La configuración de Fortify (`fortify.guard`) se modifica dinámicamente en el middleware.
- **Middleware de Laravel:** El núcleo de la implementación se basa en un middleware (`SetSessionTable`) que se antepone a las solicitudes web.

**Patrones de Uso de Herramientas:**
- **Configuración en Tiempo de Ejecución:** Se utiliza `Illuminate\Support\Facades\Config::set()` para modificar la configuración de la aplicación sobre la marcha. Esto es crucial para cambiar la tabla de sesión y el guard de autenticación sin necesidad de archivos de configuración estáticos separados.
- **Enrutamiento de Laravel:** La lógica del middleware se activa basándose en patrones de ruta (`$request->is('admin/*')`), lo que demuestra el uso del sistema de enrutamiento para controlar la lógica de la aplicación.
