<?php

/**
 *
 * Advanced Smart Tags - activated/de-activated in Zencart Admin
 *
 */

/* only activate advanced tags if you really know what you are doing, and understand regular expressions. Disable if things go awry.
 * If you wish to add your own smart-tags below, please ensure that you understand the following:
 * 1) ensure that the expressions you use avoid repetitive behaviour from one upload to the next using existing data, as you may end up with this sort of thing:
 *   <b><b><b><b>thing</b></b></b></b> ...etc for each update. This is caused for each output that qualifies as an input for any expression..
 * 2) remember to place the tags in the order that you want them to occur, as each is done in turn and may remove characters you rely on for a later tag
 * 3) the default $smart_tags array is the last to be executed, so you have all of your carriage-returns and line-breaks to play with below
 * 4) make sure you escape the following metacharacters if you are using them as string literals: ^  $  \  *  +  ?  (  )  |  .  [  ]  / etc..
 * The following examples should get your blood going... comment out those you do not want after enabling Advanced Smart Tags in admin
 * for regex help see: http://www.quanetic.com/regex.php or http://www.regular-expressions.info
*/

function ep_advanced_smart_tags()
{
  return array(
              /* replaces "Description:" at beginning of new lines with <br /> and same in bold */
              "\r\nDescription:|\rDescription:|\nDescription:" => '<br /><b>Description:</b>',

              /* replaces at beginning of description fields "Description:" with same in bold */
              "^Description:" => '<b>Description:</b>',

              /*
              just make "Description:" bold wherever it is...must use both lines to prevent duplicates!
              "<b>Description:<\/b>" => 'Description:',
              "Description:" => '<b>Description:</b>',
              /*

              /* replaces "Specification:" at beginning of new lines with <br /> and same in bold. */
              "\r\nSpecifications:|\rSpecifications:|\nSpecifications:" => '<br /><b>Specifications:</b>',

              /* replaces at beginning of descriptions "Specifications:" with same in bold */
              "^Specifications:" => '<b>Specifications:</b>',

              /*
              just make "Specifications:" bold wherever it is...must use both lines to prevent duplicates!
              "<b>Specifications:<\/b>" => 'Specifications:',
              "Specifications:" => '<b>Specifications:</b>',
              */

              /* replaces in descriptions any asterisk at beginning of new line with a <br /> and a bullet. */
              "\r\n\*|\r\*|\n\*" => '<br />&bull;',

              /* replaces in descriptions any asterisk at beginning of descriptions with a bullet. */
              "^\*" => '&bull;',

              /*
              returns/newlines in description fields replaced with space, rather than <br /> further below
              "\r\n|\r|\n" => ' ',
              */

              /* the following should produce paragraphs between double breaks, and line breaks for returns/newlines */
              "^<p>" => '', /* this prevents duplicates */
              "^" => '<p>',
              //"^<p style=\"desc-start\">" => '', /* this prevents duplicates */
              /* "^" => '<p style="desc-start">', */
              "<\/p>$" => '', /* this prevents duplicates */
              "$" => '</p>',
              "\r\n\r\n|\r\r|\n\n" => '</p><p>',
              /* if not using the above 5(+2) lines, use the line below instead.. */
              /* "\r\n\r\n|\r\r|\n\n" => '<br /><br />', */
              "\r\n|\r|\n" => '<br />',

              /* ensures "Description:" followed by single <br /> is followed by double <br /> */
              "<b>Description:<\/b><br \/>" => '<br /><b>Description:</b><br /><br />',
              );
}