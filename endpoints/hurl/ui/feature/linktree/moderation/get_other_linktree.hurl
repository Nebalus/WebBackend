# Get someone else's linktree (moderation)
# Admin/moderator endpoint

GET {{BASE_URL}}/ui/users/{{USER_ID}}/linktree/moderation
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
