
<?php
include "db.php";

$message = "";

if (isset($_POST["submit"])) {

    $name = trim($_POST["Name"] ?? "");
    $email = trim($_POST["Email"] ?? "");

    if ($name !== "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $sql = "INSERT INTO simpletb (Name, Email) VALUES (?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "ss", $name, $email);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Data saved successfully!";
            } else {
                $message = "Error saving data.";
            }

            mysqli_stmt_close($stmt);

        } else {
            $message = "Database query failed.";
        }

    } else {
        $message = "Please enter a valid name and email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simple Registration Form</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f0eb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            width: 350px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #a52d2d;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        input:focus {
            outline: none;
            border-color: #a52d2d;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #a52d2d;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #792020;
        }

        .message {
            text-align: center;
            color: #287a40;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>

    <div class="card">

        <h2>Registration Form</h2>

        <?php if ($message !== ""): ?>
            <p class="message">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">

            <label>Full Name</label>

            <input
                type="text"
                name="Name"
                placeholder="Enter your name"
                required
            >

            <label>Email</label>

            <input
                type="email"
                name="Email"
                placeholder="Enter your email"
                required
            >

            <button type="submit" name="submit">
                Register Now
            </button>

        </form>

    </div>

</body>
</html>
