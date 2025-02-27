<?php 

authenticated_page("reviewer");

$s_id = conn()->sanitize($_REQUEST['s_id']);

if (isset($_POST['add_prelim_question'])) {

    $question = conn()->sanitize($_POST['question']);
    $answer1 = conn()->sanitize($_POST['answer1']);
    $answer2 = conn()->sanitize($_POST['answer2']);
    $answer3 = conn()->sanitize($_POST['answer3']);
    $answer4 = conn()->sanitize($_POST['answer4']);
    $final_answer = $_POST['final_answer'];

    if ($final_answer == "") {
        // Redirect to the page without alert
        header("Location: reviewer_test_questions?s_id=$s_id");
        exit;
    } else {
        $query = "INSERT INTO question_answer (question, option1, option2, option3, option4, answer, subject_id, faculty_id, level) 
                  VALUES ('$question','$answer1','$answer2','$answer3','$answer4','$final_answer','$s_id','" . user_id() . "','PREBOARD1')";

        if (conn()->query($query)) {
            // Redirect to the page after successful insertion
            header("Location: reviewer_test_questions?s_id=$s_id");
            exit;
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
        }
    }
}
