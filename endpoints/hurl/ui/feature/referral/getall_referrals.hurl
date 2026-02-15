# Get all own referrals
# Lists all referral links for authenticated user

GET {{BASE_URL}}/ui/users/self/services/referrals/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
