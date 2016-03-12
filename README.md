# Protected-Signed-URLs-Self-Hosted
Self hosted signed url's for protecting downloads, similar to Amazon S3 .

### Introduction

This project is a drop-in solution for those wishing to have links that expire without incurring the additional unknown surcharge most content delivery networks charge for this service. I started this project as a result of being unable to use Amazons own signing method to create signed links to content on Amazon CloudFront using a CNAME and their free offering of SSL certificates. While this seemed like a great idea at the time, there was a lot of pain involved in sifting through their various API documentation, php libraries, and many hours getting trolled by Google search results on the topic including bucket permissions, cloudfront permissions, OAI, policies, groups, users, all resulting in the same thing that even popular software which Amazon touts somewhere in their api documentation, was a complete waste of time as nothing would generate the links.

After coming to my senses, I decided to call it quits with Amazton CloudFront (and thus s3) and write my own method which uses similar policy-style signing of URL's using a private/public key in the form of certificates to sign and validate the signing. The next problem I faced, was how to deliver large files without consuming all the memory in the system due to the way an `fread()` - `echo` loop would work.  That is when I remember XSendFile, an Apache module that does exactly that.


### System Requirements
- mod_rewrite
- xsendfile
- php 5.4+
- openssl (may work with other compatible libs but is untested)

### Installation Instructions

These instructions assume that the system requirements have been met, are installed, and enabled.

#### Server Configuration

In your websites main configuration file, ensure that the following variables have been set for your specific website. For the purpose of simplifying the documentation, we will use `/var/www/html/downloads/` as the path where you have this script installed, and `/var/www/html/` is the server location of your websites root folder.

| Parameter | Value |
| --- | --- |
|XSendFile|on|
|XSendFilePath|/var/www/html/downloads/|

For Apache, you will want to create a new `.htaccess` file in `/var/www/html/downloads/` and paste the following into it:

```apacheconf
Options -Indexes
<IfModule mod_headers.c>
	Header Always set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
	Header Always set Pragma "no-cache"
</IfModule>
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteRule ^protected.php - [QSA,L]
	RewriteRule ^(.*)$ ./protected.php [QSA,L]
</IfModule>
```

#### Generating the Private/Public key pair

By default, there is a private/public key pair included in the source as the define values for `PRIVATE_KEY` and `PUBLIC_KEY` which server no other purpose than to demonstrate the location this information will go, and to provide a quick way to 'test' the suitability of this project for those who have already been through the endless cesspool of broken/defunct projects without wasting too much more time.

**_DO NOT USE THE INCLUDED PRIVATE/PUBLIC KEY IN PRODUCTION_**

Generate the Private key (it will prompt for a password)
```sh
openssl genrsa -des3 -out PRIVATE_KEY.pem 2048
```

Remove the password from the Private key (it will prompt for the password you used when generating the private key)
```sh
openssl genrsa -des3 -out PRIVATE_KEY.pem 2048
```

**_As always, it is important that you keep your private keys: Private !!!_**


Generate the Public key
```
openssl rsa -in PRIVATE_KEY.pem -outform PEM -pubout -out PUBLIC_KEY.pem
```

The entire contents of the files are their respective defines in `config.inc.php`.  For example:

```php
	define('PUBLIC_KEY', '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx6ux0PNiW6QcKqtXxjQJ
Qrv0D4hLkoHdLzNuwvxSQpwF7YkZ1E7DfGsDUV0hZkc2vuIKIq1wBL/q5BL4lqH2
fxotBI9VJf7ldYVqywk/5lEDymxog7DmQhUid688xbUCtUUBbZ88jY1x+/rhgf7w
wHuV95X5Z5dGwXdO8z64DjWqgb8wPIiMHuCxm9/KMm3O9fzrzC80oHzXMmJRZ/tP
p2odV6xQh5Y3TkzFn6quod5loTiSsN1Ue9n9QqPVlQJD9yKiAfeg+YdRMfuYI1Vw
4cJ+r2iKAuNs+GtQOW3b1VV8hPQeMSwWShMq8YTm7IAaUaLGEwfMOuBW06OeV+i9
1wIDAQAB
-----END PUBLIC KEY-----');
```

... is the define for `PUBLIC_KEY.pem`

_NOTE:_ You can place the `PUBLIC_KEY.pem`, and `PRIVATE_KEY.pem` inside the same folder where the script is installed and it will automatically detect them and use them, eliminating the need for the `config.inc.php`. The recommended approach is to embed them directly in the `config.inc.php` file,

#### Where do you put your files ?

On the server of course !!!  While there are many solutions that require you to host the files in some obscure location, this does not require any such tactics to be involved due to the way that the files are rendered inaccessible directly through mod_rewrite.

The only requirement in this regard, is that the files are located in the same folder where this script is installed, and that the `XSendFilePath` as outlined in the **Server Configuration** step is pointed to the right folder. If using multiple installations of this script, then you can simply point XSendFilePath to the base folder of your website.

#### Generating a Signed Link

**sign( url , expires)**

|Parameter|Description|
|---|---|
|url|The url to sign. This must be a resource that is protected on your server. The script will generate a signed link for any url, however it can only validate and serve content where it is installed.|
|expires|The time (in minutes) for how long the generated link should be valid for|

_Returns_

|type|result|
|---|---|
|string|Full url with attached query string parameters for the time the link expires, and the hash/signature.|

```php
define('URLSIGNINCLUDE', true);
require('/path/to/protected.php');

$link = URLSigner::sign( 'https://www.foo.com/downloads/protectedfile.zip', 10 );
```

#### Verifying a Signed Link

**verify( url )**

|Parameter|Description|
|---|---|
|url|The full url to verify, including all attached query parameters, scheme, and path. Example: `https://www.foo.com/downloads/protectedfile.zip?Expires=1234567&Hash=12345678...`|

_Returns_

|type|result|
|---|---|
|boolean|true if signature is valid and expires value hasn't elapsed|

```php
define('URLSIGNINCLUDE', true);
require('/path/to/protected.php');

$bool = URLSigner::verify( 'https://www.foo.com/downloads/protectedfile.zip?Expires=1234567&Hash=12345678...' );
if($bool === false) {
	echo 'Valid Link';
} else {
	echo 'Invalid Link';
}
```
