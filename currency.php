<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</head>

<body style="background-color:#212529;">
    <div style="width: 40%;
     border: 1px solid #212529;
    padding: 10px 15px 10px 15px;
    margin-left: 30%;
    background-color:white;
    margin-top:5%;">

        <h3 style="text-align: center;">CURRENCY CONVERTER</h3>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <div style="font-size:1.2em;">
                <label class="form-label" for="from">
                    From:
                </label><br>
                <select class="form-select" id="from" name="from" required>
                    <option disabled>-Select currency-</option>
                    <option value="USD">USD (US Dollar)</option>
                    <option value="EUR">EUR (Euro)</option>
                    <option value="INR">INR (Rupees)</option>
                </select>
            </div><br>
            <div style="font-size:1.2em;">
                <label class="form-label" for="to">
                    To:
                </label><br>
                <select class="form-select" id="to" name="to" required>
                    <option disabled>-Select currency-</option>
                    <option value="EUR">EUR (Euro)</option>
                    <option value="INR">INR (Rupees)</option>
                    <option value="USD">USD (US Dollar)</option>
                </select>
            </div><br>
            <div style="font-size:1.2em;">
                <label for="amount" class="form-label">Amount:</label>
                <input class="form-control" type="text" id="amount" name="amount" placeholder="Enter amount" required>
                <div><br>
                    <input type="submit" value="Convert Currency" class="btn btn-dark w-100">
        </form>
    </div>
    <?php
// Database Configuration
$hostname = "localhost";
$username = "root";
$password = "mysql123";
$database = "fifth";

// Establish a database connection
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input from the form
    $amount = $_POST['amount'];
    $fromCurrency = $_POST['from'];
    $toCurrency = $_POST['to'];

    // Create a cURL request to fetch the conversion data
    $ch = curl_init();
    $url = "https://v6.exchangerate-api.com/v6/272cb4f40380e548bb73eb29/pair/$fromCurrency/$toCurrency/$amount";
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $jsonObj = json_decode($result);

    // Check if the conversion result is available
    if (isset($jsonObj->conversion_result)) {
        $conversionResult = $jsonObj->conversion_result;

        // Insert the data into the database
        $insertQuery = "INSERT INTO conversion_data (from_currency, to_currency, amount, conversion_result) 
                        VALUES ('$fromCurrency', '$toCurrency', '$amount', '$conversionResult')";

        if (mysqli_query($conn, $insertQuery)) {
            echo "<h2>Conversion Result:</h2>";
            echo "<p>$amount $fromCurrency is equal to $conversionResult $toCurrency</p>";
        } else {
            echo "<h2>Error:</h2>";
            echo "<p>Error inserting data into the database: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<h2>Error:</h2>";
        echo "<p>Unable to fetch conversion rate. Please check your input and try again.</p>";
    }
} else {
    // Handle cases where the form is not submitted
    echo "<h2>Error:</h2>";
    echo "<p>Invalid request.</p>";
}

// Close the database connection
mysqli_close($conn);
?>


</body>

</html>
