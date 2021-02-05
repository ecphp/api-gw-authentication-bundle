# API Gateway Authenticator

A bundle for PHP >= 7.4 and Symfony 5.

This bundle provides the necessary to authenticate a request based on a specific HTTP header.

It has been written specifically to authenticate requests from [API Gateway][http wso2 documentation].

However, its use is not limited to API Gateway and can be used to authenticate from any other services too, as long as they use the `Authorization` header.

## API Gateway

The European Commission API Gateway service allows you to deploy microservices as APIs behind the Gateway.

The Gateway offers an added layer of security and multiple useful utilities such as:

- API protection with tokens
- API lifecycle management
- API versioning
- API traffic management & throttling
- API analytics
- API management automation
- No hassle API publication with swagger
- Store of APIs to reuse

This service is based on the open source project [WSO2 API Gateway][http wso2 website], in a distributed deployment with custom components.

## Alternatives

There are many existing alternatives for doing JWT authentication.
This bundle is different from others:

* Rely on [firebase/php-jwt][packagist firebase/php-jwt] library,
* Provides default configuration to work with API Gateway,
* Has a failsafe mechanism for public key retrieval and embed the public keys of the default API Gateway in case of failure,
* Provides a default UserProvider service and User entity,

## Installation

* The package is not yet public, so you must manually add its repository to your application `composer.json` file:

```json
    "repositories": [{
        "type": "vcs",
        "url": "https://citnet.tech.ec.europa.eu/CITnet/stash/scm/ecphp/api-gw-authenticator.git"

    }],
```

* `composer require ecphp/api-gw-authenticator:dev-master`

* Copy the file `vendor/ecphp/api-gw-authenticator/Resources/config/packages/dev/api_gw_authenticator.yaml` inside your application `config` folder (`dev`, `test` or `prod`).

* Copy the file `vendor/ecphp/api-gw-authenticator/Resources/config/routes/api_gw_authenticator.yaml` inside your Symfony `routes` folder (`dev`, `test` or `prod`). For security reasons, it's better to only enable those routes on the `dev` environment.

* Copying those files will be obsolete when the package will be OpenSourced and a Symfony recipe will be created.

* Edit that new configuration file and configure the API Gateway environment.
  3 choices are possible:
  * `intra`
  * `acceptance`
  * `production`

* Configure the security of your application through `config/packages/security.yaml`:

    ```yaml
    security:
        firewalls:
            default:
                anonymous: ~
                stateless: true
                guard:
                    provider: apigwauthenticator
                    authenticators:
                        - apigwauthenticator.guardauthenticator
        access_control:
            - { path: ^/api, role: IS_AUTHENTICATED_FULLY }
    ```

* Get a valid token from API Gateway.

* Make a request to `/api/user` with the `Authorization` header.

```
curl -X GET "http://127.0.0.1:8000/api/user" -H "Authorization: Bearer <insert-token-here>"
```

Read more on the [dedicated documentation site][http readthedocs] (Not ready yet).

[http wso2 documentation]: https://docs.wso2.com/display/AM260/
[http wso2 website]: https://wso2.com/
[packagist firebase/php-jwt]: https://packagist.org/packages/firebase/php-jwt
[http readthedocs]: https://ecphp-api-gw-authenticator-bundle.readthedocs.io/
