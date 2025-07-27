#!/bin/sh

# Rodar as migrations
echo "Rodando migrations..."
php artisan migrate --force

# Iniciar o servidor Laravel
echo "Iniciando o servidor..."
php artisan serve --host=0.0.0.0 --port=8080
