# Symfony

## Services

## Translations

Manage translations with twig: [Symfony Translations](https://symfony.com/doc/current/translation.html)

If you want to share/use the translation files with/in JavaScript, use this library: [Symfony UX Translator](https://symfony.com/bundles/ux-translator/current/index.html)  
[https://ux.symfony.com/translator](https://ux.symfony.com/translator)

## Security

[https://symfony.com/doc/current/security.html#roles](https://symfony.com/doc/current/security.html#roles)

### Impersonating users

You can act to be another logged in user. Only a user with the role `ROLE_ALLOWED_TO_SWITCH` will be able do that. The setting for this can be adjusted in the `security.yaml` config file.

### Check if user is logged in

1. if you've given every user ROLE_USER, you can check for that role
2. you can use the special "attribute" IS_AUTHENTICATED_FULLY in place of a role (You can use IS_AUTHENTICATED anywhere roles are used)

```php
public function adminDashboard(): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
}
```

Actually, there are some special attributes like this:

- **IS_AUTHENTICATED_FULLY**: This is similar to IS_AUTHENTICATED_REMEMBERED, but stronger. Users who are logged in only because of a "remember me cookie" will have IS_AUTHENTICATED_REMEMBERED but will not have IS_AUTHENTICATED_FULLY.
- **IS_REMEMBERED**: Only users authenticated using the remember me functionality, (i.e. a remember-me cookie).
- **IS_IMPERSONATOR**: When the current user is impersonating another user in this session, this attribute will match.

### Route Authentication

#### Token

[https://symfony.com/doc/current/security/access_token.html](https://symfony.com/doc/current/security/access_token.html)

Use Access Token Authentication when you want to authenticate **users**. Not recommended for API access because we expect a user badge to return.

#### Custom Authenticator

[https://symfony.com/doc/current/security/custom_authenticator.html](https://symfony.com/doc/current/security/custom_authenticator.html)

Same as token based because it's made for identifying **users**.

#### Guards

[https://symfony.com/doc/current/routing.html#matching-expressions](https://symfony.com/doc/current/routing.html#matching-expressions)

Define a controller with a method to check every request if the condition is fulfilled.

```php
#[AsRoutingConditionService(alias: 'route_checker')]
class RouteChecker
{
    public function check(Request $request): bool
    {
        // ...
    }
}

// Controller (using an alias):
#[Route(condition: "service('route_checker').check(request)")]
```

#### Event Listeners

[https://symfony.com/doc/current/security.html#security-events](https://symfony.com/doc/current/security.html#security-events)

Subscribe to the HTTP request event and check/react on it.
