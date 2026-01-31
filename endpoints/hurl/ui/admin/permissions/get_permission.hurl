# Get specific permission by ID
# Admin endpoint

GET {{BASE_URL}}/ui/admin/permissions/4
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
