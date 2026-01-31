# Register new user
# Public endpoint - requires invitation token

POST {{BASE_URL}}/ui/register
Content-Type: application/json
{
  "invitation_token": "0000-0000-0000-0000-0000"
}

HTTP 201
[Asserts]
jsonpath "$.payload" exists
