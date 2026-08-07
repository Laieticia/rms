#!/bin/bash
set -e

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

log "🚀 Démarrage de l'application Multishop..."
log "📋 Environnement: ${APP_ENV:-production}"

# Render fournit PORT dynamiquement (10000 par défaut). En local (docker-compose)
# on retombe sur 80.
export PORT="${PORT:-80}"

# Si Render fournit l'URL publique, on l'utilise pour générer les assets HTTPS.
if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL}}"
    export ASSET_URL="${ASSET_URL:-${RENDER_EXTERNAL_URL}}"
fi

log "🌐 Le service écoutera sur le port ${PORT}"
log "🔗 URL applicative: ${APP_URL:-http://localhost}"

# Créer les répertoires nécessaires
mkdir -p /var/run/php /var/log/php /var/run/nginx /var/log/nginx /var/lib/nginx/tmp /var/log/supervisor
chown -R www-data:www-data /var/run/php /var/log/php /var/run/nginx /var/log/nginx /var/lib/nginx/tmp

mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/uploads bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Générer le vhost Nginx avec le port injecté par Render.
# IMPORTANT: on ne substitue QUE $PORT, sinon envsubst casse les variables
# nginx natives ($uri, $document_root, etc.) présentes dans le template.
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

# Générer APP_KEY si absente
if [ -z "$APP_KEY" ]; then
    log "🔑 Génération de APP_KEY..."
    php artisan key:generate --force || true
fi

# Migrations optionnelles (mets RUN_MIGRATIONS=true dans les env vars Render)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    log "📊 Exécution des migrations..."
    php artisan migrate --force || log "⚠️ Erreur lors des migrations, on continue quand même"
fi

log "🧹 Cache de configuration Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Vérifs de config avant de tout lancer
php-fpm -t
nginx -t

log "✅ Configuration validée, lancement de supervisord (php-fpm + nginx + queue)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf