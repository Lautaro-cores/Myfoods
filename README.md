# 🍲 MyFoods: El Hogar Centralizado de la Pasión Culinaria

![Logo de MyFoods - Placeholder](img/logo.png)

## ✨ Descripción General

**MyFoods** es una red social especializada diseñada para resolver la dispersión de información culinaria en internet. La plataforma ofrece un ecosistema digital único donde cocineros aficionados y profesionales pueden **descubrir, compartir y guardar** todas sus ideas y recetas favoritas en un solo lugar.

Nuestro objetivo es fomentar una comunidad activa, haciendo que el intercambio de conocimientos y creaciones gastronómicas sea eficiente, divertido y centralizado.

## 🎯 Objetivos del Proyecto

* Crear un ecosistema digital donde la pasión por la cocina tenga un hogar propio.
* Facilitar la búsqueda, publicación y el intercambio de recetas de manera eficiente.
* Fomentar la creación de una comunidad activa y participativa de amantes de la gastronomía.

---

## 💻 Tecnologías Utilizadas (Stack)

El proyecto MyFoods está construido con una arquitectura sólida y probada:

| Capa | Tecnología | Propósito |
| :--- | :--- | :--- |
| **Back-End** | **PHP** | Lógica de negocio, gestión de sesiones y comunicación con la base de datos. |
| **Front-End** | **HTML, CSS, JavaScript** | Estructura, diseño visual, interactividad y experiencia de usuario (UX/UI). |
| **Base de Datos**| **MySQL** | Almacenamiento y gestión eficiente de usuarios, recetas y datos de interacción. |

## 🚀 Instalación y Ejecución Local

Sigue estos pasos para configurar y ejecutar MyFoods en tu entorno local.

### Prerrequisitos

* **Servidor Web Local:** Un entorno como XAMPP (recomendado), WAMP o MAMP.
* **Editor de Código:** Visual Studio Code.
* **Control de Versiones:** Git.

### Pasos de Configuración

1.  **Clonar el Repositorio:**
    ```bash
    git clone [https://github.com/Lautaro-cores/Myfoods.git](https://github.com/Lautaro-cores/Myfoods.git)
    cd Myfoods
    ```

2.  **Configurar la Base de Datos (MySQL):**
    * Crea una base de datos en tu entorno MySQL llamada `[Nombre de tu DB, ej: foodmaster_db]`.
    * [Instrucciones para importar el script SQL si aplica. Ejemplo: Importa el archivo `[ruta/a/tu/script.sql]` usando phpMyAdmin o tu herramienta preferida.]

3.  **Configurar el Servidor (PHP):**
    * Coloca el código fuente en el directorio de tu servidor web local (ej: `htdocs` en XAMPP).
    * Asegúrate de que la configuración de conexión a la base de datos en los archivos PHP (ej: `config.php` o similar) apunte a tu base de datos MySQL local.

4.  **Ejecutar la Aplicación:**
    * Inicia los servicios de Apache y MySQL en XAMPP (o similar).
    * Accede a la aplicación a través de tu navegador en: `http://localhost/[ruta-a-tu-proyecto]`.

---

## 📋 Alcance y Funcionalidades Principales

El proyecto se estructura en los siguientes módulos funcionales:

| Módulo | Descripción | Estado (Logros Sprints 1-6) |
| :--- | :--- | :--- |
| **Sector de Usuario** | Registro, inicio de sesión y perfiles individuales. | Autenticación funcional. Vista de perfil de usuario e implementación para cambio/actualización de foto. Vista de perfiles públicos. |
| **Módulo de Recetas** | Permite publicar, editar y visualizar recetas detalladas con ingredientes, pasos y fotos. | Estructura para mostrar ingredientes y pasos. Vista de receta individual. |
| **Módulo de Interacción** | Fomenta la comunidad con Likes, Comentarios, Guardar/Favoritos y Seguimiento de usuarios. | Implementación completa de **Comentarios** y **Sistema de Likes** unificado. Función de **Guardar/Favoritos**. |
| **Módulo de Búsqueda** | Buscador inteligente con filtrado por texto, categorías e ingredientes. | Función de **Búsqueda y Filtrado** por texto y **Etiquetas (Tags)** implementada. |
| **Módulo de Moderación** | Sistema de reportes de contenido inapropiado y Panel de Administración. | Inicio de la estructura y la interfaz base para el **Panel de Administración**. |

---

## 👥 Equipo y Roles

| Miembro | Rol Principal |
| :--- | :--- |
| **Lautaro Cores** | Scrum Master, Back-End |
| **Mateo Medina** | Back-End |
| **Pietro Romero** | Administrador de Base de Datos |
| **Agustina Gerace** | Administradora de Base de Datos |
| **Thomas Iglesias** | Front-End |
| **Victoria Villamizar** | Diseñadora UX/UI, Front-End |
| **Martin Molina** | Front-End, Tester |

---

## 🤝 Contribución

Si te apasiona la cocina y el desarrollo web, ¡tu contribución es bienvenida! Siéntete libre de abrir un **Issue** para reportar errores o sugerir mejoras.

Para contribuir con código:

1.  Haz un **Fork** de este repositorio.
2.  Crea una nueva rama (`git checkout -b feature/nombre-de-tu-rama`).
3.  Realiza tus cambios y haz **Commit**.
4.  Abre un **Pull Request**.

## 🔗 Anexos y Documentación

* **Tablero de Tareas (Trello):** https://trello.com/b/YkBIKO8t/foodmaster
* **Documentación de Base de Datos:** https://docs.google.com/document/d/1uCD3NHYrTuaB1C-pqBlHjlsxbZ9kE4d1P1qo8HZfeKw/edit?usp=sharing
* **Documentación Front-End (Diseño):** https://docs.google.com/document/d/1zpTCYUR7a7EtxP-P_x1XaCnWXZr3IWpf/edit
* **Documentación de Sprints:** https://docs.google.com/document/d/1qUasZi0P40Yk_WzQe-LK33NWjszn8cwBuo0y0UdYDhI/edit?tab=t.0

---
