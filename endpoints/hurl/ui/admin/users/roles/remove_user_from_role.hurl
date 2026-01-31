# Remove user from role
# Admin endpoint - unassigns a role from a user

DELETE {{BASE_URL}}/ui/admin/users/self/roles/3
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
