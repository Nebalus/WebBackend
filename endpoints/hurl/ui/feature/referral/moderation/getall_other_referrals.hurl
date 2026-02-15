# Get all referrals (moderation)
# Admin/moderator endpoint - lists all users' referrals

GET {{BASE_URL}}/ui/services/referrals/all/moderation
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
