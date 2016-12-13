# Laravel.SocialAuth

Stateless token-based api authentication in Laravel for both email and social media using [jwt-auth](https://github.com/tymondesigns/jwt-auth)

### Usage

##### Sign up

Sign up is for email authentication only. Social media users will automatically be signed up on first login. A signed JWT token identifying the user will be returned on successful sign up.

- Method: `POST`
- Path: `/api/v1/signup`
- Parameters: `name`, `email`, `password`

##### Login

A signed JWT token identifying the user will be returned on successful login.

- Method: `POST`
- Path: `/api/v1/login`
- Parameters for email authentication: `email`, `password`

##### Token test

- Method: `GET`
- Path: `/api/v1/test`
- Parameters: `token`

Alternately, the token may be included in the request header with key `Authorization` and value `Bearer {token}`	

### License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).
