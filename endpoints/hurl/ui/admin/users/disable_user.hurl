# Disable user
# Admin endpoint - toggles a user's disabled state

PUT {{BASE_URL}}/ui/admin/users/self/disable
Authorization: Bearer {{AUTH_JWT}}
Content-Type: application/json

{
    "disabled": true,
    "reason": "Test disable"
}

HTTP 200
[Asserts]
jsonpath "$.success" == true
jsonpath "$.payload.disabled" == true
jsonpath "$.payload.disabled_reason" == "Test disable"
