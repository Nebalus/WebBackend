# Delete user
# Admin endpoint - hard deletes a user
# WARNING: This will permanently delete the user, use with caution in tests

DELETE {{BASE_URL}}/ui/admin/users/2
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.success" == true
jsonpath "$.message" == "User Deleted"
