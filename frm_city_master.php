<?php
    include("classes/cls_city_master.php");
    include("include/header.php");
?>
 <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
<?php
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="";
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {    
        $_bll->fillModel();
        $label="Update";
    } else {
        $label="Add";
    }?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?php echo $label; ?> Data
        </h1>
        <ol class="breadcrumb">
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="srh_city_master.php"><i class="fa fa-dashboard"></i> City Master</a></li>
          <li class="active"><?php echo $label; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_city_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">
    <?php
            global $database_name;
            global $_dbh;
            $hidden_str="";
            $table_name="tbl_city_master";
            $lbl_array=array();
            $field_array=array();
            $err_array=array();
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    if($generator_options) {
                        $fields_names=$generator_options->field_name;
                        $fields_types=$generator_options->field_type;
                        $field_scale=$generator_options->field_scale;
                        $dropdown_table=$generator_options->dropdown_table;
                        $label_column=$generator_options->label_column;
                        $value_column=$generator_options->value_column;
                        $where_condition=$generator_options->where_condition;
                        $fields_labels=$generator_options->field_label;
                        $field_display=$generator_options->field_display;
                        $field_required=$generator_options->field_required;
                        $allow_zero=$generator_options->allow_zero;
                        $allow_minus=$generator_options->allow_minus;
                        $chk_duplicate=$generator_options->chk_duplicate;
                        $field_data_type=$generator_options->field_data_type;
                        $field_is_disabled=$generator_options->is_disabled;
                        $after_detail=$generator_options->after_detail;
                        
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
                                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";
                                 $cls_field_name="_".$fields_names[$i];$is_disabled=0;$disabled_str="";
                                 
                                if(!empty($field_required) && in_array($fields_names[$i],$field_required)) {
                                    $required=1;
                                }
                                if(!empty($field_is_disabled) && in_array($fields_names[$i],$field_is_disabled)) {
                                    $is_disabled=1;
                                }
                                if(!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) {
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $duplicate_str="duplicate";
                                }
                                $lbl_str='<label for="'.$fields_names[$i].'" class="col-4 col-sm-2 col-md-1 col-lg-1 control-label">'.$fields_labels[$i].'';
                                if($required) {
                                    $required_str="required";
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $lbl_str.="*";
                                }
                                if($is_disabled) {
                                    $disabled_str="disabled";
                                }
                               
                                $lbl_str.="</label>";
                                switch($fields_types[$i]) {
                                    case "text":
                                    case "email":
                                    case "file":
                                    case "date":
                                    case "datetime-local":
                                    case "radio":
                                    case "checkbox":
                                    case "number":
                                    case "select":
                                        $value="";$field_str="";$cls="";
                                        if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                            $cls.=$required_str;
                                             if($transactionmode=="U" && $_bll->_mdl->$cls_field_name==1) {
                                                $chk_str="checked='checked'";
                                             }
                                            $value="1";
                                            $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str;
                                            $chk_str="";
                                             if(isset($_bll->_mdl)) {
                                                    $value=$_bll->_mdl->$cls_field_name; 
                                            }
                                        }
                                         if($fields_types[$i]=="number") {
                                            $step="";
                                            if(!empty($field_scale[$i]) && $field_scale[$i]>0) {
                                                for($k=1;$k<$field_scale[$i];$k++) {
                                                    $step.=0;
                                                }
                                                $step="0.".$step."1";
                                            } else {
                                                $step=1;
                                            }
                                            $step_str='step="'.$step.'"';
                                             $min=1; 
                                             if(!empty($allow_zero) && in_array($fields_names[$i],$allow_zero)) 
                                                 $min=0;
                                             if(!empty($allow_minus) && in_array($fields_names[$i],$allow_minus)) 
                                                $min="";

                                             $min_str='min="'.$min.'"';
                                         }
                                         if(!empty($value) && ($fields_types[$i]=="date" || $fields_types[$i]=="datetime-local" || $fields_types[$i]=="datetime" || $fields_types[$i]=="timestamp")) {
                                                $value=date("Y-m-d",strtotime($value));
                                         }
                                         if($fields_types[$i]=="select") {
                                            $cls.="form-select ".$required_str." ".$duplicate_str;
                                            $table=explode("_",$fields_names[$i]);
                                            $field_name=$table[0]."_name";
                                            $fields=$fields_names[$i].", ".$table[0]."_name";
                                            $tablename="tbl_".$table[0]."_master";
                                            $selected_val="";
                                            if(isset($_bll->_mdl->$cls_field_name)) {
                                                $selected_val=$_bll->_mdl->$cls_field_name;
                                            }
                                            if(!empty($where_condition[$i]))
                                                $where_condition_val=$where_condition[$i];
                                            else {
                                                $where_condition_val=null;
                                            }
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i]))
                                                $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls);
                                        } else {
                                            $field_str.='<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="'.ucwords(str_replace("_"," ",$fields_names[$i])).'" value= "'.$value.'"  '.$min_str.' '.$step_str.' '.$chk_str.'  '.$disabled_str.' '.$required_str.' />
                                            '.$error_container;
                                        }
                                        break;
                                    case "hidden":
                                        $lbl_str="";
                                        if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                            $hiddenvalue=0;
                                        else
                                            $hiddenvalue="";
                                        if($fields_names[$i]!="modified_by" && $fields_names[$i]!="modified_date") {
                                            if($fields_names[$i]=="company_id") {
                                                if(isset($_SESSION["sess_company_id"])) {
                                                    $hiddenvalue=$_SESSION["sess_company_id"];
                                                } else {
                                                    $hiddenvalue=1;
                                                }
                                            }
                                            else if($fields_names[$i]=="created_by") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=1;
                                                }
                                            } else if($fields_names[$i]=="created_date") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=date("Y-m-d H:i:s");
                                                }
                                            } else {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } 
                                            }
                                            $hidden_str.='
                                            <input type="'.$fields_types[$i].'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value= "'.$hiddenvalue.'"  />';
                                        }
                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset($_bll->_mdl)){
                                             $value=$_bll->_mdl->$cls_field_name;
                                            }
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" '.$disabled_str.' placeholder="'.ucwords(str_replace("_"," ",$fields_names[$i])).'"  '.$required_str.' >'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                    default:
                                        break;
                                } //switch ends
                                 $cls_err="";
                                    $lbl_err="";
                                   
                                if(empty($after_detail) || (!empty($after_detail) && !in_array($fields_names[$i],$after_detail))) {
                                    echo $lbl_str;
                                    if($field_str) {
                                    ?>
                                    <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $cls_err; ?>"  >
                                    <?php
                                            echo $field_str;
                                            echo $lbl_err;
                                    ?>
                                    </div>
                        <?php
                                    }
                                } else {
                                    $lbl_array[]=$lbl_str;
                                    $field_array[]=$field_str;
                                    $err_array[]=$lbl_err;
                                    $clserr_array[]=$cls_err;
                                }
                            } //for loop ends
                        } // field_types if ends
                    }
             } 
            
            ?>
                 </div><!-- /.row -->
              </div>
              <!-- /.box-body -->
