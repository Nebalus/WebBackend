# Health check endpoint
# Tests if the API is up and running

GET {{BASE_URL}}/health

HTTP 200
[Asserts]
jsonpath "$.status" == "ok"
