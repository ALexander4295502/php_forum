<?php

session_start();
require("DBconfig.php");
# Destroying All Sessions
if(session_destroy()) {
    header("Location: " . $config_maindir);
    exit;
}
?>