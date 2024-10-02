<?php
session_start();
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit();
}
require("../bootstrap.php");

use Controller\Sections;
use Core\Base;
use Controller\Quizzes;
use Controller\Questions;
use Controller\Classes;

$config = require Base::build_path("config/database.php");
$counts = new Sections($config["database"]["mysql"]);

$courseId = $_GET['course_id'];

$pageTitle = "";
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

            <div class="row">
                <!-- Left side columns -->
                <div class="card">
                    <div class="card-body">

                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered" id="myTabjustified" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#bordered-home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="quiz-tab" data-bs-toggle="tab" data-bs-target="#bordered-quiz" type="button" role="tab" aria-controls="quiz" aria-selected="false">Quiz</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#bordered-questions" type="button" role="tab" aria-controls="questions" aria-selected="false">Questions</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="answers-tab" data-bs-toggle="tab" data-bs-target="#bordered-answers" type="button" role="tab" aria-controls="answers" aria-selected="false">Review</button>
                            </li>
                        </ul>


                    </div>

                </div>
            </div>

            <div class="tab-content pt-2" id="myTabjustifiedContent">
                <div class="tab-pane fade" id="bordered-quiz" role="tabpanel" aria-labelledby="quiz-tab">
                    <div class="row align-items-top">
                        <div class="col-lg-12">
                            <!-- Default Card -->
                            <div class="card">
                                <div class="card-body">
                                    <!-- Content goes here -->

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- Vertically centered Modal -->
                                            <p></p> <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewQuiz">
                                                ADD
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="addNewQuiz" tabindex="-1">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                                        <div style="display: flex; width: 800px; align-items: center;">
                                            <!-- Multi Columns Form -->
                                            <form class="row g-3" id="addNewQuizForm" method="POST">

                                                <!-- Other input fields for the quiz -->
                                                <div class="col-md-8">
                                                    <label for="addTitle" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="addTitle" name="addTitle">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="addQuestion" class="form-label">Instructions</label>
                                                    <textarea class="form-control" id="addQuestion" name="addQuestion"></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="addStartDate" class="form-label">Quiz Start Date</label>
                                                    <input type="date" class="form-control" id="addStartDate" name="addStartDate">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="addStartTime" class="form-label">Start Time</label>
                                                    <input type="time" class="form-control" id="addStartTime" name="addStartTime">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="addDuration" class="form-label">Duration</label>
                                                    <input type="text" class="form-control" id="addDuration" name="addDuration" placeholder="in minutes">
                                                </div>
                                                <!-- Hidden input field to store the course ID -->
                                                <input type="hidden" id="addFkCourse" name="addFkCourse" value="<?= isset($_GET['course_id']) ? htmlspecialchars($_GET['course_id']) : '' ?>">
                                                <!-- Hidden inputs for fk_staff and fk_semester -->
                                                <input type="hidden" id="addFkStaff" name="addFkStaff" value="<?= $_SESSION["user"]["number"] ?>">
                                                <input type="hidden" id="addFkSemester" name="addFkSemester" value="1">
                                                <!-- <input type="hidden" id="department" name="department" value="<?= $_SESSION["user"]["fk_department"] ?>"> -->
                                            </form>

                                            <!-- End Multi Columns Form -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                                        <label class="btn btn-primary" for="add-course" id="addNewQuizBtn">Save changes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Include modal content here -->

                        <!-- Left side columns -->
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-xxl-12 col-md-12">
                                    <!-- Quizzes Table -->
                                    <?php

                                    // Check if the course ID is set in the URL
                                    if (isset($_GET['course_id'])) {
                                        // Get the course ID from the URL
                                        $courseId = htmlspecialchars($_GET['course_id']);

                                        // Fetch quizzes based on the course ID
                                        $quizzes = new Quizzes($config["database"]["mysql"]);
                                        $all_quizzes = $quizzes->fetchByCode($courseId);
                                    } else {
                                        // If course ID is not set, handle the error or redirect to another page
                                        // For now, let's display an error message
                                        echo "Course ID is not provided.";
                                        exit(); // Stop further execution
                                    }
                                    ?>

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Quizzes</h5>
                                            <!-- Borderless Table -->
                                            <table class="table table-borderless datatable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Quiz Title</th>
                                                        <th scope="col">Total Marks</th>
                                                        <th scope="col">Start Date</th>
                                                        <th scope="col">Start Time</th>
                                                        <th scope="col">Duration</th>
                                                        <!-- <th scope="col">Status</th> -->
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rowCounter = 1;
                                                    foreach ($all_quizzes as $quiz) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row"><?= $rowCounter++ ?></th>
                                                            <!-- <td><?= $quiz["id"] ?></td> -->
                                                            <!-- <td><?= $quiz["name"] ?></td> -->
                                                            <td><?= $quiz["title"] ?></td>
                                                            <td><?= $quiz["total_mark"] ?></td>
                                                            <td><?= $quiz["start_date"] ?></td>
                                                            <td><?= $quiz["start_time"] ?></td>
                                                            <td><?= $quiz["duration"] ?> MIN</td>
                                                            <!-- <td><?= $quiz["status"] ?></td> -->
                                                            <td style="display: flex;">
                                                                <!-- <button type="button" class="btn btn-info btn-sm me-2 viewQuizData" data-view="<?= $quiz["id"] ?>" data-bs-toggle="modal" data-bs-target="" title="View Quiz Details"><i class="bi bi-eye"></i></button> -->
                                                                <button type="button" class="btn btn-success btn-sm me-2 addQuestData" data-class="<?= $quiz["id"] ?>" data-bs-toggle="modal" data-bs-target="#addQuesttoQuiz" title="add questions"><i class="bi bi-plus-lg"></i></button>
                                                                <button type="button" class="btn btn-primary btn-sm me-2 editQuizData" data-quiz="<?= $quiz["id"] ?>" data-bs-toggle="modal" data-bs-target="#editQuiz" title="edit quiz"><i class="bi bi-pen"></i></button>
                                                                <button type="button" class="btn btn-danger btn-sm archiveQuizBtn" id="<?= $quiz["id"] ?>" title="archive quiz"><i class="bi bi-archive"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <!-- Include table content here -->
                                </div>
                            </div>

                        </div><!-- End Left side columns -->

                        <div class="modal fade" id="viewQuizModal" tabindex="-2">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <?php
                                        if (isset($_GET['quiz_id'])) {
                                            $quizId = htmlspecialchars($_GET['quiz_id']);
                                            $quizzes = new Quizzes($config["database"]["mysql"]);
                                            $quizDetails = $quizzes->getQuizQuestions($quizId);
                                            if (!empty($quizDetails)) {
                                        ?>
                                                <div id="quizDetails">
                                                    <div class="col-md-8">
                                                        <label for="Title" class="form-label">Title</label>
                                                        <input type="text" class="form-control" id="Title" name="Title" value="<?= $quizDetails["quiz_title"] ?>">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="Question" class="form-label">Instructions</label>
                                                        <textarea class="form-control" id="Question" name="Question"> </textarea>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="Total" class="form-label">Total</label>
                                                        <input type="date" class="form-control" id="Total" name="Total">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="StartDate" class="form-label">Start Date</label>
                                                        <input type="date" class="form-control" id="StartDate" name="StartDate">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="StartTime" class="form-label">Start Time</label>
                                                        <input type="time" class="form-control" id="StartTime" name="StartTime">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="Duration" class="form-label">Duration</label>
                                                        <input type="text" class="form-control" id="Duration" name="Duration">
                                                    </div>
                                                </div>
                                                <!-- Quiz Questions Section -->
                                                <div id="quizQuestions">
                                                    <table class="table table-borderless datatable">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Question Type</th>
                                                                <th scope="col">Question</th>
                                                                <th scope="col">Marks</th>
                                                                <th scope="col"></th>
                                                                <th scope="col"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                        <?php } else {
                                                echo "Quiz not found.";
                                                exit();
                                            }
                                        } ?>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade" id="addQuesttoQuiz" tabindex="-3">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <?php
                                        if (isset($_GET['course_id'])) {
                                            $courseId = htmlspecialchars($_GET['course_id']);
                                            $questions = new Questions($config["database"]["mysql"]);
                                            $all_questions = $questions->fetchByCode($courseId);
                                        } else {
                                            echo "Course ID is not provided.";
                                            exit();
                                        }
                                        ?>
                                        <div class="row">
                                            <form id="assignQuestForm" method="POST">
                                                <input type="hidden" name="quiz_id" id="quiz_id" value="">
                                                <table class="table table-borderless datatable">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Question Type</th>
                                                            <th scope="col">Question</th>
                                                            <th scope="col">Marks</th>
                                                            <th scope="col"></th>


                                                            <th scope="col"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $rowCounter = 1;
                                                        foreach ($all_questions as $question) { ?>
                                                            <tr>
                                                                <!-- Table data for each question -->
                                                                <th scope="row"><?= $rowCounter++ ?></th>

                                                                <td><?= $question["type"] ?></td>
                                                                <td><?= $question["question"] ?></td>
                                                                <td><?= $question["marks"] ?></td>
                                                                <td> <input type="checkbox" class="form-check-input" name="questions[]" value="<?= $question["id"] ?>">
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <button type="submit" class="btn btn-primary">Assign</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                                        <label class="btn btn-primary" for="edit-question" id="editQuestBtn">Save changes</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editQuiz" tabindex="-4">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                                        <div style="display: flex; width: 800px; align-items: center;">
                                            <!-- Multi Columns Form -->
                                            <form class="row g-3" id="editQuizForm" method="POST">
                                                <!-- Hidden input field to store the quiz ID -->
                                                <input type="hidden" id="editQuizId" name="editQuizId" value="">
                                                <input type="hidden" id="addFkCourse" name="addFkCourse" value="<?= isset($_GET['course_id']) ? htmlspecialchars($_GET['course_id']) : '' ?>">

                                                <!-- Other input fields for the quiz -->
                                                <div class="col-md-8">
                                                    <label for="editTitle" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="editTitle" value="" name="editTitle" value="">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="editInstruction" class="form-label">Instructions</label>
                                                    <textarea class="form-control" id="editInstruction" value="" name="editInstruction"></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="editStartDate" class="form-label">Start Date</label>
                                                    <input type="date" class="form-control" id="editStartDate" value="" name="editStartDate">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="editStartTime" class="form-label">Start Time</label>
                                                    <input type="time" class="form-control" id="editStartTime" value="" name="editStartTime">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="editDuration" class="form-label">Duration</label>
                                                    <input type="text" class="form-control" id="editDuration" value="" name="editDuration">
                                                </div>

                                                <!-- Hidden inputs for fk_staff and fk_semester -->
                                                <input type="hidden" id="editFkStaff" name="editFkStaff" value="<?= $_SESSION["user"]["number"] ?>">
                                                <input type="hidden" id="editFkSemester" name="editFkSemester" value="1">
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                                        <label class="btn btn-primary" for="editQuizBtn" id="editQuizBtn">Save changes</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
                <div class="tab-pane fade" id="bordered-questions" role="tabpanel" aria-labelledby="questions-tab">
                    <div class="row align-items-top">
                        <div class="col-lg-12">
                            <!-- Default Card -->

                            <!-- Default Card -->
                            <div class="card">
                                <div class="card-body">
                                    <!-- Content goes here -->

                                    <div class="row">
                                        <div class="col-lg-8">
                                            <!-- Vertically centered Modal -->
                                            <p></p> <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewQuestion">
                                                ADD
                                            </button>
                                            <!-- Button to open modal -->
                                            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
   Upload
</button> -->
                                            <!-- Modal -->
                                            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="uploadModalLabel">Upload Spreadsheet</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- File upload section -->
                                                            <div class="row mb-3">
                                                                <!-- Upload Questions Form -->
                                                                <form id="uploadForm" enctype="multipart/form-data">
                                                                    <div class="row mb-3">
                                                                        <div class="col-sm-10">
                                                                            <input class="form-control" type="file" id="formFile" name="file">
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-12">
        <button type="button" id="uploadButton" class="btn btn-primary">Upload Questions</button>
    </div> -->
                                                                </form>


                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div><!-- Script to handle AJAX submission -->


                                            <!-- Modal for adding a new question -->
                                            <!-- Modal for adding a new question -->
                                            <div class="modal fade" id="addNewQuestion" tabindex="-1">
                                                <div class="modal-dialog modal-fullscreen">
                                                    <div class="modal-content">
                                                        <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                                                            <div style="display: flex; width: 800px; align-items: center;">
                                                                <!-- Multi Columns Form -->
                                                                <form class="row g-3" id="addNewQuestionForm" method="POST">
                                                                    <!-- Question input field -->
                                                                    <div class="col-md-8">
                                                                        <label for="addQuestion" class="form-label">Question</label>
                                                                        <textarea class="form-control" id="addQuestion" name="addQuestion" required></textarea>
                                                                    </div>
                                                                    <!-- Question type input field -->
                                                                    <div class="col-md-6">
                                                                        <label for="addQuestionType" class="form-label">Question Type</label>
                                                                        <input type="text" class="form-control" id="addQuestionType" name="addQuestionType" required>
                                                                    </div>
                                                                    <!-- Marks input field -->
                                                                    <div class="col-md-2">
                                                                        <label for="addQuestionMarks" class="form-label">Marks</label>
                                                                        <input type="text" class="form-control" id="addMarks" name="addMarks" required>
                                                                    </div>
                                                                    <!-- Container for possible answers -->
                                                                    <div class="col-md-12">
                                                                        <label class="form-label">Possible Answers</label>
                                                                        <div id="possibleAnswersContainer">
                                                                            <!-- Answer input fields will be added dynamically here -->
                                                                        </div>
                                                                        <button type="button" class="btn btn-primary mt-2" id="addAnswerBtn">Add Answer</button>
                                                                        <input type="hidden" id="possibleAnswers" name="possibleAnswers" value="">
                                                                    </div>
                                                                    <!-- Hidden inputs for fk_course, fk_staff, and fk_semester -->
                                                                    <input type="hidden" id="addFkCourse" name="addFkCourse" value="<?= isset($_GET['course_id']) ? htmlspecialchars($_GET['course_id']) : '' ?>">
                                                                    <input type="hidden" id="addFkStaff" name="addFkStaff" value="<?= $_SESSION["user"]["number"] ?>">
                                                                </form>
                                                                <!-- End Multi Columns Form -->
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary" form="addNewQuestionForm">Save changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Left side columns -->
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-xxl-12 col-md-12">
                                    <!-- Questions Table -->
                                    <?php

                                    // Check if the course ID is set in the URL
                                    if (isset($_GET['course_id'])) {
                                        // Get the course ID from the URL
                                        $courseId = htmlspecialchars($_GET['course_id']);

                                        // Fetch quizzes based on the course ID
                                        $questions = new Questions($config["database"]["mysql"]);
                                        $all_questions = $questions->fetchByCode($courseId);
                                    } else {
                                        // If course ID is not set, handle the error or redirect to another page
                                        // For now, let's display an error message
                                        echo "Course ID is not provided.";
                                        exit(); // Stop further execution
                                    }
                                    ?>
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Questions</h5>
                                            <!-- Borderless Table -->
                                            <table class="table table-borderless datatable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <!-- <th scope="col">Quiz Code</th> -->
                                                        <th scope="col">Question Type</th>
                                                        <th scope="col">Question</th>
                                                        <th scope="col">Marks</th>
                                                        <th scope="col"></th>


                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rowCounter = 1;
                                                    foreach ($all_questions as $question) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row"><?= $rowCounter++ ?></th>
                                                            <td><?= $question["type"] ?></td>
                                                            <td><?= $question["question"] ?></td>
                                                            <td><?= $question["marks"] ?></td>

                                                            <td style="display: flex;">
                                                                <button type="button" class="btn btn-primary btn-sm me-2 editQuestionData" data-questiion="<?= $question["id"] ?>" data-bs-toggle="modal" data-bs-target="#editQuestion" title="edit question"><i class="bi bi-pen"></i></button>
                                                                <button type="button" class="btn btn-danger btn-sm archiveQuestBtn" id="<?= $question["id"] ?>">Archive</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>

                                            </table>
                                            <!-- End Bordered Table -->

                                        </div>
                                    </div>
                                    <!-- Include table content here -->
                                </div>
                            </div>
                        </div><!-- End Left side columns -->

                        <div class="modal fade" id="editQuestion" tabindex="-4">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                                        <div style="display: flex; width: 800px; align-items: center;">
                                            <!-- Multi Columns Form -->
                                            <form class="row g-3" id="editQuestionForm" method="POST">
                                                <!-- Hidden input field to store the quiz ID -->
                                                <input type="hidden" id="editQuestionId" name="editQuestionId" value="">
                                                <input type="hidden" id="addFkCourse" name="addFkCourse" value="<?= isset($_GET['course_id']) ? htmlspecialchars($_GET['course_id']) : '' ?>">

                                                <!-- Other input fields for the quiz -->
                                                <div class="col-md-8">
                                                    <label for="editQuestion" class="form-label">Question</label>
                                                    <input type="text" class="form-control" id="editQuestionContent" value="" name="editQuestionContent" value="">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="editQuestionType" class="form-label">Question Type</label>
                                                    <input class="form-control" id="editQuestionType" value="" name="editQuestionType">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="editMarks" class="form-label">Marks</label>
                                                    <input type="text" class="form-control" id="editMarks" value="" name="editMarks">
                                                </div>

                                                <!-- Hidden inputs for fk_staff and fk_semester -->
                                                <input type="hidden" id="editFkStaff" name="editFkStaff" value="<?= $_SESSION["user"]["number"] ?>">
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                                        <label class="btn btn-primary" for="editQuestionBtn" id="editQuestionBtn">Save changes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="bordered-reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="row align-items-top">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-xxl-12 col-md-12">
                                        <?php
                                        if (isset($_GET['course_id'])) {
                                            $courseId = htmlspecialchars($_GET['course_id']);
                                            $quizzes = new Quizzes($config["database"]["mysql"]);
                                            $all_quizzes = $quizzes->fetchByStatus($courseId);
                                        } else {

                                            echo "Course ID is not provided.";
                                            exit();
                                        }
                                        ?>
                                        <table class="table table-borderless datatable">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Quiz Title</th>
                                                    <th scope="col">Total Marks</th>
                                                    <th scope="col">Start Date</th>
                                                    <th scope="col">Start Time</th>
                                                    <th scope="col">Duration</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $rowCounter = 1;
                                                foreach ($all_quizzes as $quiz) {
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?= $rowCounter++ ?></th>
                                                        <td><?= $quiz["title"] ?></td>
                                                        <td><?= $quiz["total_mark"] ?></td>
                                                        <td><?= $quiz["start_date"] ?></td>
                                                        <td><?= $quiz["start_time"] ?></td>
                                                        <td><?= $quiz["duration"] ?> MIN</td>
                                                        <td style="display: flex;">
                                                            <button type="button" class="btn btn-info btn-sm me-2 reviewQuiz" data-quiz="<?= $quiz["id"] ?>" data-bs-toggle="modal" data-bs-target="#reviewModal" title="Review Quiz">
                                                                <i class="bi bi-eye"></i>
                                                            </button> <button type="button" class="btn btn-danger btn-sm archiveQuizBtn" id="<?= $quiz["id"] ?>" title="archive quiz"><i class="bi bi-archive"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="reviewModalLabel">Quiz Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Table Content Goes Here -->
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Bordered Tabs -->
            </div>
        </section>
    </main><!-- End #main -->
    <?php require Base::build_path("partials/foot.php") ?>
    <script>
        $(document).ready(function() {

            $(".viewQuizData").click(function() {
                var quizId = $(this).data("view");
                $.ajax({
                    type: "GET",
                    url: "../api/quiz/details",
                    data: {
                        quiz_id: quizId
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Display quiz details on the page
                            displayQuizDetails(response.data);
                        } else {
                            alert("Failed to fetch quiz details: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching quiz details:", error);
                        alert("An error occurred while fetching quiz details. Please try again later.");
                    }
                });
            });
            $("#addNewQuizBtn").on("click", function() {
                $("#addNewQuizForm").submit();
            });

            $("#addNewQuizForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/quiz/add",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    alert(data.message);
                    if (data.success) window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#addNewQuestionForm").on("submit", function(e) {
                e.preventDefault();

                // Create FormData object to serialize form data
                var formData = new FormData(this);

                // Send AJAX request
                $.ajax({
                    type: "POST",
                    url: "../api/question/add",
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    alert(data.message);
                    if (data.success) window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#addAnswerBtn").click(function() {
                // Create new answer input fields
                var answerField = '<div class="input-group mb-3">' +
                    '<input type="text" class="form-control" placeholder="Answer" name="answer_' + Date.now() + '" required>' +
                    '<div class="input-group-text">' +
                    '<input class="form-check-input mt-0" type="checkbox" name="right_answer_' + Date.now() + '" value="1"> Correct Answer' +
                    '</div>' +
                    '<button class="btn btn-outline-danger" type="button" onclick="$(this).closest(\'.input-group\').remove()">Remove</button>' +
                    '</div>';

                $("#possibleAnswersContainer").append(answerField);
            });

            $(".editQuizData").on("click", function() {
                QuizID = this.dataset.quiz;

                $.ajax({
                        type: "GET",
                        url: "../api/quiz/fetch?quiz=" + QuizID,
                    }).done(function(data) {
                        console.log(data);
                        if (data.success) {
                            // Assuming data.message is an array, display data from the first row
                            var firstRow = data.message[0];
                            $("#editTitle").val(firstRow.title);
                            $("#editInstruction").val(firstRow.instructions);
                            $("#editStartDate").val(firstRow.start_date);
                            $("#editDuration").val(firstRow.duration);
                            $("#editStartTime").val(firstRow.start_time);
                            $("#editQuizId").val(firstRow.id);
                        } else {
                            alert(data.message)
                        }
                    })
                    .fail(function(err) {
                        console.log(err);
                    });
            });

            $("#editQuizBtn").on("click", function() {
                // Submit the edit quiz form
                $("#editQuizForm").submit();
            });

            $("#editQuizForm").on("submit", function(e) {
                e.preventDefault();

                // AJAX request to update quiz details
                $.ajax({
                    type: "POST",
                    url: "../api/quiz/edit",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    alert(data.message);
                    if (data.success) window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                    window.location.reload();
                });
            });

            $(".editQuestionData").on("click", function() {
                var questionID = this.dataset.questiion;

                $.ajax({
                    type: "GET",
                    url: "../api/question/fetch?question=" + questionID,
                }).done(function(data) {
                    console.log(data);
                    if (data.success) {
                        // Populate form fields with fetched question data
                        var firstRow = data.message[0];
                        $("#editQuestionId").val(firstRow.id);
                        $("#editQuestionContent").val(firstRow.question);
                        $("#editQuestionType").val(firstRow.type);
                        $("#editMarks").val(firstRow.marks);
                    } else {
                        alert(data.message);
                    }
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#editQuestionBtn").on("click", function() {
                // Submit the edit question form
                $("#editQuestionForm").submit();
            });

            $("#editQuestionForm").on("submit", function(e) {
                e.preventDefault();

                // AJAX request to update question details
                $.ajax({
                    type: "POST",
                    url: "../api/question/edit",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    alert(data.message);
                    if (data.success) window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $(".archiveQuestBtn").on("click", function() {
                if (confirm("Are you sure you want to archive this question?")) {
                    formData = {
                        "archive-question-code": $(this).attr("id")
                    }

                    $.ajax({
                        type: "POST",
                        url: "../api/question/archive",
                        data: formData,
                    }).done(function(data) {
                        console.log(data);
                        alert(data.message);
                        if (data.success) window.location.reload();
                    }).fail(function(err) {
                        console.log(err);
                    });
                }

            });

            $(".addQuestData").click(function() {
                var quizId = $(this).data("class");
                $("#quiz_id").val(quizId);
            });

            $("#assignQuestForm").submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "../api/quiz/assign", // Specify the URL to handle the form submission
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        console.log(response);
                        alert("Questions assigned successfully");
                        window.location.reload();

                        // Close the modal or do any additional actions
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                        alert("Error occurred while assigning questions");
                    }
                });
            });

            $(".archiveQuizBtn").on("click", function() {
                if (confirm("Are you sure you want to archive this quiz?")) {
                    formData = {
                        "archive-quiz-code": $(this).attr("id")
                    }

                    $.ajax({
                        type: "POST",
                        url: "../api/quiz/archive",
                        data: formData,
                    }).done(function(data) {
                        console.log(data);
                        alert(data.message);
                        if (data.success) window.location.reload();
                    }).fail(function(err) {
                        console.log(err);
                    });
                }

            });
        });
    </script>


</body>

</html>