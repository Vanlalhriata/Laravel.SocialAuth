# Laravel.SocialAuth

Stateless token-based api authentication in Laravel for both email and social media using [jwt-auth](https://github.com/tymondesigns/jwt-auth)

---

### Installation

After cloning the repository, use Composer to install dependencies:
```
$ composer install
```
Copy `env.example` into `.env` and set the values for database, social media secrets etc.. Then generate the Laravel app key and JWT secret:
```
$ php artisan key:generate
$ php artisan jwt:generate
```
The JWT secret is stored in `config/jwt.php` by default. You may want to move this into the `.env` file instead.

Lastly, run the migrations:
```
$ php artisan migrate
```
Boom! Done.

---

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
- Parameters for email authentication: `email`, `password`, `auth-provider=email`
- Parameters for social authentication: `social-id`, `access-token`, `auth-provider={provider}`

Valid values for `auth-provider`: `email`, `facebook` 

##### Token test

- Method: `GET`
- Path: `/api/v1/test`
- Parameters: `token`

Alternately, the token may be included in the request header with key `Authorization` and value `Bearer {token}`

---

### License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).