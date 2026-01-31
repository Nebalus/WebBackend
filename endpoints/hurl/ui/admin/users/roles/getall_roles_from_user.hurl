# Get all roles from user
# Admin endpoint - returns all roles assigned to a user

GET {{BASE_URL}}/ui/admin/users/self/roles/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
