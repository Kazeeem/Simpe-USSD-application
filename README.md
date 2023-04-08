# Simpe-USSD-application

This is a simple USSD application written with Vanilla PHP, it allows users to do the following:

- Register
- Send Money
- Withdraw Money
- Check Balance

_P.S: CODE PERFECTION WAS NOT THE GOAL OF THIS PROJECT BUT RATHER FULL FOCUS ON FUNCTIONALITY, DO NOT USE IN PRODUCTION.__

## How To Test

- Run the project on Postman e.g http:localhost/ussd/index.php
- The acceptable request method is POST
- The following keys should be added to the "form-data" 
  - sessionId
  - serviceCode
  - phoneNumber
  - text
- Every menu option chosen should be typed into the text field. For menus that have sub-menus, separate the options or inputs with asterisks (*)
