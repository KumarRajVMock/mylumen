<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<p>localhost:8000/api/resetpassword?token=<?php echo $token; ?>&new_password=  </p>
<a href="{{url('http://localhost:3000/resetpassword/'.$token)}}">Reset your Password</a>
</body>
</html>