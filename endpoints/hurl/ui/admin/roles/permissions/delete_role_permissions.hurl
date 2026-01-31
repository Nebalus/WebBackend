# Delete role permissions
# Admin endpoint - removes permissions from a role

DELETE {{BASE_URL}}/ui/admin/roles/4/permissions
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
[
  "feature"
]

HTTP 200
