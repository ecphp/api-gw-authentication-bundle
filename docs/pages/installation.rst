.. _installation:

Installation
============

This package has `a Symfony Flex recipe`_ that will install configuration files for you.

Default configuration files will be copied in the `dev` environment.

Step 1
~~~~~~

The recommended way to install it is with Composer_ :

.. code-block:: bash

    composer require ecphp/api-gw-authentication-bundle

Step 2
~~~~~~

Edit the bundle configuration by editing the file `config/packages/dev/api_gw_authentication.yaml`.

.. code-block:: yaml

    api_gw_authentication:
        defaults:
            env: acceptance # Available values are: acceptance, intra, production, user

Step 3
~~~~~~

This is the crucial part of your application's security configuration.

Edit the security settings of your application by edition the file `config/packages/security.yaml`.

.. code-block:: yaml

    security:
        firewalls:
            default:
                anonymous: ~
                stateless: true
                guard:
                    provider: api_gw_authentication # This is provided by default by the bundle.
                    authenticators:
                        - lexik_jwt_authentication.jwt_token_authenticator
        access_control:
            - { path: ^/api/token, role: IS_ANONYMOUS } # Optional - Enable this ONLY on dev environment
            - { path: ^/api, role: IS_AUTHENTICATED_FULLY }

This configuration example will trigger the authentication on paths starting
with `/api`, therefore make sure that at least such paths exists.

Feel free to change these configuration to fits your need. Have a look at
`the Symfony documentation about security and Guard authentication`_.

.. _a Symfony Flex recipe: https://github.com/symfony/recipes-contrib/blob/master/ecphp/api-gw-authentication-bundle/1.0/manifest.json
.. _Composer: https://getcomposer.org
.. _the Symfony documentation about security and Guard authentication: https://symfony.com/doc/current/security/guard_authentication.html
