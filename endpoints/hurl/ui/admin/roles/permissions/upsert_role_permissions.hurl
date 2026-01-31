# Upsert (create/update) role permissions
# Admin endpoint - updates permissions for a role

PUT {{BASE_URL}}/ui/admin/roles/4/permissions
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
[
  {
    "node": "feature",
    "allow_all_sub_permissions": true,
    "value": null
  }
]

HTTP 200
[Asserts]
jsonpath "$.payload" exists
