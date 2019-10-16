# Supported validations

## Overview

### HTML5 validations
* [Email](#email)
* [Max](#max)
* [MaxLength](#maxlength)
* [Min](#min)
* [MinLength](#minlength)
* [Required](#required)

### Custom validations
* [MaxLengthCounter](#maxlengthcounter)
* [NissInsz](#nissinsz)
* [Phone](#phone)

## Validations

### Email <a name="email"/>
Validate email input as per HTML5 standard definition.

### Max <a name="max"/>
Define a maximum value that can be used as input of the associated field.

### Max Length <a name="maxlength"/>
Define a maximum number of characters that can be used in the input of the associated field.

### Min <a name="min"/>
Define a minimum value that can be used as input of the associated field.

### Min Length <a name="minlength"/>
Define a minimum number of characters that should be used in the input of the associated field.

### Required <a name="required"/>
Determine that a field is required.

### Max Length Counter <a name="maxlengthcounter"/>
Define a maximum number of characters that can be used in the input of the associated field.
Use this variant of the Max Lenght validation if you want to automatically show a counter of the remaining number of characters available for the end user.

### NISS INSZ <a name="nissinsz"/>
Validate that the given number is a valid Belgium NISS (fr) / INSZ (nl) number. Implementation details are documented in the source code.
This validation is 'forgiving', meaning that all non valid input characters are filtered away before validation. This allows the end user to input his number in the format he likes.

### Phone <a name="phone"/>
Validate that the input is a valid phone number based on Belgium fixed and mobile lines.
