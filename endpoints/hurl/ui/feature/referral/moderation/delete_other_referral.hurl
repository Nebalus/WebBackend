# Delete someone else's referral (moderation)
# Admin/moderator endpoint

DELETE {{BASE_URL}}/ui/users/{{USER_ID}}/services/referrals/{{REFERRAL_CODE}}
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
