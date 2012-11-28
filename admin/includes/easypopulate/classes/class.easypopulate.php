<?php
/*
Must control table processing.
All tables must be treated identically. Native support for multi language, and default cart consequences. IE if
new category in multi lang cart and one lang in upload, duplicate for all languages. Same for product?
Process multi lang tables using lang id as column lang id.
Tables class. Each table should be instantiated by the class. Set multi-language property (true/false) on instantiation
eg. $ep_products = new epTable('products', false);
eg. $ep_products_description = new epTable('products_descriptions', true);
*/
class epLayout
{
	/**
	 * Layout class
	 * Uploads: determines minimum requirements/mode & populates required defaults where needed
	 *
	 * Downloads: Controls final download depending on users choice (ad hoc & saved profile)
	 *
	 */
  /***
   * array of affected tables
   * @type array
   ***/
	var $tables;
  /***
   * array of language ids for inclusion
   * @type array
   ***/
	var $limitLanguages;
  /***
   * array of categories for inclusion
   * @type array
   ***/
	var $limitCategory;

  /***
   * Construct a new <code>ArrayIterator</code> for an array
   * @param $array the array to create an iterator for
   ***/
	function epLayout()
	{
	}
}
class epTables
{
}
/**
 * What to achieve from this?
 * provide means of retrieving path (names just default lang, or array of category ids?) for any category
 * provide means of matching path to existing product - Needed to move/delete product/link, and detect if not move but new path (linked or error).
 * 
 * Need to - add categories, delete categories, rename categories, descriptions, move categories
 * 
 */
class EpCategories
{
	var $pile = array();

	var $catMax;

	var $catPaths = array();

	function EpCategories()
	{
		global $language_id_default;
		$this->CreateCatPaths($language_id_default);
	}
	/**
	 * calculate maximum category depth in store
	 * can this be done in conjunction with other methods?
	 * this is now redundant..
	 */
	function CalcCatDepth($top_categories = NULL)
	{
		$this->pile[0] = 1;
		$pid_rh =& ep_query('SELECT parent_id FROM ' . TABLE_CATEGORIES);
		for ($i = 0; $row = $pid_rh->getRow($i, ECLIPSE_DB_ASSOC); $i++)
		{
			if (!isset($this->pile[$row['parent_id']]))
			{
				$parent_id = $row['parent_id'];
    		$path = array();
    		$ii = 0;
    		while (!isset($this->pile[$parent_id]))
    		{
    			$step_count[$parent_id] = $ii++;
    			$new_pid_rh =& ep_query('SELECT parent_id FROM ' . TABLE_CATEGORIES . ' WHERE categories_id = ' . (int)$parent_id);
    			$row2 = $new_pid_rh->getRow(0, ECLIPSE_DB_ASSOC);
    			$parent_id = $row2['parent_id'];
    		}
    		$ii++;
    		foreach ($step_count as $cat_id => $delta)
    		{
    			$this->pile[$cat_id] = $ii - $delta + $this->pile[$parent_id];
    		}
		  }
		}
		arsort($this->pile);
		$this->catMax = array_shift($this->pile);
		unset($this->pile);
	}

