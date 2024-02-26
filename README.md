## How to run the app locally
Follow the following steps to run the app locally. You will need docker desktop installed on your PC.
1. Clone the repository on your pc:
```
git clone https://github.com/kavi106/Car4Sure
```

2. Build the app:
```
docker-compose up --build
```

3. Access the app with the url: http://localhost:8000

## How it was implemented

Symfony was used as a backend and ReactJS as the front end.

## Symfony
I used Symfony because it has all the tools/libraries needed.

"symfony/validator" module was use to validate the inputs to the API.

Sqlite was used as a database because its easy to implement. I used doctrine so we can swap database if needed.

"lexik/jwt-authentication-bundle" module was used for authentication.

## ReactJS
ReactJS was used because I have the knowledge but any other technology could have been used.

## Not Implemented
PDF generation was not implemented due to lack of time.

## What could have been better
Error popup displays on the frontend.

Better transition between the frontend screens.

Thank you


