# Get own linktree
# Retrieves the authenticated user's linktree

GET {{BASE_URL}}/ui/users/self/linktree
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
