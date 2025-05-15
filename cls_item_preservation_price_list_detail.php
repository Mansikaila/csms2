<?php
include_once(__DIR__ . "/../config/connection.php");

class mdl_itempreservationpricelistdetail 
{
    public $item_preservation_price_list_detail_id;     
    public $item_preservation_price_list_id;     
    public $packing_unit_id;     
    public $rent_per_qty_month;     
    public $rent_per_qty_season;
    public $item_id;
    public $detailtransactionmode;
}

class dal_itempreservationpricelistdetail
{
    public function dbTransaction($mdl)
    {
        global $_dbh;

        try {
            $_dbh->exec("SET @p0 = " . ($mdl->item_preservation_price_list_detail_id ?? 'NULL'));
            $_pre = $_dbh->prepare("CALL item_preservation_price_list_detail_transaction (@p0,?,?,?,?,?)");

            $_pre->bindParam(1, $mdl->item_preservation_price_list_id);
            $_pre->bindParam(2, $mdl->packing_unit_id);
            $_pre->bindParam(3, $mdl->rent_per_qty_month);
            $_pre->bindParam(4, $mdl->rent_per_qty_season);
            $_pre->bindParam(5, $mdl->detailtransactionmode);

            $_pre->execute();
        } catch (PDOException $e) {
            error_log("Error saving detail record: " . $e->getMessage());
            throw $e;
        }
    }
}

class bll_itempreservationpricelistdetail
{
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl = new mdl_itempreservationpricelistdetail(); 
        $this->_dal = new dal_itempreservationpricelistdetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
    }

    public function pageSearch()
    {
        global $_dbh;

        $_grid = "
        <div id=\"gridContainer\" class=\"table-responsive\" style=\"width: 100%; display: block;\">
            <table id=\"dataGrid\" class=\"table table-bordered table-striped text-center align-middle\">
                <thead class=\"thead-dark\">
                    <tr>
                        <th>Packing Unit Name</th>
                        <th>Rent/Month/Qty</th>
                        <th>Rent/Season/Qty</th>
                    </tr>
                </thead>
                <tbody id=\"gridBody\">";

        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

        if ($item_id <= 0) {
            $_grid .= "<tr><td colspan=\"3\">No data available. Please select an item.</td></tr></tbody></table></div>";
            echo $_grid;
            return;
        }

        try {
            $sql = "SELECT 
                        pum.packing_unit_id, 
                        pum.packing_unit_name, 
                        COALESCE(ippl.rent_per_qty_month, '0.00') AS rent_per_qty_month, 
                        COALESCE(ippl.rent_per_qty_season, '0.00') AS rent_per_qty_season
                    FROM 
                        tbl_packing_unit_master pum
                    LEFT JOIN (
                        SELECT 
                            d.packing_unit_id, 
                            d.rent_per_qty_month, 
                            d.rent_per_qty_season
                        FROM 
                            tbl_item_preservation_price_list_detail d
                        INNER JOIN 
                            tbl_item_preservation_price_list_master m 
                            ON d.item_preservation_price_list_id = m.item_preservation_price_list_id
                        WHERE m.item_id = :item_id
                    ) ippl ON pum.packing_unit_id = ippl.packing_unit_id
                    WHERE pum.status = 1";

            $stmt = $_dbh->prepare($sql);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($result)) {
                foreach ($result as $_rs) {
                    $_grid .= "
                        <tr data-id=\"{$_rs['packing_unit_id']}\">
                            <td>{$_rs['packing_unit_name']}</td>
                            <td contenteditable=\"true\" class=\"editable rent-monthly\" data-field=\"rent_per_qty_month\">{$_rs['rent_per_qty_month']}</td>
                            <td contenteditable=\"true\" class=\"editable rent-seasonal\" data-field=\"rent_per_qty_season\">{$_rs['rent_per_qty_season']}</td>
                        </tr>";
                }
            } else {
                $_grid .= "<tr><td colspan=\"3\">No packing units found for the selected item.</td></tr>";
            }

        } catch (PDOException $e) {
            error_log("Error in pageSearch: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error fetching data.</div>";
            return;
        }

        $_grid .= "</tbody></table></div>";
        echo $_grid;
    }
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action === 'fetch_units' && isset($_POST['item_id'])) {
        $bll = new bll_itempreservationpricelistdetail();
        $bll->pageSearch();
        exit;
    }
}
?>
