# Example Website — WordPress (Helm)

Lightweight example demonstrating how to deploy a WordPress website with the provided Helm chart in this repository. Use this README to quickly install, configure, access, and remove the example site.

## Install (locally for testing)
1. Create required `.env.db` and `.env.wordpress`:

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
   
2. Install the image using docker compose:
```bash
docker compose up --build -d
```