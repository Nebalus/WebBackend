# Edit/update role by ID
# Admin endpoint

PUT {{BASE_URL}}/ui/admin/roles/10
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "name": "TEST",
  "description": "null",
  "color": "ffffff",
  "access_level": 24123,
  "applies_to_everyone": false,
  "disabled": false
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
