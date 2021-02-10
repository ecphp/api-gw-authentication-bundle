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

The package does not have yet a recipe, so you have to copy some files over in your application
in order to get everything working.

Recursively copy the directory ``vendor/ecphp/api_gw_authentication/Resources/config`` in your application.

.. warning:: Be carefull, copying this directory will copy the file
   ``vendor/ecphp/api_gw_authentication/Resources/config/routes/dev/api_gw_authentication.yaml`` which
   enable some routes in your ``dev`` environment only. Those routes might be a security issue if they
   are enabled in the ``production`` environment.
   Those routes are ``/api/token`` and ``/api/user``.
   Find the documentation related to those routes inside the classes themselves.
   To disable them completely, just delete the file ``packages/config/routes/dev/api_gw_authentication.yaml`` from your application.

Step 3
~~~~~~

Edit the bundle configuration by editing the file ``config/packages/dev/api_gw_authentication.yaml``.

.. code-block:: yaml

    api_gw_authentication:
        defaults:
            env: acceptance # Available values are: acceptance, intra, production, user

Optionaly, to use your own public and private key, then you do not need this package.
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

Step 4
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

.. _lexik/jwt-authentication-bundle: https://packagist.org/packages/lexik/jwt-authentication-bundle
.. _a Symfony Flex recipe: https://github.com/symfony/recipes-contrib/blob/master/ecphp/api-gw-authentication-bundle/1.0/manifest.json
.. _Composer: https://getcomposer.org
.. _the Symfony documentation about security and Guard authentication: https://symfony.com/doc/current/security/guard_authentication.html