	function CreateCatPaths($language_id_default)
	{
	  $this->catPaths['0'] = '';
	  $cat_result =& ep_query('SELECT c.*, cd.* FROM ' . TABLE_CATEGORIES . ' as c LEFT JOIN ' . TABLE_CATEGORIES_DESCRIPTION . ' as cd ON c.categories_id = cd.categories_id AND cd.language_id = ' . $language_id_default . ' ORDER BY c.parent_id, c.sort_order, c.categories_id');
	  for ($i = 0; $categories = $cat_result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
	  {
      $this->catPaths[$categories['categories_id']] = ltrim($this->catPaths[$categories['parent_id']] . EASYPOPULATE_CONFIG_CATEGORIES_PATH_SEPARATOR . $categories['categories_name'], EASYPOPULATE_CONFIG_CATEGORIES_PATH_SEPARATOR);
	  }
	}
	/**
	 * Create new category
	 * Uses default name for all langs unless supplied in $cat_desc_fields array
	 * only inserts supplied vals - database defaults for rest...
	 */
	function CreatNewCat($parent_id, $categories_name, $cat_fields = NULL, $cat_desc_fields = NULL)
	{
	  global $language_id_default, $ep_languages;
	  $cat_exist_result =& ep_query('SELECT * FROM ' . TABLE_CATEGORIES . ' WHERE categories_id = ' . (int)$parent_id);
	  if (ep_empty($categories_name))
	  {
	    return false;
	  }
	  else if ($cat_exist_result->getRowCount() === 0 && $parent_id != '0')
	  {

	    return false;
	  }
	  else
	  {
	    $categories_sql = array();
	    $categories_description_sql = array();
	    $categories_sql['parent_id'] = $parent_id;
	    $categories_sql['date_added'] = 'CURRENT_TIMESTAMP';
	    $categories_sql['categories_id'] = ep_get_next_id(TABLE_CATEGORIES);

	    if (isset($cat_fields))
	    {
  	    $categories_sql['categories_status'] = '1';
  	    $categories_sql['sort_order'] = '0';

	      foreach ($cat_fields as $name => $val)
	      {
	        $categories_sql[$name] = $val;
	      }
  	    if ($categories_sql['categories_status'] != '1' && $categories_sql['categories_status'] != '0')
  	      $categories_sql['categories_status'] = '0';
  	    if(!is_numeric($categories_sql['sort_order']))
  	      $categories_sql['sort_order'] = '0';
	    }
	    foreach ($ep_languages as $lid => $array)
	    {
	      $categories_description_sql[$lid]['categories_name'] = $categories_name;
	      $categories_description_sql[$lid]['categories_id'] = $categories_sql['categories_id'];
	      $categories_description_sql[$lid]['language_id'] = $lid;
	    }
	    if (isset($cat_desc_fields))
	    {
	      foreach ($cat_desc_fields as $lid => $array)
	      {
	        foreach ($array as $name => $val)
	        {
	          $categories_description_sql[$lid][$name] = $val;
	        }
	      }
	    }
	    $categories_description_sql[$language_id_default]['categories_name'] = $categories_name;
      $sql_fields = '';
      $sql_vals = '';
    	foreach ($categories_sql as $name => $val)
      {
        $sql_fields .= $name . ',';
        $sql_vals .= ep_db_input($val) . ',';
      }
      $cat_insert_result =& ep_query('INSERT INTO ' . TABLE_CATEGORIES . '(' . rtrim($sql_fields, ',') . ') VALUES (' . rtrim($sql_vals, ',') . ')');
      if ($cat_insert_result->isSuccess())
      {
  	    foreach ($ep_languages as $lid => $array)
  	    {
      	  $sql_fields = '';
          $sql_vals = '';
        	foreach ($categories_description_sql[$lid] as $name => $val)
          {
            $sql_fields .= $name . ',';
            $sql_vals .= ep_db_input($val) . ',';
          }
  	      ep_query('INSERT INTO ' . TABLE_CATEGORIES_DESCRIPTION . '(' . rtrim($sql_fields, ',') . ') VALUES (' . rtrim($sql_vals, ',') . ')');
  	    }
        return $categories_sql['categories_id'];
      }
      else
      {
        return false;
      }
	  }
	}
	function CatPathsFlip()
	{
	  $this->catPaths = array_flip($this->catPaths);
	}

	/**
	 * Interface
	 */

	function GetCatMax()
	{
		return $this->catMax;
	}

	function SetCatMax($catMax)
	{
		$this->catMax = $catMax;
	}
	function GetCatPaths()
	{
	  return $this->catPaths;
	}
}
