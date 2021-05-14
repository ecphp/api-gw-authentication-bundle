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

This package has a `Symfony recipe`_ that will provides the minimum configuration files.

.. warning:: Be carefull, the recipe will create enable some routes in your ``dev`` environment only.
   Those routes might be considered as a security issue if they are enabled in the ``production`` environment.
   Those routes are ``/api/token`` and ``/api/user``.
   Find the documentation related to those routes inside the classes themselves.
   To disable them completely, just delete the file ``packages/config/routes/dev/api_gw_authentication.yaml`` from your application.

Step 2
~~~~~~

Edit the bundle configuration by editing the file ``config/packages/dev/api_gw_authentication.yaml``.

.. code-block:: yaml

    api_gw_authentication:
        defaults:
            env: acceptance # Available values are: acceptance, intra, production, user

Optionally, to use your own public and private key, then you do not need this package.
Simply enable the bundle `lexik/jwt-authentication-bundle`_ and follow their documentation.

However, if you still want this package and your own keys, edit the configuration as such

.. code-block:: yaml

    api_gw_authentication:
        defaults:
            env: user # Available values are: acceptance, intra, production, user
        envs:
            user:
                public: <path-to-the-public-key>
                private: <path-to-the-private-key>

The environment ``user`` is the only custom environment that you can create. It has a very limited use.
It was mostly created for the unit tests.

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
            - { path: ^/api/token, role: IS_ANONYMOUS } # Optional - See step 2, enable this ONLY for dev environment
            - { path: ^/api, role: IS_AUTHENTICATED_FULLY }

This configuration example will trigger the authentication on paths starting
with `/api`, therefore make sure that at least such paths exists.

Feel free to change these configuration to fits your need. Have a look at
`the Symfony documentation about security and Guard authentication`_.

Step 4
~~~~~~

Optionally, you can override the default HTTP client.

Edit your own `services.yaml` file as such:

.. code-block:: yaml

services
    cachedHttpClient:
        class: 'Symfony\Component\HttpClient\CachingHttpClient'
        arguments:
            $store: '@http_cache.store'

    api_gw_authentication.http_client:
        class: 'Symfony\Component\HttpClient\Psr18Client'
        arguments:
            $client: '@cachedHttpClient'

.. _lexik/jwt-authentication-bundle: https://packagist.org/packages/lexik/jwt-authentication-bundle
.. _a Symfony Flex recipe: https://github.com/symfony/recipes-contrib/blob/master/ecphp/api-gw-authentication-bundle/1.0/manifest.json
.. _Composer: https://getcomposer.org
.. _the Symfony documentation about security and Guard authentication: https://symfony.com/doc/current/security/guard_authentication.html
.. _Symfony recipe: https://github.com/symfony/recipes-contrib/tree/master/ecphp/api-gw-authentication-bundle/1.0
