#!/bin/bash
set -e

echo "==> Generando .env desde variables de entorno..."

cat > .env << EOF
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = '${APP_BASE_URL:-https://ecommerce-production-904d.up.railway.app/}'
app.indexPage = ''
app.appTimezone = 'America/Bogota'

#--------------------------------------------------------------------
# DATABASE
# Railway MySQL plugin provee: MYSQLHOST, MYSQLUSER, MYSQLPASSWORD,
# MYSQLDATABASE, MYSQLPORT automaticamente.
#--------------------------------------------------------------------
database.default.hostname = ${MYSQLHOST:-tramway.proxy.rlwy.net}
database.default.database = ${MYSQLDATABASE:-ecommerce_db}
database.default.username = ${MYSQLUSER:-root}
database.default.password = ${MYSQLPASSWORD:-BlHXvVpqdOhZIWGkGbMEQtrPcHNcYJUP }
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = ${MYSQLPORT:-45533}
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------
encryption.key = ${ENCRYPTION_KEY:-hex2bin:0000000000000000000000000000000000000000000000000000000000000000}

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
session.driver = 'CodeIgniter\Session\Handlers\DatabaseHandler'
session.savePath = 'ci_sessions'

#--------------------------------------------------------------------
# LOGGER
#--------------------------------------------------------------------
logger.threshold = 4

#--------------------------------------------------------------------
# ECOMMERCE CONFIG
#--------------------------------------------------------------------
ecommerce.storeName = '${STORE_NAME:-Mi Tienda Online}'
ecommerce.currency = 'COP'
ecommerce.currencySymbol = '$'
ecommerce.taxRate = 19

#--------------------------------------------------------------------
# PAYMENT GATEWAYS
#--------------------------------------------------------------------
payu.merchantId = '${PAYU_MERCHANT_ID:-}'
payu.apiKey = '${PAYU_API_KEY:-}'
payu.apiLogin = '${PAYU_API_LOGIN:-}'
payu.accountId = '${PAYU_ACCOUNT_ID:-}'
payu.sandbox = true
payu.testMode = true

mercadopago.publicKey = '${MP_PUBLIC_KEY:-}'
mercadopago.accessToken = '${MP_ACCESS_TOKEN:-}'
mercadopago.sandbox = true
EOF

DB_HOST="${MYSQLHOST:-tramway.proxy.rlwy.net}"
DB_PORT="${MYSQLPORT:-45533}"
DB_USER="${MYSQLUSER:-root}"
DB_PASS="${MYSQLPASSWORD:-BlHXvVpqdOhZIWGkGbMEQtrPcHNcYJUP}"
DB_NAME="${MYSQLDATABASE:-ecommerce_db}"

echo "==> Esperando que la base de datos este disponible en ${DB_HOST}:${DB_PORT}..."
MAX_RETRIES=20
RETRIES=0
until php -d error_reporting=0 -r "
  \$c = @new mysqli('${DB_HOST}', '${DB_USER}', '${DB_PASS}', '${DB_NAME}', ${DB_PORT});
  exit(\$c->connect_error ? 1 : 0);
" 2>/dev/null; do
  RETRIES=$((RETRIES + 1))
  if [ "$RETRIES" -ge "$MAX_RETRIES" ]; then
    echo "   ERROR: No se pudo conectar a la BD tras ${MAX_RETRIES} intentos. Abortando."
    exit 1
  fi
  echo "   BD no disponible todavia (intento ${RETRIES}/${MAX_RETRIES}), reintentando en 3s..."
  sleep 3
done
echo "   BD disponible."

echo "==> Ejecutando migraciones..."
php spark migrate --all

if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "==> Poblando datos de prueba (seeds)..."
  php spark db:seed DatabaseSeeder
  echo "   Seeds ejecutados."
fi

echo "==> Iniciando servidor PHP en puerto ${PORT:-8080}..."
exec php -S "0.0.0.0:${PORT:-8080}" -t public/ public/index.php
