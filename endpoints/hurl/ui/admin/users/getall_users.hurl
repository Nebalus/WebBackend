# Get all users
# Admin endpoint - returns a list of all users

GET {{BASE_URL}}/ui/admin/users/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
