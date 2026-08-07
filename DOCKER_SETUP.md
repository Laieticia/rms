# Docker Setup - Multishop 2026

## 📦 Fichiers créés

```
Dockerfile              # Image multi-stage pour production
docker-compose.yml      # Pour développement local avec MySQL
.dockerignore          # Fichiers à ignorer lors du build
RENDER_DEPLOYMENT.md   # Guide complet pour Render
docker/
  ├── php.ini          # Configuration PHP
  ├── www.conf         # Configuration PHP-FPM
  ├── nginx.conf       # Configuration Nginx
  ├── default.conf     # VirtualHost Nginx
  └── supervisord.conf # Gestionnaire de processus
```

## 🚀 Déploiement sur Render

### Étape 1: Préparer GitHub
```bash
# Commiter les nouveaux fichiers
git add Dockerfile docker/ .dockerignore RENDER_DEPLOYMENT.md
git commit -m "Add Docker configuration for Render deployment"
git push origin main
```

### Étape 2: Créer un service sur Render
1. Aller sur https://dashboard.render.com
2. Cliquer "New +" > "Web Service"
3. Connecter votre repository GitHub
4. Configuration:
   - **Name**: `multishop-app`
   - **Environment**: `Docker`
   - **Region**: Sélectionner le plus proche
   - **Plan**: Commencer avec "Free" (ou supérieur selon les besoins)

### Étape 3: Configuration des variables
Ajouter dans "Environment":
```
APP_KEY=             # Sera généré automatiquement
APP_ENV=production
APP_DEBUG=false
APP_URL=<votre-domaine>.onrender.com

DB_HOST=<mysql-host-externe>
DB_PORT=3306
DB_DATABASE=<nom-base>
DB_USERNAME=<user>
DB_PASSWORD=<password>
```

### Étape 4: Commands
- **Build Command**: `composer install --no-dev && npm install && npm run build`
- **Start Command**: `php artisan migrate --force && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf`

### Étape 5: Déployer
Cliquer "Create Web Service" et attendre le build et déploiement.

## 🧪 Test local avec Docker

### Avec docker-compose (inclus MySQL)
```bash
# Build et démarrer
docker-compose up -d

# Vérifier le statut
docker-compose ps

# Voir les logs
docker-compose logs -f app

# Arrêter
docker-compose down
```

### Avec Docker uniquement (BD externe)
```bash
# Build
docker build -t multishop:latest .

# Démarrer (en remplaçant les variables BD)
docker run -p 8000:80 \
  -e DB_HOST=your-db-host \
  -e DB_DATABASE=your-db \
  -e DB_USERNAME=your-user \
  -e DB_PASSWORD=your-password \
  -e APP_KEY=base64:... \
  multishop:latest
```

## 📋 Points importants

✅ **PHP 8.2** - Compatible avec Laravel 12
✅ **Nginx** - Configuration optimisée
✅ **PHP-FPM** - Haute performance
✅ **Supervisor** - Gère PHP-FPM, Nginx et Queue
✅ **Multi-stage build** - Image optimisée pour production
✅ **Tailwind & Vite** - Assets pré-compilés
✅ **Health check** - Montage de santé automatique
✅ **Queue worker** - Traitement des jobs en arrière-plan
✅ **Gzip enabled** - Compression automatique
✅ **Cache control** - Headers optimisés pour assets statiques

## 🔧 Configuration personnalisée

Modifier les fichiers dans `docker/` selon vos besoins:
- Augmenter `pm.max_children` pour plus de workers PHP
- Ajouter des extensions PHP dans Dockerfile
- Ajuster les timeouts Nginx si nécessaire

## 📞 Support Render

- Documentation: https://render.com/docs
- Support: https://render.com/support
- Status: https://status.render.com
