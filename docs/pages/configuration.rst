.. _configuration:

Configuration
=============

Hereunder an example of configuration for this bundle.

.. code:: yaml

    api_gw_authentication:
        defaults:
            env: acceptance # Available values are: acceptance, intra, production

    security:
        providers:
            api_gw_authentication:
                id: api_gw_authentication.user_provider
