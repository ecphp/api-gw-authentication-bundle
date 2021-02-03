# API Gateway Authenticator

A bundle for Symfony 5.

## Installation

* Add a custom repository to your application `composer.json` file

```yaml
    "repositories": [{
        "type": "vcs",
        "url": "https://citnet.tech.ec.europa.eu/CITnet/stash/scm/ecphp/api-gw-authenticator.git"

    }],
```

* `composer require ecphp/api-gw-authenticator:dev-master`

* Copy the file `vendor/ecphp/api-gw-authenticator/Resources/config/packages/dev/api_gw_authenticator.yaml` inside your Symfony configuration folder (`dev`, `test` or `prod`).

* Edit that new configuration file and configure the API Gateway environment.
  3 choices are possible:
  * `intra`
  * `acceptance`
  * `production`

Read more on the dedicated documentation site: https://ecphp-api-gw-authenticator-bundle.readthedocs.io/
