# Edit someone else's referral (moderation)
# Admin/moderator endpoint

PUT {{BASE_URL}}/ui/users/{{USER_ID}}/services/referrals/{{REFERRAL_CODE}}
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "url": "https://test.de",
  "label": "test"
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
