# Get specific own blog post
# Requires blog ID parameter

GET {{BASE_URL}}/ui/users/self/services/blogs/{{BLOG_ID}}
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload.blog_id" exists
jsonpath "$.payload.title" exists
