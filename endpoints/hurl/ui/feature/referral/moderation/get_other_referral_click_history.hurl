# Get someone else's referral click history (moderation)
# Admin/moderator endpoint

GET {{BASE_URL}}/ui/users/{{USER_ID}}/services/referrals/{{REFERRAL_CODE}}/click_history?range=30
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
