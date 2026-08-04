#!/bin/bash
cat > /var/www/html/.env << ENVEOF
CI_ENVIRONMENT=${CI_ENVIRONMENT}
app.baseURL='${APP_BASEURL}'
database.default.hostname=${DB_HOSTNAME}
database.default.database=${DB_DATABASE}
database.default.username=${DB_USERNAME}
database.default.password=${DB_PASSWORD}
database.default.DBDriver=MySQLi
database.default.port=${DB_PORT}
GEMINI_API_KEY=${GEMINI_API_KEY}
ENVEOF
exec php -S 0.0.0.0:8080 -t public