<?php
  require('includes/application_top.php');

$_SESSION['cart']->actionAddProductAjaxAttributes($goto, $parameters);

include(DIR_WS_TEMPLATES . $template_dir .'/sideboxes/tpl_shopping_cart_ajax.php');

  echo $content;

?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
