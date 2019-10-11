# Configuration
## config file (config/packages/ems_form.yaml)
```yaml
ems_form:
    hashcash_difficulty: '%env(int:EMSF_HASHCASH_DIFFICULTY)%'
    instance:
        type: form_instance
        type-form-field: form_structure_field
        type-form-markup: form_structure_markup
        form-field: form
        theme-field: theme_template
        submission-field: submissions
```

## HashCash
By default hashcash is enable because of the default value (16384) for the configuration setting **ems_form.hashcash_difficulty**.
This value can be overwritten by setting the environment variable **EMSF_HASHCASH_DIFFICULTY**.

For disabling hashcash set the difficulty value to 0.
