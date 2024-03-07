<?php
    session_start();
    if (isset($_SESSION["user"])){
        header("Location: index.php");
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="registration.css">
</head>
<body>
    <div class="container">
        <div class="registration-box">

            
<?php
if(isset($_POST["Submit"])) {
    $LastName = $_POST["LastName"];
    $FirstName = $_POST["FirstName"];
    $email = $_POST["Email"];
    $country = $_POST["country"];
    $state = $_POST["state"];
    $city = $_POST["city"];
    $Barangay = $_POST["Barangay"];
    $PhaseSubdivision = $_POST["PhaseSubdivision"];
    $Street = $_POST["Street"];
    $LotBlock = $_POST["LotBlock"];
    $ContactNumber = $_POST["contactNumber"];
    $password = $_POST["password"];
    $RepeatPassword = $_POST["repeat_password"];

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();

    if (empty($LastName) || empty($FirstName) || empty($email) || empty($password) || empty($RepeatPassword)) {
        array_push($errors, "All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        array_push($errors, "Email is not valid");
    }

    if(strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }

    if($password != $RepeatPassword){
        array_push($errors, "Password does not match");
    }

    require_once "database.php";
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);

    if($rowCount > 0){
        array_push($errors,"Email Already Exists.");
    }

    if (count($errors) > 0){
        foreach($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        require_once("database.php");
        $sql = "INSERT INTO user (last_name, first_name, email, country, state, city, barangay, subdivision, street, lot_block, contact_number, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        $preparestmt = mysqli_stmt_prepare($stmt, $sql);

        if ($preparestmt) {
            mysqli_stmt_bind_param($stmt, "ssssssssssss", $LastName, $FirstName, $email, $country, $state, $city, $Barangay, $PhaseSubdivision, $Street, $LotBlock, $ContactNumber, $passwordHash);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>You are Registered Successfully!</div>";
        } else {
            die("Something went wrong");
        }
    }
}
?>
<form action="registration.php" method="post">
    <div class="form-group">
        <input type="text" class="form-control" name="LastName" placeholder="Last Name">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="FirstName" placeholder="First Name">
    </div>
    <div class="form-group">
        <input type="email" class="form-control" name="Email" placeholder="Email">
    </div>

    <div class="form-group">
        <label for="country">Country</label>
        <select class="form-control" id="country" name="country" required="">
            <option selected="">Choose...</option>
        </select>
    </div>

    <div class="form-group">
        <label for="state">State/Province</label>
        <select class="form-control" id="state" name="state">
            <option selected="">Choose...</option>
        </select>
    </div>

    <div class="form-group">
        <label for="city">City/Municipality</label>
        <select class="form-control" id="city" name="city">
            <option selected="">Choose...</option>
        </select>
    </div>

    <div class="form-group">
        <input type="text" class="form-control" name="Barangay" placeholder="Barangay (if applicable)">
    </div>

    <div class="form-group">
        <input type="text" class="form-control" name="PhaseSubdivision" placeholder="Phase/Subdivision (if applicable)">
    </div>

    <div class="form-group">
        <input type="text" class="form-control" name="Street" placeholder="Street">
    </div>

    <div class="form-group">
        <input type="text" class="form-control" name="LotBlock" placeholder="Lot/Block">
    </div>

    <div class="form-group">
        <label for="contactNumber">Contact Number</label>
        <div class="input-group">
            <input type="text" class="form-control" id="phoneCode" readonly="">
            <input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Enter Contact Number">
        </div>
    </div>

    <div class="form-group">
        <input type="password" class="form-control" name="password" placeholder="Input Password">
    </div>

    <div class="form-group">
        <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="Submit" value="Register">
    </div>
</form>
    <div><p>Already registered? <a href="index.php"> Login Here</a></div>
</div>
<script>
    let data = [];

    document.addEventListener('DOMContentLoaded', function() {
        fetch('https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/countries%2Bstates%2Bcities.json')
            .then(response => response.json())
            .then(jsonData => {
                data = jsonData;
                const countries = data.map(country => country.name);
                populateDropdown('country', countries);
            })
            .catch(error => console.error('Error fetching countries:', error));
    });

    function populateDropdown(dropdownId, data) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.innerHTML = '';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.text = item;
            dropdown.add(option);
        });
    }

    document.getElementById('country').addEventListener('change', function() {
        const selectedCountry = this.value;
        const countryData = data.find(country => country.name === selectedCountry);
        if (countryData && countryData.states) {
            const states = countryData.states.map(state => state.name);
            populateDropdown('state', states);
        }
        const phoneCode = countryData ? countryData.phone_code : '';
        document.getElementById('phoneCode').value = phoneCode;
    });

    document.getElementById('state').addEventListener('change', function() {
        const selectedState = this.value;
        const countryData = data.find(country => country.name === document.getElementById('country').value);
        if (countryData) {
            const stateData = countryData.states.find(state => state.name === selectedState);
            if (stateData && stateData.cities) {
                const cities = stateData.cities.map(city => city.name);
                populateDropdown('city', cities);
            } else {
                console.log('No cities found for state:', selectedState);
            }
        } else {
            console.log('Country data not found for state:', selectedState);
        }
    });
</script>
<script src="script.js"></script>
</body>
</html>