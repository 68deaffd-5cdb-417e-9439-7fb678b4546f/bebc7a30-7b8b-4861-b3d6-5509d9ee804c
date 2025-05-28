#!/bin/bash

KEYCLOAK_URL="http://keycloak:8080/realms/demo_realm/protocol/openid-connect/certs"

composer install

until php artisan migrate --force; do
  echo "Waiting for the database to be ready for migrations..."
  sleep 5
done

# Retry fetching the Keycloak public key until successful
until php artisan keycloak:fetch-public-key "$KEYCLOAK_URL"; do
  echo "Waiting for Keycloak to be ready..."
  sleep 5
done

socat TCP-LISTEN:8080,fork TCP:keycloak:8080 & php artisan serve --host=0.0.0.0 --port=8000

