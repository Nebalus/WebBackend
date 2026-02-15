# Get someone else's referral (moderation)
# Admin/moderator endpoint

GET {{BASE_URL}}/ui/users/{{USER_ID}}/services/referrals/{{REFERRAL_CODE}}
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
