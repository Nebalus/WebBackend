# Edit someone else's linktree (moderation)
# Admin/moderator endpoint

PUT {{BASE_URL}}/ui/users/{{USER_ID}}/linktree/moderation
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "title": "Moderated Links",
  "bio": "Updated bio",
  "links": []
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
