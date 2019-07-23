Endpoint
===
Load the iframe endpoint in `routes.yaml`:
```yaml
forms:
  resource: '@EMSFormBundle/Resources/config/routing/form.xml'
```

The system is using the client request from the EMS\ClientHelper namespace. Make sure to define it in your `services.yaml` configuration:
```yaml
EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest: '@emsch.client_request.website'
```

Server side validation
===

Email
---
Email validation is handled by the Symfony framework configuration. To activate HTML5 email validation,
use the following configuration:
```yaml
framework:
    validation:
        enabled:              true
        translation_domain:   validators
        email_validation_mode: html5
```