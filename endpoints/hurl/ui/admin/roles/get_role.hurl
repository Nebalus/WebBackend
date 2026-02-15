# Get specific role by ID
# Admin endpoint

GET {{BASE_URL}}/ui/admin/roles/6
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
