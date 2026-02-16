# Edit user
# Admin endpoint - updates a user's username, email, and email_verified

PUT {{BASE_URL}}/ui/admin/users/self
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json

{
    "username": "testadmin",
    "email": "admin@nebalus.dev",
    "email_verified": true
}

HTTP 200
[Asserts]
jsonpath "$.success" == true
jsonpath "$.payload.username" isString
jsonpath "$.payload.email" isString
