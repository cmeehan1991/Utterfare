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
DEFINE('BASE_URL',  PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
DEFINE('SEARCH_URL', PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'includes/php/search.php');
DEFINE('SINGLE_URL', PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'includes/php/single-item.php');
DEFINE('USER_URL', PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'includes/php/user.php');
DEFINE('USER_ID', $_SESSION['user_id']);
DEFINE('SIGNED_IN',  $_SESSION['IS_SIGNED_IN']);
?>

<script type="text/javascript">
	var search_url = '<?php echo SEARCH_URL ?>';
	var base_url = '<?php echo BASE_URL ?>';
	var single_item_url = '<?php echo SINGLE_URL?>';
	var user_url = '<?php echo USER_URL?>';
</script>