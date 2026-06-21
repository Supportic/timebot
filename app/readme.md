# Timebot

## Install

Create `.env.local` file:

```bash
DATABASE_URL="mysql://admin:admin@db:3306/timebot?serverVersion=12.2.2-MariaDB&charset=utf8mb4"
APP_SECRET=ChangeMe
MAILER_DSN=smtp://mailpit:1025
```

## 3rd Party Packages

Holidays: https://www.yasumi.dev/docs/getting-started/  
Google API: https://github.com/googleapis/google-api-php-client

## Personio API

https://api.personio.de

### Authentication

In order to talk to the personio API it requires an auth token in each request which can be obtained from the `POST /auth` route.  
This route requires a `client_id` and `client_secret` which can be generated in the personio backend. (see [Settings > API credentials](https://developer.personio.de/docs/getting-started-with-the-personio-api#2-api-access-and-authorization))  
The credentials should have access to:

- employees:read
- attendances:read
- absences:read

You can test your credentials on this site: https://developer.personio.de/reference/post_auth  
The token is valid for 24h.

List all cache pools (from config/packages/cache.yaml):  
`php bin/console cache:pool:list`

Delete cached auth token:  
`php bin/console cache:pool:delete personio.auth.cache personio.api.v1.auth_token`

### fetching the API

We don't serialize API responses into Models because serializing an arbitrary amount of data into classes creates an overhead of several seconds. (depending on the amount of data)