<?php
    if(!empty($field_array)) {
?>
     <!-- remaining main table content-->
    <div class="box-body">
        <div class="form-group row gy-2">
    <?php
        for($j=0;$j<count($field_array);$j++) {
            echo $lbl_array[$j];
            if($field_array[$j]) {
            ?>
            <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $clserr_array[$j]; ?>"  >
            <?php
                    echo $field_array[$j];
                    echo $err_array[$j];
            ?>
            </div>
    <?php
            }
        } // after detail for loop ends
    ?>
    </div>
</div>
<?php
    } // empty detail array if ends
?>
<!-- .box-footer -->
              <div class="box-footer">
               <?php echo  $hidden_str; ?>
                <!--<input type="hidden" id="city_id" name="city_id" value= "<?php if(isset($_bll->_mdl))  echo $_bll->_mdl->_city_id; else echo 0; ?>">-->
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="modified_by" name="modified_by" value= "1">
                <input type="hidden" id="modified_date" name="modified_date" value= "<?php echo date('Y-m-d H:i:s')?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                <input type="hidden" id="deleted_records" name="deleted_records" />
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_city_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
                <input type="button" class="btn btn-dark" id="btn_cancel" name="btn_cancel" value="Cancel"  onclick="window.location=window.history.back();">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
    
