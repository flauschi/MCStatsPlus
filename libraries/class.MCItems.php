<?php
class MCItems {
	protected $reg;

	protected $items;
	protected $itemsByStatsId;
	protected $itemsByShopId;

	public function __construct() {
		$this->reg = Registry::instance();
		
		$this->initItems();
	}
	
	public function __destruct() {
	
	}
	
	protected function initItems() {
		$this->items = array();
		$this->itemsByStatsId = array();
		$this->itemsByShopId = array();

		$sql = "SELECT * FROM `" . $this->reg->dbPrefix . "MCSPMCItems`;";
		$results = $this->reg->db->dbResult($sql);

		foreach( $results as $result ) {
			$this->items[(integer)$result->itemId] = array(
				'type' => (string)$result->type,
				'version' => (string)$result->version,

				'isDestroyable' => ((integer)$result->isDestroyable == 1) ? true: false,
				'isCreateable' => ((integer)$result->isCreateable == 1) ? true: false,
				'isPickupable' => ((integer)$result->isPickupable == 1) ? true: false,
				'isDroppable' => ((integer)$result->isDroppable == 1) ? true: false,

				'statsId' => (string)$result->statsId,
				'shopId' => (string)$result->shopId
			);
			
			$this->itemsByStatsId[(integer)$result->statsId] = &$this->items[(integer)$result->itemId];			
			$this->itemsByShopId[(integer)$result->shopId] = &$this->items[(integer)$result->itemId];
		}
	}
	
	
}
?>