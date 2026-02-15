# Get someone else's blog post (moderation)
# Admin/moderator endpoint

GET {{BASE_URL}}/ui/blogs/{{BLOG_ID}}/moderation
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
