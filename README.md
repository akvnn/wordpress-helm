# WordPress Migration Helm Chart

A Helm chart designed specifically for migrating WPEngine WordPress websites to Kubernetes, with proper persistence handling and environment configuration.

## Overview

This Helm chart differs from standard WordPress charts by being **migration-focused** rather than deployment-focused. It's built to handle the complete migration of existing WPEngine WordPress sites to Kubernetes, including:

- Pre-built Docker images with custom themes and configurations
- Persistent volume claims for WordPress content directories
- Environment-based configuration for seamless transitions
- Database import capabilities for existing WordPress data

## Why This Chart?

Most WordPress Helm charts are designed for fresh installations. This chart is purpose-built for **migrating existing WordPress sites** from WPEngine to Kubernetes with:

- Support for custom `wp-content` directories stored in Git
- Separation of stateful content (plugins, uploads, logs, languages) into distinct persistent volumes
- Easy environment configuration through ConfigMaps
- Docker Compose setup for local development and testing before migration

## Prerequisites

- Kubernetes 1.19+
- Helm 3.0+
- Persistent Volume provisioner support in the underlying infrastructure
- Docker and Docker Compose (for local development)

## Repository Structure

```
.
├── charts/
│   └── wordpress-website/
│       ├── Chart.yaml
│       ├── values.yaml
│       ├── templates/
│       │   ├── deployment.yaml
│       │   ├── service.yaml
│       │   ├── configmap.yaml
│       │   └── ...
│       └── README.md
└── example-website/
    ├── Dockerfile
    ├── docker-compose.yaml
    ├── wp-content/
    │   ├── themes/
    │   ├── mysql.sql
    │   └── ...
    ├── wp-config.php
    ├── .env.db
    └── .env.wordpress
```

## Local Development with Docker Compose

Before deploying to Kubernetes, test your WordPress site locally:

1. **Prepare your WordPress content:**
   ```bash
   cd example-website
   # Add your custom themes to wp-content/themes/
   # Add your database dump to wp-content/mysql.sql 
   ```

2. **Configure environment variables:**
   
   Create `.env.db`:
   ```env
   MYSQL_ROOT_PASSWORD=your_root_password
   MYSQL_DATABASE=wp_db
   MYSQL_USER=wp_user
   MYSQL_PASSWORD=your_password
   ```
   
   Create `.env.wordpress`:
   ```env
   WORDPRESS_DB_HOST=db:3306
   WORDPRESS_DB_NAME=wp_db
   WORDPRESS_DB_USER=wp_user
   WORDPRESS_DB_PASSWORD=your_password
   WP_HOME=http://localhost:8080
   WP_SITEURL=http://localhost:8080
   AUTH_KEY=your_auth_key
   SECURE_AUTH_KEY=your_secure_auth_key
   LOGGED_IN_KEY=your_logged_in_key
   NONCE_KEY=your_nonce_key
   AUTH_SALT=your_auth_salt
   SECURE_AUTH_SALT=your_secure_auth_salt
   LOGGED_IN_SALT=your_logged_in_salt
   NONCE_SALT=your_nonce_salt
   ```

3. **Start the local environment:**
   ```bash
   docker-compose up -d
   ```

4. **Access your site:**
   - WordPress: http://localhost:8080
   - MySQL: localhost:3306

The database will automatically import from `mysql.sql` on first startup.

## Building Your WordPress Image

Your custom WordPress image includes:

- WordPress 6.8.2 with PHP 8.3 and Apache
- Custom themes from your Git repository
- Custom PHP settings optimized for WordPress
- Proper permissions for `wp-content`

**Build the image:**
```bash
cd example-website
docker build -t your-registry/wordpress-site:latest .
docker push your-registry/wordpress-site:latest
```

## Configuration

### Environment Variables

Configure WordPress through the `configMapEnv` section in `values.yaml`:

```yaml
configMapEnv:
  WORDPRESS_DB_NAME: wp_db
  WORDPRESS_DB_USER: wp_user
  WORDPRESS_DB_HOST: postgres  # or mysql service name
  WP_HOME: https://your-domain.com
  WP_SITEURL: https://your-domain.com
  WORDPRESS_DB_PASSWORD: your-password
  AUTH_KEY: generate-unique-key
  SECURE_AUTH_KEY: generate-unique-key
  LOGGED_IN_KEY: generate-unique-key
  NONCE_KEY: generate-unique-key
  AUTH_SALT: generate-unique-salt
  SECURE_AUTH_SALT: generate-unique-salt
  LOGGED_IN_SALT: generate-unique-salt
  NONCE_SALT: generate-unique-salt
```

