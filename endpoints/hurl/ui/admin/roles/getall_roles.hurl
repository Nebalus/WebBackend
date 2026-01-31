# Get all roles
# Admin endpoint - requires admin permissions

GET {{BASE_URL}}/ui/admin/roles/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
