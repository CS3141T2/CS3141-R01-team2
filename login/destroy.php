<?php
// Test page to destroy session data
session_start();
session_destroy();

header("Location: /login");