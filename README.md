# Magento 2.4.x module Personalized Email

    composer require vbdev/magento2-send-personalized-email

## Main Functionalities
- Attribute-based email sending module.
- The module will send an e-mail to the customer who makes a purchase containing a product with the attribute that was defined in the Select attribute code field in **Stores->Configuration->Sales->Sales Emails->Personalized Email->Custom Attribute Code Select**
- The module offers a configuration in the admin, in **Stores->Configuration->Sales->Sales Emails->Personalized Email**
- In the admin settings, you can select the email template to be sent, but if no template is selected, the module has a default template, which you can include in view/frontend/email/personalized_email_template_example_default.html
- In the admin you can also select the sender email

https://user-images.githubusercontent.com/99985671/232643562-b5afbd38-4849-4fce-90d0-d95cc140d6b5.mp4

### Type 1: Zip file

- Unzip the zip file in `app/code/Vbdev`
- Enable the module by running `php bin/magento module:enable Vbdev_PersonalizedEmail`
- Apply database updates by running `php bin/magento setup:upgrade`
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Install the module composer by running `composer require vbdev/magento2-send-personalized-email`
- enable the module by running `php bin/magento module:enable Vbdev_PersonalizedEmail`
- apply database updates by running `php bin/magento setup:upgrade`
- Flush the cache by running `php bin/magento cache:flush`
