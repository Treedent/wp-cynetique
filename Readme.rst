# Cynetique
A wordpress plugin with oauth2 implementation for REST data request.

You will have to add php sessions start at the top of your wp-config.php :


.. code-block:: php
    // Start session for the rest server token persistance
    if ( ! session_id() ) {
        session_start();
    }

    define('WP_HOME','http://www....');
    define('WP_SITEURL','http://www...');