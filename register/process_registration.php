<?php
// Include the database connection file
include 'db_connection.php'; // Ensure the database connection file exists and is correct

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch Step 1 data
    $fullName = isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : null;
    $noIC = isset($_POST['noIC']) ? htmlspecialchars($_POST['noIC']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : null;
    $religion = isset($_POST['religion']) ? htmlspecialchars($_POST['religion']) : null;

    // Fetch Step 2 data
    $institusi = isset($_POST['institusi']) ? htmlspecialchars($_POST['institusi']) : null;
    $field = isset($_POST['field']) ? htmlspecialchars($_POST['field']) : null;
    $subField = isset($_POST['subField']) ? htmlspecialchars($_POST['subField']) : null;
    $department = isset($_POST['department']) ? htmlspecialchars($_POST['department']) : null;
    $program = isset($_POST['program']) ? htmlspecialchars($_POST['program']) : null;
    $appointmentDate = isset($_POST['appointmentDate']) ? htmlspecialchars($_POST['appointmentDate']) : null;
    $retirementDate = isset($_POST['retirementDate']) ? htmlspecialchars($_POST['retirementDate']) : null;

    // Debugging: Print the POST data to see which fields are empty or missing
    echo "<pre>";
    var_dump($_POST); // Print the contents of the $_POST array
    echo "</pre>";

    // Check if all fields are filled in
    if (!$fullName || !$noIC || !$email || !$phone || !$religion || !$institusi || !$field || !$subField || !$department || !$program || !$appointmentDate || !$retirementDate) {
        echo "All fields are required.";
        exit;  // Stop the script if not all required fields are filled
    }

    // Prepare the SQL query to insert the data
    try {
        $sql = "INSERT INTO tbluser (NAMA, NO_IC, EMAIL, PHONE, AGAMA, JANTINA, INSTITUSI, BIDANG_PENGAJIAN, SUB_BIDANG, JABATAN_UNIT, PROGRAM, TARIKH_LANTIKAN, TARIKH_PENCEN)
                VALUES (:fullName, :noIC, :email, :phone, :religion, :gender, :institusi, :field, :subField, :department, :program, :appointmentDate, :retirementDate)";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':fullName', $fullName);
        $stmt->bindParam(':noIC', $noIC);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':religion', $religion);
        $stmt->bindParam(':gender', $gender); // Ensure the gender field is included
        $stmt->bindParam(':institusi', $institusi);
        $stmt->bindParam(':field', $field);
        $stmt->bindParam(':subField', $subField);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':program', $program);
        $stmt->bindParam(':appointmentDate', $appointmentDate);
        $stmt->bindParam(':retirementDate', $retirementDate);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Please submit the form first.";
}
?>
