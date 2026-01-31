# Get all blog posts (moderation)
# Admin/moderator endpoint - shows all users' blogs

GET {{BASE_URL}}/ui/blogs/all/moderation
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
