# Get all permissions
# Admin endpoint

GET {{BASE_URL}}/ui/admin/permissions/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
