# 💖 Formule of Love - Dedicatorias Semanales

Aplicación web desarrollada en PHP y MySQL que permite publicar mensajes semanales acompañados de canciones de YouTube.

Inspirado en la idea de expresar sentimientos a través de la música 💌🎶

---

## 🚀 Características

* 🔐 Sistema de autenticación (login admin)
* 🎵 Catálogo de canciones reutilizables
* 💖 Dedicatorias personalizadas por semana
* 📜 Historial de dedicatorias
* 📱 Diseño responsivo

---

## 🛠️ Tecnologías

* PHP (Vanilla)
* MySQL (phpMyAdmin)
* HTML5 + CSS3
* JavaScript (opcional)

---

## 📁 Estructura

/config → conexión a BD
/public → vistas principales
/src → lógica (MVC básico)
/uploads → archivos (opcional)

---

## ⚙️ Instalación

1. Clonar repositorio

2. Crear base de datos:

```sql
CREATE DATABASE `O+T`;
```

3. Importar tablas

4. Configurar `.env`

5. Ejecutar en servidor local:

* XAMPP
* Laragon

---

## 🔐 Acceso

Crear usuario manualmente con contraseña encriptada usando:

```php
password_hash("tu_password", PASSWORD_DEFAULT);
```

---

## 🌍 Deploy

Compatible con InfinityFree:

* Subir archivos a `/htdocs`
* Crear base de datos en el panel
* Ajustar `.env` con credenciales de producción

---

## 💡 Concepto

Este proyecto separa:

* 🎵 canciones (catálogo)
* 💌 dedicatorias (contenido semanal)

Permitiendo reutilizar canciones con diferentes mensajes.

---

## ❤️ Autor

SrCalvo Jazael

*"La música dice lo que el corazón no puede explicar."*
