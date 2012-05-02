<?php
class gp_legacy {
	
	public function __construct(){
		$this->connect = mssql_connect('10.179.138.7', 'greenpagesAdmin', 'greenocean');
		$this->db = mssql_select_db('greenpagesaustralia_com_au', $this->connect);
	}

	public function getDirectoryPages($old_crm_id) {
		$mssql = "
			SELECT 
				[crm_index_vs_listing].[ivl_listingid],
				[crm_index_vs_listing].[ivl_indexid],
				[LINKS].[LINK_NAME],
				LINK_EXPIRED = DATEDIFF(s, '19700101', [LINKS].[LINK_EXPIRED]),
				d2.[index_title] AS d2,
				d3.[index_title] AS d3
  			FROM 
  				[greenpagesaustralia_com_au].[greenpagesauAdmin].[crm_index_vs_listing]
  			INNER JOIN 
  				[greenpagesaustralia_com_au].[greenpagesauAdmin].[crm_index] AS d3
  			ON 
  				d3.[index_id] = [crm_index_vs_listing].[ivl_indexid]
  			INNER JOIN 
  				[greenpagesaustralia_com_au].[dbo].[LINKS]
  			ON 
  				[LINKS].[LINK_ID] = [crm_index_vs_listing].[ivl_listingid]
  			LEFT OUTER JOIN 
  				[greenpagesaustralia_com_au].[greenpagesauAdmin].[crm_index] AS d2
  			ON
  				d3.[index_heirachy] = d2.[index_id]
  			WHERE 
  				[crm_index_vs_listing].[ivl_listingid] = {$old_crm_id}
  			AND
  				d3.[index_active] = 1
  			AND
  				d3.[index_regionid] = 1
  			AND
  				[LINKS].[LINK_APPROVED] = 1
  			AND
  				[LINKS].[link_country] = 'AU'
  			;
  		";
		
		$msresult = mssql_query($mssql, $this->connect);
		
		$pages = array();
		while ($row = mssql_fetch_array($msresult)) {
			if (!array_key_exists('listing_title', $pages)) {
				$pages["listing_title"] = $row['LINK_NAME'];
			}
			
			if (!array_key_exists('listing_expired', $pages)) {
				if ($row['LINK_EXPIRED'] == NULL || time() < $row['LINK_EXPIRED'] ) {
					$pages["listing_expired"] = false;
				} else {
					$pages["listing_expired"] = $row['LINK_EXPIRED'];
				}
			}
			
			$pages[] = array(
				"directory_path" => "/index.asp?page_id=105&id={$row['ivl_indexid']}&company_id={$row['ivl_listingid']}",
				"directory_trail" => array($row['d2'], $row['d3'])
			);
		}
		
		return $pages;
	}
	
}
?>