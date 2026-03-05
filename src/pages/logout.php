<?php
session_start();
session_destroy();
header('Location: /shoe-repair/');
exit();
?>