# Get all permissions for a specific role
# Admin endpoint

GET {{BASE_URL}}/ui/admin/roles/4/permissions/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
