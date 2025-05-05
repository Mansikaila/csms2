<?php
$menuData = getDynamicMenu($_dbh);

$currentMonth = date("m"); 
$currentYear = date("Y");
if ($currentMonth >= 4) {
    $startYear = $currentYear;
    $endYear = $currentYear + 1;
} else {
    $startYear = $currentYear - 1;
    $endYear = $currentYear;
}
$yearRange = 'FY ' . $startYear . '-' . $endYear; 
?>

<header class="main-header">
    <nav class="navbar navbar-expand-lg navbar-static-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Done By Hetasvi-->
            <div class="collapse navbar-collapse pull-left" id="navbarSupportedContent">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link"><i class="fa fa-home"></i></a>
                    </li>
                    <?php
                    $currentGroup = null;

                    if (!empty($menuData) && is_array($menuData)):
                        foreach ($menuData as $module => $menuGroups): 
                    ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?php echo htmlspecialchars($module); ?>"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($module); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo htmlspecialchars($module); ?>">

                            <?php
                            $totalGroups = count($menuGroups);
                            $groupIndex = 0;

                            foreach ($menuGroups as $menuGroup => $menuItems): 
                                $groupIndex++;
                                ?>
                                <?php 
                                foreach ($menuItems as $menu):
                                    ?>
                                    <li class="dropdown-item">
                                        <!-- Make the entire row clickable -->
                                        <a href="<?php echo htmlspecialchars($menu['link']); ?>" class="text-dark d-block">
                                            <?php echo htmlspecialchars($menu['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>

                                <?php if ($groupIndex < $totalGroups): ?>
                                    <hr>
                                <?php endif; ?>

                            <?php
                            endforeach;
                            ?>
                            </ul>
                        </li>
                    <?php endforeach;
                    else: ?>
                        <li class="nav-item"><a class="nav-link">No modules found</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Done By Mansi-->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="user user-menu d-flex align-items-center">
                        <!-- Display Current Year Range -->
                      <a href="srh_switch_year_master.php" class="year-range-link">
                            <span class="year-range"><?php echo $yearRange; ?></span>
                    </a>
                        <!-- User Name -->
                        <span class="hidden-xs">
                            <?= !empty($_SESSION['sess_person_name']) ? ucwords($_SESSION['sess_person_name']) : 'Guest'; ?>
                        </span>
                        <div class="user-actions ms-3">
                            <?php if (!empty($_SESSION['sess_person_name'])): ?>
                                <a href="logout.php" class="btn btn-logout"><i class="fa fa-sign-out"></i></a>
                            <?php else: ?>
                                <a href="index.php" class="btn btn-logout"><i class="fa fa-sign-in"></i></a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- Done By Mansi-->
        </div>
    </nav>
</header>