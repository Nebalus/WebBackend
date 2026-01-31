# Create new role
# Admin endpoint

POST {{BASE_URL}}/ui/admin/roles
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "access_level": 113,
  "applies_to_everyone": false,
  "color": "0243ff",
  "disabled": true,
  "description": "sdf",
  "name": "sdss"
}

HTTP 201
[Asserts]
jsonpath "$.payload.id" exists
