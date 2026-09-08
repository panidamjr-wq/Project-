<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>FlauntFit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:#FFF ;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }    
        
         .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px; /* ระยะห่างระหว่างโลโก้และฟอร์ม */
        }

        .logo {
            width: 250px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container {
            background-color: rgba(255, 169, 169, 0.7); /* พื้นหลังโปร่งใส */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        h1 {
            color: #000;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid#999;
            border-radius: 4px;
            background-color: #FFFFFF;
            color: #000000;
        }

        .form-buttons input {
            background-color: #6F6;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .form-buttons input[type="reset"] {
            background-color: #F00;
        }

        .form-buttons input:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <img src="lg.png" alt="FlauntFit Logo" class="logo">
        <div class="container">
            <h1>FlauntFit</h1> <!-- เปลี่ยนชื่อหัวข้อเป็น FlauntFit -->
            <form action="#" method="post">
                <div class="form-group">
                    <label for="username">Username :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="name">Name-Surname :</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="address">Address :</label>
                    <textarea id="address" name="address" rows="4"></textarea>
                </div>
                <div class="form-buttons">
                    <input type="submit" value="Register">
                    <input type="reset" value="Clear">
                </div>
            </form>
        </div>
    </div>

</body>
</html>
