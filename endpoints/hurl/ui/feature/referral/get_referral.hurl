# Get own referral
# Retrieves a specific referral by code

GET {{BASE_URL}}/ui/users/self/services/referrals/{{REFERRAL_CODE}}
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
jsonpath "$.payload.code" exists
