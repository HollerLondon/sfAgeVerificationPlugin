sfAgeVerificationPlugin
=======================

Install sfDoctrineGuardPlugin

Enable sfDoctrineGuardPlugin and sfAgeVerificationPlugin in your app's settings.yml:

     all:
      .settings:
       enabled_modules:        [sfAgeVerification]

Set your application's myUser class to extend sfAgeVerifiedUser rather than sfBasicSecurityUser:

     class myUser extends sfAgeVerifiedUser
     {
     }

The countries.yml file defines country-specific required ages, and (optionally) the URL to redirect users to if they fall beneath the age limit

To customise the templates, create a new module in your application called `sfAgeVerification`, and create templates for `deniedSuccess.php` and `verifySuccess.php`. 

The plugin includes a Symfony form class (`AgeVerificationForm`) to display and validate the age gate form.

All templates and form fields are fully I18N-ready.
