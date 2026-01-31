# Authenticate user
# Returns JWT token for authenticated requests

POST {{BASE_URL}}/ui/auth
Content-Type: application/json
{
  "username": "{{USERNAME}}",
  "password": "{{PASSWORD}}"
}

HTTP 200
[Asserts]
jsonpath "$.payload.jwt" exists
[Captures]
AUTH_JWT: jsonpath "$.payload.jwt"
