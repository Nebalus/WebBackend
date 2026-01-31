# Create blog post
# Example POST request with JSON body

POST {{BASE_URL}}/ui/blogs
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "title": "My Blog Post",
  "content": "This is the content of my blog post",
  "public": true
}

HTTP 201
[Asserts]
jsonpath "$.payload.id" exists
jsonpath "$.payload.title" == "My Blog Post"
