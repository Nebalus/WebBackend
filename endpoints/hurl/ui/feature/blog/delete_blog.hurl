# Delete own blog post
# Requires blog ID parameter

DELETE {{BASE_URL}}/ui/users/self/services/blogs/{{BLOG_ID}}
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
