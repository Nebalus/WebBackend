# Create referral link
# Creates a new referral/shortened URL

POST {{BASE_URL}}/ui/users/self/services/referrals
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json
{
  "label": "TEST",
  "url": "https://example.com"
}

HTTP 201
[Asserts]
jsonpath "$.payload.id" exists
jsonpath "$.payload.code" exists
