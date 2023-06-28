<?php
/**
 * Local application configuration
 *
 * Insert your local database credentials here
 * and provide the email address the system should use.
 */

return array(
    'db' => array(
        'database' => 'ep3bs',
        'username' => 'ep3bs',
        'password' => 'PuLf648YbAGq7B2m8H',

        'hostname' => 'db',
        'port' => 3306,
    ),
    'mail' => array(
        'type' => $_ENV["MAIL_TYPE"], // 'sendmail', 'smtp' or 'smtp-tls'
        'address' => $_ENV["MAIL_ADDRESS"], // info@bookings.example.com
            // Make sure 'bookings.example.com' matches the hosting domain when using type 'sendmail'

        'host' => $_ENV["MAIL_SMTP_HOST"], // for 'smtp' type only, otherwise remove or leave as is
        'user' => $_ENV["MAIL_SMTP_USER"], // for 'smtp' type only, otherwise remove or leave as is
        'pw'   => $_ENV["MAIL_SMTP_PW"], // for 'smtp' type only, otherwise remove or leave as is

        'port' => $_ENV["MAIL_SMTP_PORT"], // for 'smtp' type only, otherwise remove or leave as is
        'auth' => $_ENV["MAIL_SMTP_AUTH"], // for 'smtp' type only, change this to 'login' if you have problems with SMTP authentication
    ),
    'i18n' => array(
        'choice' => array(
            'en-US' => 'English',
            
            // More possible languages:
            // 'fr-FR' => 'Français',
            // 'hu-HU' => 'Magyar',
        ),

        'currency' => 'AUD',

        // The language is usually detected from the user's web browser.
        // If it cannot be detected automatically and there is no cookie from a manual language selection,
        // the following locale will be used as the default "fallback":
        'locale' => 'en-AU',
    ),
);
