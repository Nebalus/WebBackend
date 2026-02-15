# Get file upload statistics
# Returns stats for uploaded files

GET {{BASE_URL}}/ui/files/{{FILE_ID}}/stats
Authorization: Bearer {{AUTH_JWT}}

HTTP 200
[Asserts]
jsonpath "$.payload" exists
