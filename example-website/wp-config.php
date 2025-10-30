<?php
# Database Configuration
define( 'DB_NAME', $_ENV['WORDPRESS_DB_NAME'] ?: getenv('WORDPRESS_DB_NAME') );
define( 'DB_USER', $_ENV['WORDPRESS_DB_USER'] ?: getenv('WORDPRESS_DB_USER') );
define( 'DB_PASSWORD', $_ENV['WORDPRESS_DB_PASSWORD'] ?: getenv('WORDPRESS_DB_PASSWORD') );
define( 'DB_HOST', $_ENV['WORDPRESS_DB_HOST'] ?: getenv('WORDPRESS_DB_HOST') );
define( 'DB_HOST_SLAVE', $_ENV['WORDPRESS_DB_HOST'] ?: getenv('WORDPRESS_DB_HOST') );

define('WP_HOME', $_ENV['WP_HOME'] ?: getenv('WP_HOME'));
define('WP_SITEURL', $_ENV['WP_SITEURL'] ?: getenv('WP_SITEURL'));

// define('RELOCATE', true);

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');

$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define( 'AUTH_KEY', $_ENV['AUTH_KEY'] ?: getenv('AUTH_KEY') );
define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] ?: getenv('SECURE_AUTH_KEY') );
define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] ?: getenv('LOGGED_IN_KEY') );
define( 'NONCE_KEY', $_ENV['NONCE_KEY'] ?: getenv('NONCE_KEY') );
define( 'AUTH_SALT', $_ENV['AUTH_SALT'] ?: getenv('AUTH_SALT') );
define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] ?: getenv('SECURE_AUTH_SALT') );
define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] ?: getenv('LOGGED_IN_SALT') );
define( 'NONCE_SALT', $_ENV['NONCE_SALT'] ?: getenv('NONCE_SALT') );

ini_set('display_errors', 0);
//ini_set('error_reporting', E_ALL );
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', 'wp-content/logs/debug.log');
define('WP_DEBUG_DISPLAY', false);

# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );


define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '1024M');

# That's It. Pencils down
if (!defined('ABSPATH'))
  define('ABSPATH', __DIR__ . '/');

// Fix for HTTPS detection behind reverse proxy (Docker, Nginx, etc.)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

require_once(ABSPATH . 'wp-settings.php');
