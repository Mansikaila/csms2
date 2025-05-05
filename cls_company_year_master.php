<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_companyyearmaster 
{                        
public $_company_year_id;          
    public $_company_id;          
    public $_company_year_type;          
    public $_start_date;          
    public $_end_date;          
    public $_created_date;          
    public $_created_by;          
    public $_modified_date;          
    public $_modified_by;          
    public $_transactionmode;
    
}

class bll_companyyearmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_companyyearmaster(); 
        $this->_dal =new dal_companyyearmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_company_year_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_company_year_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_company_year_master.php");
       }

    }
 
    public function fillModel()
    {
        global $_dbh;
        $this->_dal->fillModel($this->_mdl);
    
    
    }
     public function pageSearch()
    {
        global $_dbh;

        $sql="CAll csms1_search('t.start_date, t.end_date, t.company_year_id','tbl_company_year_master t')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Start Date <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Start Date\" /></th> 
                         <th> End Date <br><input type=\"text\" data-index=\"4\" placeholder=\"Search End Date\" /></th> 
                         </tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        
        $_grid.="<tr>
        <td> 
            <form  method=\"post\" action=\"frm_company_year_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"company_year_id\" value=\"".$_rs["company_year_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_company_year_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"company_year_id\" value=\"".$_rs["company_year_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
                                $fieldvalue = "";
                             if(!empty($_rs["start_date"])) {
                             $fieldvalue=date("d/m/Y",strtotime($_rs["start_date"]));
                             $fieldvalue.="<small> ".date("h:i:s a",strtotime($_rs["start_date"]))."</small>";
                             }
                             
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       
                             if(!empty($_rs["end_date"])) {
                             $fieldvalue=date("d/m/Y",strtotime($_rs["end_date"]));
                             $fieldvalue.="<small> ".date("h:i:s a",strtotime($_rs["end_date"]))."</small>";
                             }
                             
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"8\">No records available.</td>";
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
    function checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name) {
        global $_dbh;
        try {
            $sql="CAll csms1_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_default = $result->fetchColumn();
            return $is_default;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            return 0;
        }
        return 0;
    }
}
 class dal_companyyearmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_company_year_id);
        $_pre=$_dbh->prepare("CALL company_year_master_transaction (@p0,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_company_id);
        $_pre->bindParam(2,$_mdl->_company_year_type);
        $_pre->bindParam(3,$_mdl->_start_date);
        $_pre->bindParam(4,$_mdl->_end_date);
        $_pre->bindParam(5,$_mdl->_created_date);
        $_pre->bindParam(6,$_mdl->_created_by);
        $_pre->bindParam(7,$_mdl->_modified_date);
        $_pre->bindParam(8,$_mdl->_modified_by);
        $_pre->bindParam(9,$_mdl->_transactionmode);
        $_pre->execute();
        
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL company_year_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["company_year_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_company_year_id=$_rs[0]["company_year_id"];
        $_mdl->_company_id=$_rs[0]["company_id"];
        $_mdl->_company_year_type=$_rs[0]["company_year_type"];
        $_mdl->_start_date=$_rs[0]["start_date"];
        $_mdl->_end_date=$_rs[0]["end_date"];
        $_mdl->_created_date=$_rs[0]["created_date"];
        $_mdl->_created_by=$_rs[0]["created_by"];
        $_mdl->_modified_date=$_rs[0]["modified_date"];
        $_mdl->_modified_by=$_rs[0]["modified_by"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_companyyearmaster();

if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
if(isset($_POST["type"]) && $_POST["type"]=="ajax") {
    $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";
    if(isset($_POST["column_name"]))
        $column_name=$_POST["column_name"];
    if(isset($_POST["column_value"]))
        $column_value=$_POST["column_value"];
    if(isset($_POST["id_name"]))
        $id_name=$_POST["id_name"];
    if(isset($_POST["id_value"]))
        $id_value=$_POST["id_value"];
    if(isset($_POST["table_name"]))
        $table_name=$_POST["table_name"];
    echo $_bll->checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name);
    exit;
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
 
            
            if(isset($_REQUEST["company_year_id"]) && !empty($_REQUEST["company_year_id"]))
                $field=trim($_REQUEST["company_year_id"]);
            else {
                    $field=0;
           }
    $_bll->_mdl->_company_year_id=$field;

            
            if(isset($_REQUEST["company_id"]) && !empty($_REQUEST["company_id"]))
                $field=trim($_REQUEST["company_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_company_id=$field;

            
            if(isset($_REQUEST["company_year_type"]) && !empty($_REQUEST["company_year_type"]))
                $field=trim($_REQUEST["company_year_type"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_company_year_type=$field;

            
            if (isset($_REQUEST["start_date"]) && !empty($_REQUEST["start_date"])) {
    $field = trim($_REQUEST["start_date"]);
} else {
    $field = null;
}
$_bll->_mdl->_start_date = $field;

if (isset($_REQUEST["end_date"]) && !empty($_REQUEST["end_date"])) {
    $field = trim($_REQUEST["end_date"]);
} else {
    $field = null;
}
$_bll->_mdl->_end_date = $field;
            
            if(isset($_REQUEST["created_date"]) && !empty($_REQUEST["created_date"]))
                $field=trim($_REQUEST["created_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_date=$field;

            
            if(isset($_REQUEST["created_by"]) && !empty($_REQUEST["created_by"]))
                $field=trim($_REQUEST["created_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_by=$field;

            
            if(isset($_REQUEST["modified_date"]) && !empty($_REQUEST["modified_date"]))
                $field=trim($_REQUEST["modified_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_date=$field;

            
            if(isset($_REQUEST["modified_by"]) && !empty($_REQUEST["modified_by"]))
                $field=trim($_REQUEST["modified_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_by=$field;

            if(isset($_REQUEST["transactionmode"]))
                $tmode=$_REQUEST["transactionmode"];
            else
                $tmode="I";
            $_bll->_mdl->_transactionmode =$tmode;
        $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
