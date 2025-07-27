#!/bin/bash

# Espera o MySQL subir
echo "Aguardando o MySQL iniciar..."
until nc -z db 3306; do
  sleep 1
done

echo "MySQL conectado com sucesso."

# Dá permissões nas pastas necessárias
chmod -R 775 storage bootstrap/cache

# Gera a chave da aplicação, se não existir
if [ ! -f ".env" ]; then
  cp .env.example .env
fi

if [ ! -s storage/oauth-private.key ]; then
  php artisan key:generate
fi

# Executa as migrations
php artisan migrate --force

# (opcional) Caching configs
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Inicia o servidor
php artisan serve --host=0.0.0.0 --port=8000
