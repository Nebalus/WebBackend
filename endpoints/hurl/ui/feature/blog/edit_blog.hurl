# Edit own blog post
# Requires blog ID and updated blog data

PUT {{BASE_URL}}/ui/users/self/services/blogs/{{BLOG_ID}}
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "title": "Updated Blog Title",
  "content": "Updated content",
  "public": true
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
