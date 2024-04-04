<?php
$title = "Edit Author";
require_once "../template/header.php";
checkAdmin();

// Get the author ID
if (isset($_GET['author_id'])) {
    $author_id = $_GET['author_id'];
} else {
    echo "Empty author ID!";
    exit;
}

// Get author data
$query = "SELECT * FROM author WHERE author_id = '$author_id'";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Can't retrieve data " . mysqli_error($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);

// Author editing if Save change is chosen
if (isset($_POST['save_change'])) {

    // Get the author data from the form
    $author_name = mysqli_real_escape_string($conn, trim($_POST['author_name']));
    $author_description = mysqli_real_escape_string($conn, trim($_POST['author_description']));

    // Validate input
    if (empty($author_name)) {
        echo "Author name is required";
        exit;
    }

    // Update the author data in the database
    $query = "UPDATE author SET  
        author_name = '$author_name', 
        author_description = '$author_description'
        WHERE author_id = '$author_id'
    ";

    // Execute the query to update the author in the database
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Can't update data " . mysqli_error($conn);
        exit;
    }

    // Redirect to the specified location
    header("Location: authoredit.php?author_id=$author_id");
}
?>

<form method="post" action="authoredit.php?author_id=<?php echo $author_id; ?>">
    <table class="table">
        <tr>
            <th style="vertical-align: middle;">Author ID</th>
            <td><input type="text" name="author_id" value="<?php echo $row['author_id']; ?>" readOnly="true"></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Author Name</th>
            <td><input type="text" name="author_name" value="<?php echo $row['author_name']; ?>" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Description</th>
            <td><textarea name="author_description" cols="60" rows="5"><?php echo $row['author_description']; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_change" value="Save changes" class="btn btn-success">
                <input type="reset" value="Reset" class="btn btn-danger">
                <a href="author.php" class="btn btn-default">Go back</a>
            </td>
        </tr>
    </table>
</form>

<script>
    // Function to toggle visibility and position of the input for writing new author or publisher
    function toggleNewInput(selectElement, inputElement) {
        inputElement.style.display = (selectElement.value === 'new_author' || selectElement.value === 'new_publisher') ? 'inline-block' : 'none';
        inputElement.style.verticalAlign = (selectElement.value === 'new_author' || selectElement.value === 'new_publisher') ? 'top' : 'middle';
    }

    // Attach the function to the change event of author and publisher dropdowns
    const authorDropdown = document.querySelector('select[name="author"]');
    const publisherDropdown = document.querySelector('select[name="publisher"]');
    const authorInput = document.querySelector('input[name="new_author"]');
    const publisherInput = document.querySelector('input[name="new_publisher"]');

    authorDropdown.addEventListener('change', () => toggleNewInput(authorDropdown, authorInput));
    publisherDropdown.addEventListener('change', () => toggleNewInput(publisherDropdown, publisherInput));

    // Trigger the function on page load to set the initial position
    toggleNewInput(authorDropdown, authorInput);
    toggleNewInput(publisherDropdown, publisherInput);

    // Function to auto-resize textarea and adjust input sizes
    function autoResizeInputs() {
        // Resize text input fields
        const textInputs = document.querySelectorAll('input[type="text"]');
        textInputs.forEach(input => {
            input.style.width = 'auto';
            input.style.width = input.scrollWidth + 'px';
        });

        // Resize textarea
        const textarea = document.getElementById('descriptionTextarea');
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // Attach the function to text inputs and textarea's input event
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('input', autoResizeInputs);
    });
    document.getElementById('descriptionTextarea').addEventListener('input', autoResizeInputs);

    // Trigger the function on page load to adjust sizes if there's initial content
    window.addEventListener('load', autoResizeInputs);
</script>

<?php
require_once "../template/footer.php";
?>
