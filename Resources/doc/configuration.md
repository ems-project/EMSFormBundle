Endpoint
===
Load the iframe endpoint in `routes.yaml`:
```yaml
forms:
  resource: '@EMSFormBundle/Resources/config/routing/form.xml'
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
Customized validations are available in the js file `form-validations.js`, to expose this file using Webpack, 
add the following to your webpack.config.js

```javascript
var config = Encore.getWebpackConfig();
config.resolve.alias.emsf = path.resolve(__dirname, 'vendor/elasticms/form-bundle/Resources/assets/form-validations.js');
```

To expose the frontend validations to other applications, create a new javascript file and import the validation functionalities
```javascript
// src/assets/form-validations.js
import {} from 'emsf';
```