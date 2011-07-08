<?php
/**
 * Promotion class
 * Handles special promotion deals
 *
 * @author Jonathan Davis
 * @version 1.0
 * @copyright Ingenesis Limited, 2 September, 2008
 * @package shopp
 **/

class Promotion extends DatabaseObject {
	static $table = "promo";

	static $values = array(
		"Name" => "text",
		"Category" => "text",
		"Variation" => "text",
		"Price" => "price",
		"Sale price" => "price",
		"Type" => "text",
		"In stock" => "text",
		"Any item name" => "text",
		"Any item quantity" => "text",
		"Any item amount" => "price",
		"Total quantity" => "text",
		"Shipping amount" => "price",
		"Subtotal amount" => "price",
		"Promo use count" => "text",
		"Promo code" => "text",
		"Ship-to country" => "text",
		"Customer type" => "text"
	);

	function Promotion ($id=false) {
		$this->init(self::$table);
		if ($this->load($id)) return true;
		else return false;
	}

	function catalog_discounts () {
		$db = DB::get();

		$product_table = DatabaseObject::tablename(Product::$table);
		$price_table = DatabaseObject::tablename(Price::$table);
		// $catalog_table = DatabaseObject::tablename(Catalog::$table);
		// $category_table = DatabaseObject::tablename(ProductCategory::$table);

		$where_notdiscounted = array("0 = FIND_IN_SET($this->id,discounts)");
		$where = array();
		// Go through each rule to construct an SQL query
		// that gets all applicable product & price ids
		if (!empty($this->rules) && is_array($this->rules)) {
			foreach ($this->rules as $rule) {

				if (Promotion::$values[$rule['property']] == "price")
					$value = floatvalue($rule['value']);
				else $value = $rule['value'];

				switch($rule['logic']) {
					case "Is equal to": $match = "='$value'"; break;
					case "Is not equal to": $match = "!='$value'"; break;
					case "Contains": $match = " LIKE '%$value%'"; break;
					case "Does not contain": $match = " NOT LIKE '%$value%'"; break;
					case "Begins with": $match = " LIKE '$value%'"; break;
					case "Ends with": $match = " LIKE '%$value'"; break;
					case "Is greater than": $match = "> $value"; break;
					case "Is greater than or equal to": $match = ">= $value"; break;
					case "Is less than": $match = "< $value"; break;
					case "Is less than or equal to": $match = "<= $value"; break;
				}

				switch($rule['property']) {
					case "Name":
						$where[] = "p.name$match";
						$joins[$product_table] = "INNER JOIN $product_table as p ON prc.product=p.id";
						break;
					case "Category":
						$where[] = "tm.name$match";
						global $wpdb;
						$joins[$wpdb->term_relationships] = "INNER JOIN $wpdb->term_relationships AS tr ON (prc.product=tr.object_id)";
						$joins[$wpdb->term_taxonomy] = "INNER JOIN $wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id=tt.term_taxonomy_id)";
						$joins[$wpdb->terms] = "INNER JOIN $wpdb->terms AS tm ON (tm.term_id=tt.term_id)";

						// $joins[$catalog_table] = "LEFT JOIN $catalog_table AS catalog ON catalog.product=prc.product";
						// $joins[$category_table] = "LEFT JOIN $category_table AS cat ON catalog.parent=cat.id AND catalog.type='category'";
						break;
					case "Variation": $where[] = "prc.label$match"; break;
					case "Price": $where[] = "prc.price$match"; break;
					case "Sale price": $where[] = "(prc.onsale='on' AND prc.saleprice$match)"; break;
					case "Type": $where[] = "prc.type$match"; break;
					case "In stock": $where[] = "(prc.inventory='on' AND prc.stock$match)"; break;
				}

			}

		}

		if (!empty($where)) $where = "WHERE ".join(" AND ",$where);
		else $where = false;

		if (!empty($joins)) $joins = join(' ',$joins);
		else $joins = false;

		// Find all the pricetags the promotion is *currently assigned* to
		$query = "SELECT id FROM $price_table WHERE 0 < FIND_IN_SET($this->id,discounts)";
		$results = $db->query($query,AS_ARRAY);
		$current = array_map(create_function('$o', 'return $o->id;'), $results);

		// Find all the pricetags the promotion is *going to apply* to
		$query = "SELECT prc.id,prc.product,prc.discounts FROM $price_table AS prc
					$joins
					$where";

		$results = $db->query($query,AS_ARRAY);
		$updates = array_map(create_function('$o', 'return $o->id;'), $results);

		// Determine which records need promo added to and removed from
		$added = array_diff($updates,$current);
		$removed = array_diff($current,$updates);

		// Add discounts to specific rows
		$query = "UPDATE $price_table
					SET discounts=CONCAT(discounts,IF(discounts='','$this->id',',$this->id'))
					WHERE id IN (".join(',',$added).")";
		if (!empty($added)) $db->query($query);

		// Remove discounts from pricetags that now don't match the conditions
		if (!empty($removed)) $this->uncatalog_discounts($removed);

		// Recalculate product stats for the products with pricetags that have changed
		$Collection = new PromoProducts(array('id' => $this->id));
		$Collection->pagination = false;
		$Collection->load( array('load'=>array('prices','restat')) );
	}

