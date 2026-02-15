# Get all owned blogs
# Returns list of blogs owned by authenticated user

GET {{BASE_URL}}/ui/users/self/services/blogs/all
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" isCollection
