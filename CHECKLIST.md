# ✅ CHECKLIST DE CONFIGURACIÓN

Usa este checklist para asegurarte de que configuraste todo correctamente.

## 📋 ANTES DE COMENZAR

- [ ] Verificar que tienes PHP 7.4+ instalado
  ```bash
  php -v
  ```

- [ ] Verificar que tienes MySQL/MariaDB instalado
  ```bash
  mysql --version
  ```

- [ ] Descargar/clonar el repositorio
  ```bash
  git clone tu-repo.git
  cd tu-repo
  ```

---

## 🗄️ CONFIGURACIÓN DE BASE DE DATOS

- [ ] Crear base de datos ejecutando database.sql
  ```bash
  mysql -u root < database.sql
  ```

- [ ] Verificar que las tablas fueron creadas
  - [ ] `usuarios` con datos de ejemplo
  - [ ] `ventas` con datos de ejemplo
  - [ ] `productos` con datos de ejemplo
  - [ ] `categorias` con datos de ejemplo

- [ ] Verificar credenciales de prueba:
  - [ ] admin@pesado.com / password123 existe como ADMIN
  - [ ] user@pesado.com / password123 existe como USER

---

## 🔧 CONFIGURACIÓN DE LA APLICACIÓN

- [ ] Copiar .env.example a .env
  ```bash
  cp .env.example .env
  ```

- [ ] Editar .env con credenciales de MySQL
  ```
  DB_HOST=localhost
  DB_USER=root
  DB_PASS=(tu contraseña si existe)
  DB_NAME=pesado_fallo
  DB_PORT=3306
  ```

- [ ] Verificar que todos estos archivos existen:
  - [ ] config.php
  - [ ] db.php
  - [ ] auth.php
  - [ ] login.php
  - [ ] landing.php
  - [ ] dashboard.php
  - [ ] index.php
  - [ ] styles.css
  - [ ] database.sql

---

## 🚀 PRUEBA LOCAL

- [ ] Iniciar servidor PHP
  ```bash
  php -S localhost:8000
  ```

- [ ] Abrir navegador en http://localhost:8000/login.php

- [ ] Probar login como ADMIN
  - [ ] Email: admin@pesado.com
  - [ ] Contraseña: password123
  - [ ] Debería redirigir a /dashboard.php

- [ ] Verificar dashboard
  - [ ] Ver estadísticas de ventas hoy
  - [ ] Ver tabla de usuarios
  - [ ] Ver tabla de ventas recientes
  - [ ] Poder agregar usuario (+ Agregar Usuario)
  - [ ] Poder editar usuario
  - [ ] Poder eliminar usuario

- [ ] Logout desde dashboard
  - [ ] Hacer clic en "Cerrar sesión"
  - [ ] Debería redirigir a /login.php

- [ ] Probar login como USER
  - [ ] Email: user@pesado.com
  - [ ] Contraseña: password123
  - [ ] Debería redirigir a /landing.php

- [ ] Verificar landing page
  - [ ] Ver saludo: "Hola, [Nombre]"
  - [ ] En esquina superior derecha
  - [ ] Poder cerrar sesión

- [ ] Logout desde landing
  - [ ] Hacer clic en "Salir"
  - [ ] Debería redirigir a /login.php

---

## 🔐 PRUEBA DE SEGURIDAD

- [ ] Intentar acceder a /dashboard.php sin login
  - Debería redirigir a /login.php

- [ ] Intentar acceder a /landing.php sin login
  - Debería redirigir a /login.php

- [ ] Login como USER e intentar ir a /dashboard.php
  - Debería mostrar error 403

- [ ] Intentar login con email inválido
  - Debería mostrar "Credenciales inválidas"

- [ ] Intentar login con contraseña incorrecta
  - Debería mostrar "Credenciales inválidas"

---

## 📝 PERSONALIZACIÓN

- [ ] Cambiar nombre de la empresa en styles.css si es necesario

- [ ] Actualizar logo/branding en landing.php

- [ ] Cambiar contraseña de usuarios de prueba:
  ```sql
  UPDATE usuarios SET contraseña = PASSWORD_HASH 
  WHERE email = 'admin@pesado.com';
  ```

- [ ] Agregar más productos a la tabla `productos`

- [ ] Agregar más categorías a la tabla `categorias`

---

## 🎯 ANTES DE DESPLEGAR EN RAILWAY

- [ ] Crear archivo .env.production con credenciales de Railway
  ```
  ENVIRONMENT=production
  APP_URL=https://tu-app.railway.app
  DB_HOST=...
  DB_USER=...
  DB_PASS=...
  ```

- [ ] Cambiar todas las contraseñas de usuarios de prueba

- [ ] Ejecutar database.sql en BD de Railway

- [ ] Verificar que railway.json está correcto

- [ ] Verificar que Procfile está correcto

- [ ] Verificar que .gitignore incluye .env

- [ ] Hacer commit y push de todos los archivos
  ```bash
  git add .
  git commit -m "Agregar sistema de autenticación y dashboard"
  git push
  ```

---

## 🚀 DESPLIEGUE EN RAILWAY

- [ ] Conectar repositorio a Railway

- [ ] Crear servicio MySQL en Railway

- [ ] Configurar variables de entorno en Railway

- [ ] Ejecutar database.sql en MySQL de Railway

- [ ] Esperar a que Railway termine el deploy

- [ ] Abrir https://tu-app.railway.app/login.php

- [ ] Probar login con credenciales

- [ ] Si hay error, revisar logs en Railway:
  ```bash
  railway logs
  ```

- [ ] Cambiar credenciales de prueba en BD de producción

---

## 📊 POST-CONFIGURACIÓN

- [ ] Crear usuario admin adicional con tus datos
  - [ ] Nombre: Tu nombre
  - [ ] Email: Tu email
  - [ ] Contraseña: Contraseña fuerte

- [ ] Crear usuario de prueba para usuarios
  - [ ] Nombre: Usuario Test
  - [ ] Email: test@pesado.com

- [ ] Documentar credenciales en lugar seguro
  - NO guardar en el código
  - NO compartir por email sin encriptar

- [ ] Actualizar README.md con info específica de tu proyecto

- [ ] Agregar archivo de contacto/soporte

---

## 🆘 TROUBLESHOOTING

**Si no funciona el login:**
- [ ] Verifica que MySQL está corriendo
- [ ] Verifica que database.sql se ejecutó
- [ ] Comprueba .env tiene credenciales correctas
- [ ] Revisa logs del servidor PHP

**Si no aparece el saludo:**
- [ ] Verifica que SESSION se inicia en config.php
- [ ] Comprueba que $_SESSION['nombre'] existe
- [ ] Revisa que landing.php incluye config.php

**Si dashboard no muestra datos:**
- [ ] Verifica que conexión a BD funciona
- [ ] Ejecuta queries en PHPMyAdmin manualmente
- [ ] Revisa logs de PHP

**Si no puedo desplegar en Railway:**
- [ ] Verifica que railway.json es válido
- [ ] Comprueba que Procfile existe
- [ ] Revisa credenciales de BD de Railway
- [ ] Ve a los logs de Railway para más info

---

## ✨ ¡LISTO!

Una vez hayas completado todos estos checkpoints, tu aplicación está:
✅ Instalada localmente
✅ Funcionando correctamente
✅ Lista para desplegar en Railway
✅ Segura contra vulnerabilidades comunes
✅ Personalizada para tu negocio

**Próximo paso:** Desplegar en Railway siguiendo RAILWAY_DEPLOYMENT.md

---

Última actualización: 2024