	function uncatalog_discounts ($pricetags) {
		$db =& DB::get();
		$_table = DatabaseObject::tablename(Price::$table);
		if (empty($pricetags)) return;

		$discounted = $db->query("SELECT id,discounts,FIND_IN_SET($this->id,discounts) AS offset FROM $_table WHERE id IN ('".join(',',$pricetags)."')",AS_ARRAY);

		foreach ($discounted as $index => $pricetag) {
			$promos = explode(',',$pricetag->discounts);
			array_splice($promos,($offset-1),1);
			$db->query("UPDATE LOW_PRIORITY $_table SET discounts='".join(',',$promos)."' WHERE id=$pricetag->id");
		}
	}

	/**
	 * match_rule ()
	 * Determines if the value of a given subject matches the rule based
	 * on the specified operation */
	function match_rule ($subject,$op,$value,$property=false) {
		switch($op) {
			// String or Numeric operations
			case "Is equal to":
			 	if($property && Promotion::$values[$property] == 'price') {
					return ( floatvalue($subject) != 0
					&& floatvalue($value) != 0
					&& floatvalue($subject) == floatvalue($value));
				} else {
					if (is_array($subject)) return (in_array($value,$subject));
					return ("$subject" === "$value");
				}
				break;
			case "Is not equal to":
				if (is_array($subject)) return (!in_array($value,$subject));
				return ("$subject" !== "$value"
						|| (floatvalue($subject) != 0
						&& floatvalue($value) != 0
						&& floatvalue($subject) != floatvalue($value)));
						break;

			// String operations
			case "Contains":
				if (is_array($subject)) {
					foreach ($subject as $s)
						if (stripos($s,$value) !== false) return true;
					return false;
				}
				return (stripos($subject,$value) !== false); break;
			case "Does not contain":
				if (is_array($subject)) {
					foreach ($subject as $s)
						if (stripos($s,$value) !== false) return false;
					return true;
				}
				return (stripos($subject,$value) === false); break;
			case "Begins with":
				if (is_array($subject)) {
					foreach ($subject as $s)
						if (stripos($s,$value) === 0) return true;
					return false;
				}
				return (stripos($subject,$value) === 0); break;
			case "Ends with":
				if (is_array($subject)) {
					foreach ($subject as $s)
						if (stripos($s,$value) === strlen($s) - strlen($value)) return true;
					return false;
				}
				return  (stripos($subject,$value) === strlen($subject) - strlen($value)); break;

			// Numeric operations
			case "Is greater than":
				return (floatvalue($subject,false) > floatvalue($value,false));
				break;
			case "Is greater than or equal to":
				return (floatvalue($subject,false) >= floatvalue($value,false));
				break;
			case "Is less than":
				return (floatvalue($subject,false) < floatvalue($value,false));
				break;
			case "Is less than or equal to":
				return (floatvalue($subject,false) <= floatvalue($value,false));
				break;
		}

		return false;
	}

