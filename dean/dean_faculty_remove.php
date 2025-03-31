<?php 

authenticated_page("dean");

$f_id = conn()->sanitize($_REQUEST['f_id']);

if (isset($_REQUEST['confirmed']) && $_REQUEST['confirmed'] == '1') {
    // If the confirmation is received, process the query
    $query = "UPDATE users SET status = 'Inactive' WHERE id = '$f_id'";
    if (conn()->query($query)) {
        echo "<script type='text/javascript'>
                alert('Reviewer successfully removed!');
                window.location.href = 'dean_faculty';
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error removing reviewer. Please try again.');
                window.location.href = 'dean_faculty';
              </script>";
    }
} else {
    // If not confirmed yet, show confirmation prompt
    echo "<script type='text/javascript'>
            if (confirm('Are you sure you want to remove this reviewer?')) {
                window.location.href = 'dean_faculty_remove?f_id=$f_id&confirmed=1';
            } else {
                alert('Action canceled.');
                window.location.href = 'dean_faculty';
            }
          </script>";
}

