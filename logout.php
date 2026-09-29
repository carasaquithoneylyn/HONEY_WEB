

<?php 

//ABADIES SOLUTIONS

require_once 'include/initialize.php';
// Four steps to closing a session
// (i.e. logging out)

// 1. Find the session
@session_start();

// 2. Unset all the session variables
// unset( $_SESSION['USERID'] );
// unset( $_SESSION['FULLNAME'] );
// unset( $_SESSION['USERNAME'] );
// unset( $_SESSION['PASS'] );
// unset( $_SESSION['ROLE'] );
 

unset( $_SESSION['UID'] );
unset( $_SESSION['DISPLAYNAME'] );
unset( $_SESSION['USERNAME'] );
unset( $_SESSION['TYPE'] );
// 4. Destroy the session
// session_destroy();
?>
<script language="javascript">
	window.location.href = "login.php?logout=1"
</script>
              <?php
//redirect("login.php?logout=1");
?>
