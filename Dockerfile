FROM php:8.1-cli

# Instalar extensión MySQL
RUN docker-php-ext-install mysqli

# Copiar archivos de la aplicación
COPY . /app

WORKDIR /app

# Exponer puerto
EXPOSE 8080

# Comando de inicio
CMD ["php", "-S", "0.0.0.0:8080"]
