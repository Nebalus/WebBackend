# Get user permissions
# Shows aggregation of all permissions from user's roles
# Requires authentication

GET {{BASE_URL}}/ui/users/self/permissions
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
