<?php
/**
 * Sage Pay Direct payment module Cards Accepted sidebox - Displays icons for Card Types accepted by
 * this store through the Sage Pay Direct payment module.
 *
 * @package templateSystem
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version $Id: sage_pay_direct_cards_accepted.php 385 2009-06-23 11:11:45Z Bob $
 */


require($template->get_template_dir('tpl_sage_pay_direct_cards_accepted.php', DIR_WS_TEMPLATE, $current_page_base, 'sideboxes'). '/tpl_sage_pay_direct_cards_accepted.php');

$title = MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_CARDS_ACCEPTED;
$title_link = false;

require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base, 'common') . '/' . $column_box_default);

?>