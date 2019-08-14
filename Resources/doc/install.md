Endpoint
===
Load the iframe endpoint in `routes.yaml`:
```yaml
forms:
  resource: '@EMSFormBundle/Resources/config/routing/form.xml'
```

Framework configuration
===
```yaml
framework:
    validation:
        enabled:              true
        translation_domain:   validators
        email_validation_mode: html5
    assets:
        packages:
            emsform:
                json_manifest_path: '%kernel.project_dir%/public/bundles/emsform/manifest.json'
```