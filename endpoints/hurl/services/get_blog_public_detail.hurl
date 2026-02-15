# Get Public Blog Detail Test
# 1. Create a blog (Draft)
# 2. Try to fetch publicly (Should 404)
# 3. Update to Published
# 4. Fetch publicly (Should 200)

# 1. Create Blog
POST {{BASE_URL}}/ui/users/self/services/blogs
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "slug": "public-test-blog",
  "title": "Public Test Blog",
  "content": "This is public content.",
  "excerpt": "Public excerpt",
  "status": "DRAFT"
}

HTTP 201
[Captures]
blog_id: jsonpath "$.payload.blog_id"
slug: jsonpath "$.payload.slug"
[Asserts]
jsonpath "$.payload.blog_id" exists
jsonpath "$.payload.slug" == "public-test-blog"

# 2. Fetch Publicly (Draft)
GET {{BASE_URL}}/services/blogs/{{slug}}

HTTP 404

# 3. Update to Published
PUT {{BASE_URL}}/ui/users/self/services/blogs/{{blog_id}}
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "slug": "public-test-blog",
  "title": "Public Test Blog",
  "content": "This is public content.",
  "excerpt": "Public excerpt",
  "status": "PUBLISHED",
  "is_featured": false
}

HTTP 200

# 4. Fetch Publicly (Published)
GET {{BASE_URL}}/services/blogs/{{slug}}

HTTP 200
[Asserts]
jsonpath "$.payload.blog_id" == "{{blog_id}}"
jsonpath "$.payload.title" == "Public Test Blog"
jsonpath "$.payload.content" == "This is public content."
jsonpath "$.payload.status" == "PUBLISHED"
