<?php
/*
Easy Populate user overrides - override variables where needed.

In some instances, you will encounter issues such as an unrecognised currency symbol that Excel is inserting into your prices, or you have an obscure text delimiter not recognised by Easy Populate.

The good news is that you can set them here! Just change 'false' to 'true' for each appropriate variable below and edit them to suit your needs. The values below are the defaults already in use, and are not required to be activated.

If it breaks your Easy Populate, just make them false again, or comment out by putting // at the beginning of the line.
*/

/* Importing */

/* used to detect type of text delimiter in your file */
if ( false ) $ep_overrides['text_delimiters'] = array("'" => '', "\"" => '');

/* used to strip erroneous currency symbols from prices in your file */
if ( false ) $ep_overrides['currency_symbols'] = " $£¥€";

/* Mac users may have problems with Easy Populate recognising file rows.
Mac users may need to use: $ep_overrides['linebreak'] = "\r"; and set to: if ( true ) */
if ( false ) $ep_overrides['linebreak'] = "\n";
