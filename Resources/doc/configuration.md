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

Browser side validation
===

Custom validations
---
Customized validations are available in the js file `nissValidation.js`, to expose this file using Webpack,
add the following to your webpack.config.js

```javascript
let config = Encore.getWebpackConfig();
config.resolve.alias.emsf = path.resolve(__dirname, 'vendor/elasticms/form-bundle/Resources/assets/nissValidation.js');
```

To expose the frontend validations to other applications, create a new javascript file and import the validation functionalities
```javascript
// src/assets/nissValidation.js
import {} from 'emsf';
```

Using webpack with multiple configurations (https://symfony.com/doc/current/frontend/encore/advanced-config.html) one could use the following configuration:
```javascript
Encore.reset();

Encore
    .setOutputPath('public/emsform')
    .setPublicPath('/emsform')
    .setManifestKeyPrefix('emsform')

    .enableSourceMaps(!Encore.isProduction())

    .addEntry('js/form-validations', './assets/js/form-validations.js')
;

let formConfig = Encore.getWebpackConfig();
formConfig.resolve.alias.emsf = path.resolve(__dirname, 'vendor/elasticms/form-bundle/Resources/FormValidation/assets/app.js');

formConfig.name = 'formConfig';

module.exports = [config, formConfig];
```