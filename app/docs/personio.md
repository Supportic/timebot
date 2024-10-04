# Personio

## API

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
`php bin/console cache:pool:delete personio.auth.cache personio.api.auth_token`
