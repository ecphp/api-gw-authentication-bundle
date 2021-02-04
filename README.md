# API Gateway Authenticator

A bundle for Symfony 5.

## Installation

* Add a custom repository to your application `composer.json` file

```json
    "repositories": [{
        "type": "vcs",
        "url": "https://citnet.tech.ec.europa.eu/CITnet/stash/scm/ecphp/api-gw-authenticator.git"

    }],
```

* `composer require ecphp/api-gw-authenticator:dev-master`

* Copy the file `vendor/ecphp/api-gw-authenticator/Resources/config/packages/dev/api_gw_authenticator.yaml` inside your application `config` folder (`dev`, `test` or `prod`).

* Copy the file `vendor/ecphp/api-gw-authenticator/Resources/config/routes/api_gw_authenticator.yaml` inside your Symfony `routes` folder (`dev`, `test` or `prod`).

* Copying those files will be obsolete when the package will be OpenSourced and a Symfony recipe will be created.

* Edit that new configuration file and configure the API Gateway environment.
  3 choices are possible:
  * `intra`
  * `acceptance`
  * `production`

* Configure the security of your application, example:

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

* Get a valid token from API Gateway

* Make a request to `/api/user` withe the proper header to get the introspection of the JWT header.

Read more on the dedicated documentation site: https://ecphp-api-gw-authenticator-bundle.readthedocs.io/
