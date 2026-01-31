# Delete own referral
# Deletes a referral link by code

DELETE {{BASE_URL}}/ui/users/self/services/referrals/{{REFERRAL_CODE}}
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
