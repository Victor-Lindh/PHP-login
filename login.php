<?php require 'check_session.php'?>
<?php require 'journal-connect.php'?>
<html>
<head>
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="regDiv">
<form action="?action=register" method="POST">
<fieldset>
<legend>Registrera dig!</legend>
<label for="username">Användarnamn</label><br>
<input type="text" name="username">
<br><br>
<label for="passw">Lösenord</label><br>
<input type="password" name="passw">
<br><br>
<button type="submit" id="regUser">Registrera dig!</button>
</fieldset>
</form>
</div>

  <div id="logindiv">
<form action="?action=login" method="POST">
<fieldset>
  <legend>Logga in</legend>
    <label for="username">Användarnamn</label><br>
    <input type="text" name="username">
    <br><br>
    <label for="passw">Lösenord</label><br>
    <input type="password" name="passw">
    <br><br>
    <button type="submit" id="submit">Logga in</button>
</fieldset>
</form>
  </div>
  <div id="msgDiv" class="hide">
<p id="msg"></p>

  </div>
  <?php
    if(isset($_GET['action']) && $_GET['action'] == 'login'){

      $statement = $pdo->prepare("SELECT * FROM users WHERE username = :username");
      $statement->execute([":username" => $_POST['username']]);
      $user = $statement->fetch(PDO::FETCH_ASSOC);    

      if(password_verify($_POST['passw'], $user['password'])){
        session_start();
        $_SESSION['username'] = $user['username'];
        header('Location: loggedin.php');
      }else{
        ?>
        <script>
                      document.getElementById("msgDiv").classList.remove("hide");
            document.getElementById("msgDiv").classList.add("show");
        document.getElementById("msg").innerHTML = "Fel användarnamn/lösenord.";
        </script>
        <?php
      }
    }
    
  
  if(isset($_GET['action']) && $_GET['action'] == 'register'){
    $user_query = "SELECT * FROM users WHERE username = :username";
    $statement = $pdo->prepare($user_query);
    $statement->execute([':username' => $_POST['username']]);
    $check_username = $statement->fetch(PDO::FETCH_ASSOC);

    if($check_username > 0){
      ?>
      <script>
            document.getElementById("msgDiv").classList.remove("hide");
            document.getElementById("msgDiv").classList.add("show");
            document.getElementById("msg").innerHTML = "Användarnamnet är upptaget.";
      </script>
      <?php
    }elseif($_POST['passw'] == ""){
      ?>
      <script>
            document.getElementById("msgDiv").classList.remove("hide");
            document.getElementById("msgDiv").classList.add("show");
            document.getElementById("msg").innerHTML = "Lösenordet får inte vara tomt!";
      </script>
      <?php
    }elseif($_POST['username'] == ""){
      ?>
      <script>
      document.getElementById("msgDiv").classList.remove("hide");
      document.getElementById("msgDiv").classList.add("show");
      document.getElementById("msg").innerHTML = "Användarnamnet får inte vara tomt!";
      </script>
      <?php
    
      }else{
    $create_user = "INSERT INTO users (username,password)
                    VALUES (:username,:password);";
    $statement = $pdo->prepare($create_user);
    $statement->execute(['username' => $_POST['username'],'password' => password_hash($_POST['passw'], PASSWORD_BCRYPT)]);
    ?>
    <script>
      document.getElementById("msgDiv").classList.remove("hide");
      document.getElementById("msgDiv").classList.add("show");
      document.getElementById("msg").innerHTML = "Välkommen, <?php echo $_POST['username'];?>!<br>Nu kan du logga in med det<br>användarnamn och lösenord du valt."
    
    </script>
    <?php
  }
}
  ?>


  </body>
  </html>