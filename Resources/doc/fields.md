# Supported fields

## Overview

### Common fields
* [Checkbox](#checkbox)
* [ChoiceCheckboxes](#choicecheckboxes)
* [ChoiceRadios](#choiceradios)
* [ChoiceSelect](#choiceselect)
* [ChoiceSelectMultiple](#choiceselectmultiple)
* [Email](#email)
* [EmailWithConfirmation](#emailwithconfirmation)
* [File](#file)
* [Number](#number)
* [Phone](#phone)
* [Text](#text)
* [Textarea](#textarea)

### Custom fields
* [Markup](#markup)
* [NissInsz](#nissinsz)
* [Submit](#submit)


## Fields

### Checkbox <a name="checkbox"/>
A single checkbox field, that can be turned on and off again.

### Choice Checkboxes <a name="choicecheckboxes"/>
A list of values that can be chosen from using a checkbox layout. The end user is allowed to choose multiple values.

See [Choice Select](#choiceselectpassing) for information on passing values and labels to this field.

### Choice Radios <a name="choiceradios"/>
A list of values that can be chosen from using a radio button layout. The end user can only select one value.

See [Choice Select](#choiceselectpassing) for information on passing values and labels to this field.

### Choice Select <a name="choiceselect"/>
A list of values that can be chosen from using a select box. The end user can only select one value.

#### Passing values and labels to this field <a name="choiceselectpassing"/>
Choices are passed via the "choices" content type. The content type contains a list of values and labels. These should be formatted as a json array.
A simple choice value list would look like this:
```json
[
  "dossier",
  "demand",
  "document",
  "information",
  "complaint",
  "other"
]
```

A multi level value list would look as follows (demonstrating 3 levels!):
```json 
[
  { "dossier": [
    "dossier-a",
    { "dossier-b": ["dossier-b-1", "dossier-b-2"]},
    { "dossier-c": ["dossier-c-1", "dossier-c-2"]}
  ]},
  { "demand":  [
    "demand-a",
    { "demand-b": ["demand-b-1"]}
  ]},
  "document",
  "information",
  { "complaint":  [
    { "complaint-a": ["complaint-a-1", "complaint-a-2"]},
    { "complaint-b": ["complaint-b-1", "complaint-b-2"]},
    { "complaint-c": ["complaint-c-1", "complaint-c-2"]}
  ]},
  "other"
]
```

If you would pass a multi level choice by mystake to this field, only the first level will be used.

### Choice Select Multiple <a name="choiceselectmultiple"/>
A list of values that can be chosen from using a select box. The end user is allowed to choose multiple values.

See [Choice Select](#choiceselectpassing) for information on passing values and labels to this field.

### Email <a name="email"/>
Ensure that the end user's input is a valid email address.

### Email With Confirmation <a name="emailwithconfirmation"/>
Ensure that the end user's input is a valid email address. And provides a second field in which the end user needs to validate the given address.
This field is designed to prevent pasting values in both the original and repeated email field.

### File <a name="file"/>
Allow an end user to upload a file.

### Number <a name="number"/>
This field only allows integers as input.

### Phone <a name="phone"/>
A field that's used for phone input, as per html standard no validations happen by default on this field.

### Text <a name="text"/>
A simple field for text input (one line).

### Textarea <a name="textarea"/>
A simple field for large text input (multiple lines).

### Markup <a name="markup"/>
This field is special, as it is not a real form field. The `Markup` field allows you to introduce text between fields in your form.
The end user cannot change the value of this field, and the static data of this 'field' is not processed on submit.

### NissInsz <a name="nissinsz"/>
A text field designed to combine with the NissInsz validation. The frontend will automatically activate validation when both this field and it's validation are combined.

### Submit <a name="submit"/>
Make sure to add this field at the end of your form to allow the submission of your data to the server!
