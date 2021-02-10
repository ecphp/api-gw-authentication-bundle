Usage
=====

Step 1
~~~~~~

Follow the :ref:`installation` procedure.

Step 2
~~~~~~

Configure the configuration files accordingly and the security of your Symfony application.

Step 3
~~~~~~

Get a valid token from API Gateway.

Step 4
~~~~~~

- Make a request to ``/api/user`` with the ``Authorization`` header.

``curl -X GET "http://127.0.0.1:8000/api/user" -H "Authorization: Bearer <insert-token-here>"``

At this point, the ``KeyLoader`` will try to retrieve the public key from the API Gatewey environment in use.

If it fails, it will use a local copy of the key inside the bundle.

The ``HttpClient`` in use in this bundle is a ``CachingHttpClient``, which means that the request to API Gateway
is cached by default. So when you request the keys multiple times, only one http call will be made.

There is no lifespan configuration for a ``CachingHttpClient``, it is forever cached until you clear the Symfony cache yourself.

