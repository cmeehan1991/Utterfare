<?php 
$whitelist = array(
    '127.0.0.1',
    '::1',
);

if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
	define('PROTOCOL', 'https://');
}else{
	define('PROTOCOL', 'http://');
}
DEFINE('BASE_URL', PROTOCOL . $_SERVER['SERVER_NAME']);
DEFINE('VENDOR_URL',  PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
DEFINE('VENDOR', PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'includes/classes/vendor.php');
DEFINE('USER_ID', $_SESSION['user_id']);

DEFINE('USER_SIGNED_IN',  $_SESSION['UF_USER_SIGNED_IN']);
DEFINE('VENDOR_SIGNED_IN',  $_SESSION['UF_VENDER_SIGNED_IN']);
DEFINE('UF_VENDOR_USER_SIGNED_IN',  $_SESSION['UF_VENDOR_USER_SIGNED_IN']);
DEFINE('UF_VENDOR_ID', $_SESSION['UF_VENDOR_ID']);
DEFINE('VENDOR_USER_ID', $_SESSION['UF_VENDOR_USER_ID']);
?>

<script type="text/javascript">
	var vendor_class_url = '<?php echo VENDOR ?>';
	var vendor_url = '<?php echo VENDOR_URL?>';
	var session = '<?php echo json_encode($_SESSION); ?>';
</script>