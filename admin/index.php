<?php
session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}

require("../bootstrap.php");

use Controller\Counts;
use Core\Base;
use Controller\Sections;
$config = require Base::build_path("config/database.php");
$counts = new Counts($config["database"]["mysql"]);

$pageTitle = "Dashboard";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php require Base::build_path("partials/head.php") ?>
</head>

<body>

    <?php require Base::build_path("partials/header.php") ?>

    <?php require Base::build_path("partials/aside.php") ?>

    <main id="main" class="main">

        <?php require Base::build_path("partials/page-title.php") ?>
    
        <section class="section dashboard">
            <?php if (isset($_SESSION["user"]["role"]) && ($_SESSION["user"]["role"] === "secretary" || $_SESSION["user"]["role"] === "hod")) { ?>
        <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Sales Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Students</h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-contacts-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalStudents($_SESSION["user"]["fk_department"]) ?></h6>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card revenue-card">
                                 <div class="card-body">
                                    <h5 class="card-title">Total Staff</h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-account-pin-box-line"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalLecturers($_SESSION["user"]["fk_department"]) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Revenue Card -->
                        <!-- Customers Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card customers-card">
                               <div class="card-body">
                                    <h5 class="card-title">Total Courses</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-booklet-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalCourses($_SESSION["user"]["fk_department"]) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Customers Card -->

                        <!-- Customers Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Programs</h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-book-read-line"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalPrograms($_SESSION["user"]["fk_department"]) ?></h6>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div><!-- End Reports Card -->
                    </div>
                    <div class="row">
                        <!-- Sales Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card report-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Classes</h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalClasses($_SESSION["user"]["fk_department"]) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                  <?php if (isset($_SESSION["user"]["role"]) && ($_SESSION["user"]["role"] === "lecturer" || $_SESSION["user"]["role"] === "hod")) { ?>
                       <div class = "row ">  
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card report-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Classes Assigned</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalLectures($_SESSION["user"]["number"]) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>                             
                            </div> 
                           <div class="col-xxl-3 col-md-6">
                            <div class="card info-card report-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Quizzes Set</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalQuizzesSet($_SESSION["user"]["number"]) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>                             
                            </div> 
                            <div class="col-xxl-3 col-md-6">
                            <div class="card info-card report-card">
                            <div class="card-body">
                                    <h5 class="card-title">Total Completed Quizzes</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalQuizzesCompleted($_SESSION["user"]["number"]) ?></h6>
                                        </div>
                                    </div>
                            </div>
                            </div>                             
                            </div>
                            <!-- <div class="col-xxl-3 col-md-6">
                            <div class="card info-card report-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Pending Quizzes</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $counts->totalQuizzesActive($_SESSION["user"]["number"]) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>                             
                            </div>  -->
                       </div>
                            <?php }?>

                            
                    </div><!-- End Left side columns -->

                </div>
        </section>
        
        
        <?php if (isset($_SESSION["user"]["role"]) && ($_SESSION["user"]["role"] === "lecturer" || $_SESSION["user"]["role"] === "hod")) { ?>

                <section class="section dashboard">
                <ol class="breadcrumb">
            <li class="breadcrumb-item">Courses</li>
        </ol>   
          
                        <div class="row">
                            <?php
                            $classes = new Sections($config["database"]["mysql"]);
                            $all_classes = $classes->fetchByStaffID($_SESSION["user"]["number"]);

                            // Group classes by course
                            $classes_by_course = [];
                            foreach ($all_classes as $class) {
                                $course_code = $class["fk_course"];
                                if (!isset($classes_by_course[$course_code])) {
                                    $classes_by_course[$course_code] = [];
                                }
                                $classes_by_course[$course_code][] = $class;
                            }
                            // Display each course with its classes
                            foreach ($classes_by_course as $course_code => $classes) {
                                ?>
                                <div class="col-xxl-3 col-md-6">
                                    <div class="card info-card sales-card">
                                        <div class="card-body">
                                            <h5 class="card-title">Course: <?= $course_code ?></h5>
                                            <div class="ps-3">
                                                <p class="m-0">Credit Hours: <?= $class["credit_hours"] ?></p>
                                            </div>
                                            <?php foreach ($classes as $class) { ?>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-contacts-fill"></i>
                                                    </div>
                                                    <div class="ps-3">
                                                        <p class="m-0">Class <?= $class["classCode"] ?></p>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="card-footer text-center">
                                            <button class="btn btn-primary" onclick="redirectToClass('<?= $course_code ?>')">View</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
            <script>
                function redirectToClass(courseId) {
                    // Store the course ID in local storage
                    localStorage.setItem('selectedCourseId', courseId);
                    
                    // Redirect to class.php with the course ID as a parameter
                    window.location.href = 'class.php?course_id=' + courseId;
                }
            </script>

                </section>
                <?php }?>
    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") 
    ?>

</body>

</html>