> **Security Note:** Generate unique keys and salts using the [WordPress Secret Key Generator](https://api.wordpress.org/secret-key/1.1/salt/)

### Persistent Volumes

The chart creates separate PVCs for different WordPress content directories:

```yaml
volumes:
  - name: languages-volume
    persistentVolumeClaim:
      claimName: your-site-languages-volume-claim
  - name: logs-volume
    persistentVolumeClaim:
      claimName: your-site-logs-volume-claim
  - name: plugins-volume
    persistentVolumeClaim:
      claimName: your-site-plugins-volume-claim
  - name: uploads-volume
    persistentVolumeClaim:
      claimName: your-site-uploads-volume-claim
```

Corresponding mount paths:
```yaml
volumeMounts:
  - name: languages-volume
    mountPath: /var/www/html/wp-content/languages
  - name: logs-volume
    mountPath: /var/www/html/wp-content/logs
  - name: plugins-volume
    mountPath: /var/www/html/wp-content/plugins
  - name: uploads-volume
    mountPath: /var/www/html/wp-content/uploads
```

## Migration Steps

### 1. Export from WPEngine

- Export your WordPress database (SQL dump)
- Download your `wp-content` directory (especially `uploads/`, `plugins/`, and custom content)
- Note your WordPress configuration (database credentials, salts, etc.)

### 2. Prepare Your Repository

```bash
# Add themes to version control
cp -r /path/to/wpengine/wp-content/themes ./example-website/wp-content/

# Add database dump for local testing
cp /path/to/database-dump.sql ./example-website/wp-content/mysql.sql

# Commit to Git
git add example-website/wp-content/themes
git commit -m "Add WordPress themes for migration"
```

### 3. Test Locally

Run Docker Compose to verify everything works:
```bash
cd example-website
docker-compose up
```

### 4. Prepare Kubernetes Data

Before deploying, populate your persistent volumes with existing content:

```bash
# Create a temporary pod to load data
kubectl run -it --rm data-loader --image=busybox -- sh

# Then copy your plugins, uploads, etc. to the PVCs
kubectl cp plugins/ data-loader:/mnt/plugins/
kubectl cp uploads/ data-loader:/mnt/uploads/
```

Or use an init container or Job to handle data loading.

### 5. Deploy to Kubernetes

```bash
helm repo add akvnn https://akvnn.github.io/wordpress-helm
helm repo update
helm install wordpress-website akvnn/wordpress-website
```

Make sure to modify the deployment (particularly the env vars) by overriding values in `values.yaml`:
```bash
helm install wordpress-website akvnn/wordpress-website \
  --set image.repository=your-registry/wordpress-site \
  --set image.tag=latest \
  --set configMapEnv.WORDPRESS_DB_NAME=wp_db \
  --set configMapEnv.WORDPRESS_DB_USER=wp_user \
  --set configMapEnv.WORDPRESS_DB_HOST=mysql \
  --set configMapEnv.WORDPRESS_DB_PASSWORD=your-secure-password \
  --set configMapEnv.WP_HOME=https://your-domain.com \
  --set configMapEnv.WP_SITEURL=https://your-domain.com \
  --set configMapEnv.AUTH_KEY=your-auth-key \
  --set configMapEnv.SECURE_AUTH_KEY=your-secure-auth-key \
  --set configMapEnv.LOGGED_IN_KEY=your-logged-in-key \
  --set configMapEnv.NONCE_KEY=your-nonce-key \
  --set configMapEnv.AUTH_SALT=your-auth-salt \
  --set configMapEnv.SECURE_AUTH_SALT=your-secure-auth-salt \
  --set configMapEnv.LOGGED_IN_SALT=your-logged-in-salt \
  --set configMapEnv.NONCE_SALT=your-nonce-salt
```

### 6. Import Database

Connect to your database service and import your SQL dump:
```bash
kubectl exec -it mysql-pod -- mysql -u wp_user -p wp_db < database-dump.sql
```

## PHP Configuration

The Docker image includes optimized PHP settings for WordPress:

- `upload_max_filesize`: 64M
- `post_max_size`: 64M
- `max_execution_time`: 300s
- `memory_limit`: 256M
- `max_input_vars`: 3000

Adjust these in the Dockerfile if needed for your site's requirements.

## Key Differences from Standard WordPress Charts

| Feature | This Chart | Standard Charts |
|---------|------------|-----------------|
| **Purpose** | Migration from WPEngine | Fresh installation |
| **Content Source** | Pre-built Docker image with Git-tracked themes | Bitnami images or empty volumes |
| **Volume Strategy** | Separate PVCs for each content type | Single volume or no persistence |
| **Database** | Import existing data | Create new database |
| **Configuration** | Environment-based (WPEngine-like) | WordPress installer |

## Troubleshooting

### Database Connection Issues

Check that `WORDPRESS_DB_HOST` matches your database service name:
```bash
kubectl get svc
```

### Permission Errors

Ensure volumes have correct ownership:
```bash
kubectl exec -it wordpress-pod -- chown -R www-data:www-data /var/www/html/wp-content
```

### Site URL Mismatch

Update `WP_HOME` and `WP_SITEURL` in your ConfigMap and restart:
```bash
helm upgrade my-wordpress-site . -f values.yaml
kubectl rollout restart deployment/wordpress
```

## Support

For issues or questions about migrating your WPEngine WordPress site to Kubernetes, please open an issue in this repository.

## License

MIT