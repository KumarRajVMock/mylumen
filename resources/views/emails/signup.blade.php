<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Dear <?php echo $name; ?></h2>
    <p>localhost:8000/api/verify?token=<?php echo $token; ?>  </p>

    <a href="{{url('http://localhost:3000/verify/'.$token)}}">Verify Email</a>

    <!-- <h1>Your email is verified!</h1> -->
</body>
</html>