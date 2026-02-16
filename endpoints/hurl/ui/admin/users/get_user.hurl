# Get user by ID
# Admin endpoint - returns a single user's details

GET {{BASE_URL}}/ui/admin/users/self
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.success" == true
jsonpath "$.payload.user_id" isInteger
jsonpath "$.payload.username" isString
jsonpath "$.payload.email" isString
