API Gateway Authentication Bundle
=================================

This bundle provides the necessary to authenticate a request based on a specific HTTP header.

It has been written to facilitate the authentication of requests from `API Gateway`_.

This bundle relies on `lexik/jwt-authentication-bundle`_ and provide a specific KeyLoader.

The features it provides are:

- Provides default configuration to work with API Gateway,
- Has a failsafe mechanism for public key retrieval and embed the public keys of the default API Gateway in case of failure,
- Provides a default ``UserProvider`` service and ``User`` entity,

API Gateway
~~~~~~~~~~~

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

This service is based on the open source project `WSO2 API Gateway`_, in a distributed deployment with custom components.

.. _API Gateway: https://docs.wso2.com/display/AM260/
.. _lexik/jwt-authentication-bundle: https://github.com/lexik/LexikJWTAuthenticationBundle
.. _WSO2 API Gateway: https://wso2.com/

.. toctree::
    :hidden:

    API Gateway Authentication Bundle <self>

.. toctree::
   :hidden:
   :caption: Table of Contents

   Requirements <pages/requirements>
   Installation <pages/installation>
   Configuration <pages/configuration>
   Usage <pages/usage>
   Tests <pages/tests>
   Contributing <pages/contributing>
   Development <pages/development>
