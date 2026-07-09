#!/bin/bash
# ============================================
# Script de configuración inicial rápida
# ============================================
# Este script automatiza los primeros pasos
# de configuración (solo para Linux/Mac)

echo "🚀 Iniciando configuración de Pesado y al Fallo..."

# Verificar si estamos en el directorio correcto
if [ ! -f "database.sql" ]; then
    echo "❌ Error: Ejecuta este script desde la raíz del proyecto"
    exit 1
fi

# Verificar PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP no está instalado"
    exit 1
fi

# Verificar MySQL
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL no está instalado"
    exit 1
fi

echo "✓ PHP y MySQL detectados"

# Copiar .env.example a .env
if [ ! -f ".env" ]; then
    echo "📋 Creando .env..."
    cp .env.example .env
    echo "✓ .env creado (edita con tus credenciales)"
else
    echo "✓ .env ya existe"
fi

# Crear base de datos
read -p "¿Ejecutar database.sql en MySQL? (s/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    read -p "Usuario MySQL [root]: " db_user
    db_user=${db_user:-root}
    
    mysql -u "$db_user" < database.sql
    if [ $? -eq 0 ]; then
        echo "✓ Base de datos creada exitosamente"
    else
        echo "❌ Error al crear base de datos"
        exit 1
    fi
fi

# Iniciar servidor
read -p "¿Iniciar servidor PHP en localhost:8000? (s/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "🚀 Iniciando servidor..."
    echo "Abre: http://localhost:8000/login.php"
    php -S localhost:8000
fi

echo "✓ Configuración completada!"
