# Get referral click history
# Retrieves click statistics for a referral link

GET {{BASE_URL}}/ui/users/self/services/referrals/{{REFERRAL_CODE}}/click_history?range=30
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