<!-- Delete confirmation Popup -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete confirmation Popup -->
<!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div>
<!-- ./wrapper -->

<?php
    include("include/footer_includes.php");
?>
<script>
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    let detailIdLabel="";
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="<?php echo "city_id" ?>";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_city_master.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo "tbl_city_master"; ?>",type:"ajax"},
            success: function(response) {
                //let input=document.getElementById("party_sequence");
                if (response == 1) {
                    input.classList.add("is-invalid");
                    input.focus();
                    let message="";
                    if(input.validationMessage)
                        message=input.validationMessage;
                    else
                        message="Duplicate Value";
                    if(input.nextElementSibling) 
                      input.nextElementSibling.textContent = message;
                      return false;
                } else {
                   input.classList.remove("is-invalid");
                    if(input.nextElementSibling) 
                        input.nextElementSibling.textContent = "";
                }
            },
            error: function() {
                console.log("Error");
            }
        }); // ajax completed
    }
    duplicateInputs.forEach((input) => {
        input.addEventListener("blur", function () {
          checkDuplicate(input);
        });
      });
document.addEventListener("DOMContentLoaded", function () {
    const stateField = document.getElementById("state_id");
    const countryField = document.getElementById("country_id");

    function fetchCountry(stateId) {
        $.ajax({
            url: "classes/cls_city_master.php",
            type: "POST",
            data: { action: "fetchCountry", state_id: stateId },
            success: function(response) {
                console.log(response);
                if(response) {
                    countryField.value = response;
                }
            },
            error: function() {
                console.log("Error fetching country data.");
            }
        });
    }

    if (stateField) {
        stateField.addEventListener("change", function() {
            fetchCountry(this.value);
        });
    }
});

    
 document.getElementById("btn_add").addEventListener("click", function (event) {
    //event.preventDefault();
    const form = document.getElementById("masterForm"); // Store form reference
    let i=0;
    let firstelement;
     duplicateInputs.forEach((input) => {
          checkDuplicate(input);
      });
    if (!form.checkValidity()) {
        //event.stopPropagation();
        form.querySelectorAll(":invalid").forEach(function (input) {
            if(i==0) {
                firstelement=input;
            }
          input.classList.add("is-invalid");
          input.nextElementSibling.textContent = input.validationMessage; 
          i++;
        });
         if(firstelement) firstelement.focus(); 
         return false;
    }
    setTimeout(function(){
        const invalidInputs = document.querySelectorAll(".is-invalid");
        if(invalidInputs.length>0)
        {} else{
            const jsonDataString = JSON.stringify(jsonData);
            document.getElementById("detail_records").value = jsonDataString;

            const deletedDataString = JSON.stringify(deleteData);
            document.getElementById("deleted_records").value = deletedDataString;

            let transactionMode = document.getElementById("transactionmode").value;
            let message = "";
            let title = "";
            let icon = "success";

            if (transactionMode === "U") {
                message = "Record updated successfully!";
                title = "Update Successful!";
            } else {
                message = "Record added successfully!";
                title = "Save Successful!";
            }
            /*const result=await Swal.fire(title, message, icon);
            if (result.isConfirmed) {
              form.removeEventListener("submit", arguments.callee);
              document.getElementById("btn_add").click();
            }*/
             
             //form.removeEventListener("submit", arguments.callee);
             // document.getElementById("btn_add").click();
             (async function() {
              //await customAlert(message);
              result=await Swal.fire(title, message, icon);
                if (result.isConfirmed) {
                $("#masterForm").submit();
                }
                
            })();
        }
    },200);
} );
});
</script>
<?php
    include("include/footer_close.php");
?>