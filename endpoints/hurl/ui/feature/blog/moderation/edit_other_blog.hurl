# Edit someone else's blog post (moderation)
# Admin/moderator endpoint

PUT {{BASE_URL}}/ui/blogs/{{BLOG_ID}}/moderation
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "title": "Moderated Title",
  "content": "Moderated content",
  "public": false
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
