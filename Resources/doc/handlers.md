Handle Submitted data
=====================

When you are using forms, you probably also want to handle the submitted data.
The [SubmissionBundle](https://github.com/ems-project/EMSSubmissionBundle) provides default handlers by implementing the `EMS\FormBundle\Handler\AbstractHandler`.

Using your own handlers
-----------------------

Doing more specific actions with the submitted data can be achieved using Symfony tags by adding more implementations of the AbstractHandler class.
Inspire yourself on the implementations found in the [SubmissionBundle](https://github.com/ems-project/EMSSubmissionBundle), email for example:

```php
<?php
//EmailHandler.php

namespace EMS\SubmissionBundle\Handler;
               
//...

class EmailHandler extends AbstractHandler
{
    //... setup removed for simplicity

    public function handle(SubmissionConfig $submission, FormInterface $form, FormConfig $config, ResponseCollector $responses): AbstractResponse
    {
        try {
            //render the email template
        } catch (\Exception $exception) {
            return new FailedResponse(sprintf('Submission failed, contact your admin. %s', $exception->getMessage()));
        }

        // other checks / manipulations

        return new EmailResponse();
    }
}
```

```php
<?php
//EmailResponse()

namespace EMS\SubmissionBundle\Submit;

class EmailResponse extends AbstractResponse
{
    public function getResponse(): string
    {
        return 'Submission send by mail.';
    }
}
```

Let the form-bundle find your handler by tagging it:
```xml
<service id="emss.emailhandler" class="EMS\SubmissionBundle\Handler\EmailHandler">
    <!-- ... arguments -->
    <tag name="emsf.handler" />
</service>
```

Chained Handlers
----------------

Handlers are called one-by-one, each handler's response is collected and useable for the next Handler using the ResponseCollector.
In the Handlers 'handle' function this object is passed. To get the result of the previous handler simply call 'lastResone'.

```php
<?php
//EmailHandler.php

namespace EMS\SubmissionBundle\Handler;
               
//...

public function handle(SubmissionConfig $submission, FormInterface $form, FormConfig $config, ResponseCollector $responses): AbstractResponse
    {
        try {
            if ($responses->hasLastResponse()) {
                //do something with $responses->getLastResponse();
            }
            //render the email template
        } catch (\Exception $exception) {
            return new FailedResponse(sprintf('Submission failed, contact your admin. %s', $exception->getMessage()));
        }

        // other checks / manipulations

        return new EmailResponse();
    }
```
