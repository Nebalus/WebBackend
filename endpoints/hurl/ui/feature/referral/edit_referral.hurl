# Edit own referral
# Updates a referral link

PUT {{BASE_URL}}/ui/users/self/services/referrals/{{REFERRAL_CODE}}
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "url": "https://test.de",
  "label": "test"
}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
