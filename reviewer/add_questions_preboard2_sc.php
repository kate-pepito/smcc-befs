<?php
session_start();
include('../dbconnect.php');
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$s_id = mysqli_real_escape_string($conn, $_REQUEST['s_id']);
$active_tab = isset($_REQUEST['active_tab']) ? $_REQUEST['active_tab'] : 'preboard'; // Default to 'preboard' if not set

if (isset($_POST['add_prelim_question1'])) {

    $question = $_POST['question'];
    $answer1 = $_POST['answer11'];
    $answer2 = $_POST['answer22'];
    $answer3 = $_POST['answer33'];
    $answer4 = $_POST['answer44'];
    $final_answer = $_POST['final_answer1'];

    if ($final_answer == "") {
        // Redirect to the page without alert
        header("Location: reviewer_test_questions.php?user_id=$user_id&s_id=$s_id&active_tab=$active_tab");
        exit;
    } else {
        $query = "INSERT INTO question_answer (question, option1, option2, option3, option4, answer, subject_id, faculty_id, level) 
                  VALUES ('$question','$answer1','$answer2','$answer3','$answer4','$final_answer','$s_id','$user_id','PREBOARD2')";

        if (mysqli_query($conn, $query)) {
            // Redirect to the page after successful insertion and stay on the current tab
            header("Location: reviewer_test_questions.php?user_id=$user_id&s_id=$s_id&active_tab=$active_tab");
            exit;
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}
?>
