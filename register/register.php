<?php
// Include database connection
include('db_connection.php');  // assuming your connection code is in db_connection.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture the form inputs
    $fullName = $_POST['fullName'];
    $noIC = $_POST['noIC'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $agama = $_POST['agama'];
    $institusi = $_POST['institusi'];
    $bidangPengajian = $_POST['bidangPengajian'];
    $subBidang = $_POST['subBidang'];
    $jabatanUnit = $_POST['jabatanUnit'];
    $program = $_POST['program'];
    $tarikhLantikan = $_POST['tarikhLantikan'];
    $tarikhPencen = $_POST['tarikhPencen'];
    
    // Sanitize user inputs to prevent SQL injection
    $fullName = mysqli_real_escape_string($conn, $fullName);
    $noIC = mysqli_real_escape_string($conn, $noIC);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $country = mysqli_real_escape_string($conn, $country);
    $city = mysqli_real_escape_string($conn, $city);
    $gender = mysqli_real_escape_string($conn, $gender);
    $agama = mysqli_real_escape_string($conn, $agama);
    $institusi = mysqli_real_escape_string($conn, $institusi);
    $bidangPengajian = mysqli_real_escape_string($conn, $bidangPengajian);
    $subBidang = mysqli_real_escape_string($conn, $subBidang);
    $jabatanUnit = mysqli_real_escape_string($conn, $jabatanUnit);
    $program = mysqli_real_escape_string($conn, $program);
    $tarikhLantikan = mysqli_real_escape_string($conn, $tarikhLantikan);
    $tarikhPencen = mysqli_real_escape_string($conn, $tarikhPencen);

    // Hash the password for security (not included in this form, but handled separately)
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // SQL query to insert the new user into the database
    $sql = "INSERT INTO tbluser (NO_IC, NAMA, EMAIL, PHONE, INSTITUSI, BIDANG_PENGAJIAN, SUB_BIDANG, JABATAN_UNIT, PROGRAM, TARIKH_LANTIKAN, TARIKH_PENCEN, GRED, JANTINA, AGAMA)
            VALUES ('$noIC', '$fullName', '$email', '$phone', '$institusi', '$bidangPengajian', '$subBidang', '$jabatanUnit', '$program', '$tarikhLantikan', '$tarikhPencen', '', '$gender', '$agama')";
    
    if ($conn->query($sql) === TRUE) {
