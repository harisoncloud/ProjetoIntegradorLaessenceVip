<?php
class ModelCatalogMagic360Gallery extends Model {
	public function getRow($product_id) {
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "magic360images (
            `product_id` INT(11) NOT NULL ,
            `images` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `columns` VARCHAR(10) NOT NULL,
            PRIMARY KEY(`product_id`)
        )");
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "magic360images WHERE product_id = " . (int)$product_id);
        
        $data = array();
        foreach ($query->rows as $result) {
            $data = array(
                'images' => $result['images'],
                'columns' => $result['columns']
            );
        }
        return $data;
	}
	
	public function updateRow($product_id, $images, $columns){
        $this->db->query("UPDATE " . DB_PREFIX . "magic360images SET images = '" . $images . "', columns = '" . abs(intval($columns)) . "' WHERE product_id = '" . (int)$product_id . "'");
        return 'updated';
	}
	
	public function deleteRow($product_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "magic360images WHERE product_id = " . (int)$product_id );
        return 'deleted';
    }
    
    public function addRow($product_id, $images, $columns){
        $this->db->query("INSERT INTO " . DB_PREFIX . "magic360images (`product_id`, `images`, `columns`) VALUES('" . (int)$product_id . "','" . $images . "','" . abs(intval($columns)) . "')");
        return 'added';
    }

    public function checkRow($product_id){
        $result = $this->db->query("SELECT 1 FROM " . DB_PREFIX . "magic360images WHERE product_id = " . (int)$product_id);
        if($result->num_rows) return TRUE;

        return FALSE;
    }
}