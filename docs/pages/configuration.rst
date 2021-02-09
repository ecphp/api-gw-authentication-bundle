.. _configuration:

Configuration
=============

Hereunder an example of configuration for this bundle.

.. code:: yaml

    api_gw_authentication:
        defaults:
            env: acceptance # Available values are: acceptance, intra, production, user

    security:
        providers:
            api_gw_authentication:
                id: api_gw_authentication.user_provider

You may customize a specific configuration by doing:

.. code:: yaml

    api_gw_authentication:
        defaults:
            env: user # Available values are: acceptance, intra, production, user
        envs:
            user:
                public: <path-to-public-key-in-pem>
                private: <path-to-private-key-in-pem>

    security:
        providers:
            api_gw_authentication:
                id: api_gw_authentication.user_provider

However, it is impossible to override existing API Gateway environments (
acceptance, intra and production).
