# Example
Requirements:
- 1 elasticms backend [https://emsforms.example]
- 1 elasticms skeleton [https://emsforms-skeleton.example]

## Frontend
Your html page needs the following **3 elements** for getting a EMSForm.

### form.js
Includes the javascript for sending and receiving postMessage from or to the ems form skeleton.
```html
<script src="https://emsforms-skeleton.test/bundles/emsform/js/form.js"></script>
```

### ems-form-iframe
This iframe is used for sending postMessage to the ems form skeleton. 
The **myCommunicationId** is the ouuid from the emsforms backend. 
You will only get a response if your domain is allowed!

```html
<iframe id="ems-form-iframe" src="http://emsforms-skeleton.test/form/myCommunicationId"></iframe>
```

### ems-form
The form or messages will be placed in this container.
```html
<div id="ems-form"></div>
```

### full example

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Example emsForm</title>
    </head>
<body>
    <div id="wrapper">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla posuere velit quis elit rutrum,
            eu ornare dui cursus. Maecenas rhoncus velit justo. Vestibulum eleifend nunc ut lorem malesuada,
            id pellentesque tortor mollis.
        </p>
        <div id="ems-form"></div>
    </div>
    <iframe id="ems-form-iframe" src="http://emsforms-skeleton.test/iframe/{ouuid}/{locale}"></iframe>
    <script src="https://emsforms-skeleton.test/bundles/emsform/js/form.js"></script>
</body>
</html>
```

## Custom

If you change the ids you need to initialize the form yourself 
and pass the correct values for the **form** and **iframe** option.
If you want to add multiple forms you need to have 2 iframes.

```html
    <script src="https://emsforms-skeleton.test/bundles/emsform/js/form.js"></script>
    <script>
        document.getElementById('ems-form-iframe1').onload = function() {
            new emsForm({ 'idForm': 'form1', 'idIframe': 'iframe1'}).init(); 
        };
        document.getElementById('ems-form-iframe2').onload = function() {
            new emsForm({ 'idForm': 'form2', 'idIframe': 'iframe2'}).init(); 
        };
    </script>
 ```







