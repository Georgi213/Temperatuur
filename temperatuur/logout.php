<?php
session_start();
session_destroy();
header("location: lisamine.php");
