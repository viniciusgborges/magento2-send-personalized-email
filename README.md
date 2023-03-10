# Magento 2.4.x module Personalized Email

    composer require vbdev/magento2-send-personalized-email

## Main Functionalities
- Attribute-based email sending module.
- The module will send an email to the customer who buys a product that has the "sendmail" attribute defined.
- The module offers a configuration in the admin, in **Stores->Configuration->Sales->Sales Emails->Personalized Email**
- In the admin settings, you can select the email template to be sent, but if no template is selected, the module has a default template, which you can include in view/frontend/email/personalized_email_template_example_default.html
- In the admin you can also select the sender email

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