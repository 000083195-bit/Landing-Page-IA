# Guía de Despliegue en Railway

## Paso 1: Preparar tu repositorio

### 1.1 Asegúrate que tienes en tu repo:
- database.sql (script de BD)
- config.php
- db.php
- auth.php
- login.php
- dashboard.php
- landing.php
- index.php
- styles.css
- .env.example
- composer.json
- railway.json
- Procfile

### 1.2 Actualizar .env.example
```
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=pesado_fallo
DB_PORT=3306
ENVIRONMENT=development
APP_URL=http://localhost
```

## Paso 2: Crear proyecto en Railway

1. Ve a https://railway.app
2. Haz login o crea cuenta
3. Crea un nuevo proyecto
4. Conecta tu repositorio de GitHub

## Paso 3: Agregar servicio de MySQL

1. En tu proyecto de Railway, haz clic en "+ New"
2. Busca y selecciona "MySQL"
3. Railway creará la base de datos automáticamente

## Paso 4: Obtener credenciales de Railway

1. Haz clic en el servicio MySQL
2. Ve a la pestaña "Connect"
3. Copia las credenciales:
   - Host
   - Username
   - Password
   - Port
   - Database

## Paso 5: Configurar variables de entorno

En tu proyecto de Railway:

1. Haz clic en el servicio PHP/tu aplicación
2. Ve a "Variables"
3. Agrega las siguientes variables:

```
ENVIRONMENT=production
APP_URL=https://tu-app-name.railway.app

DB_HOST=[host de Railway]
DB_USER=[usuario de Railway]
DB_PASS=[contraseña de Railway]
DB_PORT=[puerto de Railway]
DB_NAME=[nombre de la BD de Railway]
```

## Paso 6: Ejecutar migraciones

Para crear las tablas en tu BD de Railway:

### Opción 1: Usando phpmyadmin (más fácil)
1. En Railway, haz clic en MySQL
2. Ve a "Connect"
3. Usa la URL de phpmyadmin
4. Importa el archivo database.sql

### Opción 2: Usando CLI de Railway
```bash
# Instalar CLI de Railway
npm install -g @railway/cli

# Conectarse a Railway
railway login

# Conectarse a tu proyecto
railway link

# Conectar a la BD MySQL
railway connect mysql

# Pegar contenido de database.sql y ejecutar
```

## Paso 7: Deploy automático

Railway desplegará automáticamente cuando:
- Hagas push a tu rama principal
- O manualmente desde el dashboard

## Paso 8: Verificar que funciona

1. Ve a tu URL de Railway: https://tu-app-name.railway.app/login.php
2. Intenta login con:
   - Email: admin@pesado.com
   - Contraseña: password123

## Problemas comunes

### Error: "Error de conexión a la base de datos"
- Verifica que las variables de entorno en Railway coincidan con database.sql
- Asegúrate que el servicio MySQL está corriendo
- Revisa los logs en Railway

### Error: "Tablas no encontradas"
- Ejecuta el script database.sql en phpmyadmin
- Verifica que importó correctamente

### Error: "APP_URL incorrecto"
- Copia la URL exacta de tu aplicación desde Railway
- Asegúrate de agregar https://

## Actualizaciones futuras

Para actualizar tu aplicación:
1. Haz cambios en tu código
2. Commit y push a GitHub
3. Railway desplegará automáticamente

## Monitoreo

En el dashboard de Railway puedes:
- Ver logs en tiempo real
- Monitorear el uso de CPU y memoria
- Ver fallos de despliegue
- Reiniciar la aplicación

## Support

- Documentación Railway: https://docs.railway.app
- Foro Railway: https://railway.app/support

---
¡Tu aplicación está lista para producción! 🚀
