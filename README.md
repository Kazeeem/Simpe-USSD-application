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

<img width="1007" alt="Screenshot 2023-04-08 at 9 01 47 AM" src="https://user-images.githubusercontent.com/35298707/230710751-152d3895-8d44-4e82-92f4-217393ab44d0.png">
<img width="1061" alt="Screenshot 2023-04-08 at 9 05 12 AM" src="https://user-images.githubusercontent.com/35298707/230710858-1824638c-a1cb-4771-b911-32c66cbe05ae.png">
