#!/bin/bash

API_URL="http://localhost:8001/api/v1"

echo "=== Регистрация пользователя ==="

REGISTER_RESPONSE=$(curl -s -X POST "$API_URL/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
        "name":"Ferhat",
        "email":"ferhat@example.com",
        "password":"password123",
        "password_confirmation":"password123",
        "phone":"+45645758",
        "role":"passenger"
      }')

echo "$REGISTER_RESPONSE"
echo

echo "=== Логин пользователя ==="

LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
        "email":"ferhat@example.com",
        "password":"password123"
      }')

# Извлекаем токен без jq
TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

echo "Токен: $TOKEN"
echo

echo "=== Получение доступных поездок (Trips) ==="
curl -s -X GET "$API_URL/trips/available" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
echo
echo

# Если пользователь водитель, пример создания машины
echo "=== Добавление машины (Cars) ==="
curl -s -X POST "$API_URL/cars" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
        "model":"Toyota Corolla",
        "plate":"34ABC123",
        "color":"Red"
      }'
echo
