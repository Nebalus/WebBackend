# Create blog post
# Example POST request with JSON body

POST {{BASE_URL}}/ui/users/self/services/blogs
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "slug": "my_test_blog_post",
  "title": "My Test Blog Post",
  "content": "This is the full content of my blog post with detailed information about the topic.",
  "excerpt": "A short summary of the blog post",
  "status": "DRAFT"
}

HTTP 201
[Asserts]
jsonpath "$.payload.blog_id" exists
jsonpath "$.payload.title" == "My Test Blog Post"
