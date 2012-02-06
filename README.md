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
		
Done!