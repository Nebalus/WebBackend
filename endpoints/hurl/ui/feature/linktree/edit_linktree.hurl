# Edit own linktree
# Updates the authenticated user's linktree configuration

PUT {{BASE_URL}}/ui/users/self/linktree
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "title": "My Links",
  "bio": "Check out my links",
  "links": [
    {
      "title": "Website",
      "url": "https://example.com"
    }
  ]
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
