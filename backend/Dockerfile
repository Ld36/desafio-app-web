FROM php:8.4-cli

# Instala as dependências do sistema e a extensão do PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql

# Copia o Composer para dentro do container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /app

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-interaction --optimize-autoloader

# Expõe a porta que o artisan serve vai usar
EXPOSE 8000

# Script para iniciar o servidor com migrations
RUN echo '#!/bin/bash\n\
echo "Aguardando banco de dados estar pronto..."\n\
for i in {1..30}; do\n\
    if php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then\n\
        echo "Banco de dados conectado!"\n\
        break\n\
    fi\n\
    echo "Tentativa $i/30 - aguardando..."\n\
    sleep 2\n\
done\n\
\n\
echo "Rodando migrations..."\n\
php artisan migrate --force\n\
\n\
echo "Rodando seeds..."\n\
php artisan db:seed --force\n\
\n\
echo "Iniciando servidor Laravel..."\n\
php artisan serve --host=0.0.0.0 --port=8000' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]