# 📁 Sistema de Gestión de Usuarios

Este proyecto permite registrar, editar, eliminar y cuenta con generación automática de contratos en PDF y firma digital. Con enfoque en buenas prácticas, documentación clara y uso de librerías modernas.

---

## ✍️ Proceso de desarrollo

_Aquí puedes escribir tu experiencia, decisiones técnicas, retos superados, y aprendizajes. Por ejemplo:_

> Este proyecto lo desarrollé en un día, priorizando funcionalidad y claridad. Usé PHP con el patrón MVC básico, integré Tailwind para estilos rápidos y limpios, y documenté cada método para facilitar el mantenimiento. Aprendí a generar PDF con FPDF y a validar correos en tiempo real. También reforcé el uso de controladores y vistas separadas, he de admitir que estaba muy oxidado en cuestión de PHP y sus funciones, pero di un esfuerzo en cumplir y generar de forma correcta lo solicitado.

---

## 🧰 Librerías utilizadas

- **SweetAlert2**  
  Usada para mostrar alertas visuales y confirmaciones al eliminar usuarios. Mejora la experiencia del usuario con modales elegantes y personalizables.

- **FPDF**  
  Utilizada para generar contratos en formato PDF automáticamente, incluyendo los datos del usuario y su firma digital.

- **Tailwind CSS**  
  Framework de estilos utilitario que permite construir interfaces modernas y responsivas con clases semánticas y flexibles.

---

## 🗄️ Backup de la base de datos

Se incluye un archivo `crud_prueba.sql` con la estructura y datos de ejemplo de la base de datos.  
Puedes encontrarlo en la carpeta `config/crud_prueba.sql`.

Para restaurarlo:

1. Abre **phpMyAdmin**.
2. Crea una base de datos con el nombre deseado.
3. Ve a la pestaña **Importar**.
4. Selecciona el archivo `.sql` y haz clic en **Continuar**.

---

## 🚀 Cómo ejecutar el proyecto

1. Clona el repositorio.
2. Configura la conexión en `config/database.php`.
3. Asegúrate de tener PHP y MySQL activos.
4. Importa la base de datos desde el archivo `.sql`.
5. Accede desde tu navegador a `index.php`.

---