	/**
	 * Records when a specific promotion is used
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @param array $promos A list of Promotion ids of the promotions to be updated
	 * @return void
	 **/
	function used ($promos) {
		$db =& DB::get();
		if (empty($promos) || !is_array($promos)) return;
		$table = DatabaseObject::tablename(self::$table);
		$db->query("UPDATE LOW_PRIORITY $table SET uses=uses+1 WHERE 0 < FIND_IN_SET(id,'".join(',',$promos)."')");
	}

	static function activedates () {

		// By default the promotion editor will save a value of 1
		// for the start and end dates if no date values are provided.
		// We can evaluate in SQL if the dates are set by checking
		// if they are more or less than the default. However, we
		// wse an offset amount as a buffer to account for how
		// MySQL's UNIX_TIMESTAMP() converts the datetime to a
		// UTC-based timestamp from the Jan 1, 1970 00:00:00 epoch
		// 43200 to represents 12-hours (UTC +/- 12 hours), then we
		// add 1 to account for the default amount set in the editor
		$offset = 43200 + 1;

		return "(
		    -- Promo is not date based
		    (
		        UNIX_TIMESTAMP(starts) <= $offset
		        AND
		        UNIX_TIMESTAMP(ends) <= $offset
		    )
		    OR
		    -- Promo has start and end dates, check that we are in between
		    (
		        UNIX_TIMESTAMP(starts) > $offset
		        AND
		        UNIX_TIMESTAMP(ends) > $offset
		        AND
		        (".time()." BETWEEN UNIX_TIMESTAMP(starts) AND UNIX_TIMESTAMP(ends))
		    )
		    OR
		    -- Promo has _only_ a start date, check that we are after it
		    (
		        UNIX_TIMESTAMP(starts) > $offset
		        AND
		        UNIX_TIMESTAMP(ends) <= $offset
		        AND
		        UNIX_TIMESTAMP(starts) < ".time()."
		    )
		    OR
		    -- Promo has _only_ an end date, check that we are before it
		    (
		        UNIX_TIMESTAMP(starts) <= $offset
		        AND
		        UNIX_TIMESTAMP(ends) > $offset
		        AND
		        ".time()." < UNIX_TIMESTAMP(ends)
			)
	    )";
	}

	function duplicate () {
		$Promotion = new Promotion();
		$Promotion->copydata($this);
		$Promotion->name = sprintf(__('%s copy','Shopp'),$Promotion->name);
		$Promotion->status = 'disabled';
		$Promotion->uses = 0;
		$Promotion->created = null;
		$Promotion->modified = null;
		$Promotion->save();
	}

	/**
	 * Deletes an entire set of promotions
	 *
	 * @author Jonathan Davis
	 * @since 1.2
	 *
	 * @param array $ids List of promotion IDs to delete
	 * @return boolean Success/fail
	 **/
	static function deleteset ($ids) {
		if (empty($ids) || !is_array($ids)) return false;
		$table = DatabaseObject::tablename(self::$table);
		DB::query("DELETE FROM $table WHERE id IN (".join(',',$ids).")");
		return true;
	}

	/**
	 * Enable an entire set of promotions
	 *
	 * @author Jonathan Davis
	 * @since 1.2
	 *
	 * @param array $ids List of promotion IDs to enable
	 * @return boolean Success/fail
	 **/
	static function enableset ($ids) {
		if (empty($ids) || !is_array($ids)) return false;
		$table = DatabaseObject::tablename(self::$table);
		DB::query("UPDATE $table SET status='enabled' WHERE id IN (".join(',',$ids).")");
		return true;
	}

	/**
	 * Disables an entire set of promotions
	 *
	 * @author Jonathan Davis
	 * @since 1.2
	 *
	 * @param array $ids List of promotion IDs to disable
	 * @return boolean Success/fail
	 **/
	static function disableset ($ids) {
		if (empty($ids) || !is_array($ids)) return false;
		$table = DatabaseObject::tablename(self::$table);
		DB::query("UPDATE $table SET status='disabled' WHERE id IN (".join(',',$ids).")");
		return true;
	}


} // END clas Promotion

?>