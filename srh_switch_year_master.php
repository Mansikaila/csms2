<?php
    include("classes/cls_company_year_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");

    // Fetch all company year ranges
    $yearRanges = $_bll->getAllCompanyYearRanges();

    $transactionmode = "";
    if (isset($_REQUEST["transactionmode"])) {    
        $transactionmode = $_REQUEST["transactionmode"];
    }
    if ($transactionmode == "U") {    
        $_bll->fillModel();
        $label = "Update";
    } else {
        $label = "Add";
    }
?>

<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
     <div class="content-wrapper">
    <div class="container-fluid">
      <section class="content-header">
        <h1>
          Switch Year
        </h1>
        <ol class="breadcrumb">
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href=""><i class="fa fa-dashboard"></i> Switch Year</a></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="col-md-12" style="padding:0;">
           <div class="box box-info">
                <!-- Form start -->
                <form id="masterForm" action="" method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
                <div class="box-body">
                    <div class="form-group row gy-2">
                        <label for="companyYear" class="col-sm-2 control-label">Select Company Year</label>
                        <div class="col-sm-3">
                            <select name="companyYear" id="companyYear" class="form-control" required>
                                <option value="">-- Select Year --</option>
                                <?php foreach ($yearRanges as $yearRange): ?>
                                   <option value="<?php echo htmlspecialchars($yearRange['company_year_id'] ?? '') ?>">
                                    <?php echo htmlspecialchars(($yearRange['start_year'] ?? '') . '-' . ($yearRange['end_year'] ?? '')) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
               </form>
            </div>
          </div>
        </section>
         </div>
    </div>
    </div>
</body>

<?php
    include("include/footer.php");
?>