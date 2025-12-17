<?php
define ("APP Name","Mental Health Platform");
define('Base URL','http://localhost/mhsp');

//roles
define('ROLE_USER', 'user');
define('Role_counsellor', 'counsellor');
define ('Role_admin','admin');

// Post settings
define('POST_STATUS_ACTIVE', 1);
define('POST_STATUS_DELETED', 0);

// Security limits
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_TIMEOUT', 1860); // 31 minutes

// File upload limits
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // max size 10 MB

?>