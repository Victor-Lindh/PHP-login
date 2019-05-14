<?php require 'journal-connect.php'?>
<?php require 'check_session.php'?>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<script>document.getElementById("emptyDiv").classList.remove("show");</script>
</head>
<body id="loggedIn">
<header>    
<h2 class="greeting"><?php echo $_SESSION['username']?>'s journal</h2>
<button id="logOutBtn"><a href='logout.php' type="submit">Logga ut</a></button>
</header>
<main>

<div id="newEntryDiv">
<form action="?action=send" method="POST" id="entryForm">
<fieldset>
<legend>Skriv ett nytt inlägg här!</legend>
<label for="title">Rubrik</label><br>
<input type="text" name="title" maxlength="100" placeholder="Rubrik(max 100 tecken)"><br><br>
<label for="content">Inlägg</label><br>
<textarea name="content" cols="40" rows="10" maxlength="100" placeholder="Skriv ditt inlägg här!&#10;Max 100 tecken!"></textarea>
<br>
<button type="submit">Skicka inlägg</button>
</fieldset>
</form>
<div id="emptyDiv" class="hide">
<p id="emptyMsg"></p>

  </div>
</div>

<div id="contentDiv">
<h3>Dina senaste inlägg:</h3><br>

    <?php
    if(isset($_SESSION['username'])){
$user_id_query = "SELECT userID FROM users WHERE username = :username";
$statement1 = $pdo->prepare($user_id_query);
$statement1->execute([':username' => $_SESSION['username']]);
$user_id = $statement1->fetch(PDO::FETCH_ASSOC);



}

$get_posts_query = "SELECT * FROM entries WHERE userID = {$user_id['userID']}";
$statement2 = $pdo->prepare($get_posts_query);
$statement2->execute();
$posts_data = $statement2->fetchAll(PDO::FETCH_ASSOC);

if (empty($posts_data)) {
    echo "<p id='noContent'>Det finns inga inlägg att visa.</p>";
}else{
foreach ($posts_data as $posts) {
    echo "<div class='postDiv'><h1>".$posts['title']."</h1>";
    echo "<p>".$posts['content']."</p><br><small>Skrivet av ".$_SESSION['username'].", den ".$posts['createdAt']."</small><br><br>";
    echo "<button class='deleteEntry'><a href=?delete=".$posts['entryID'].">Radera</a></button>";
    echo "</div>";
  }
}
?>

    </div>
    </main>

<?php


if(isset($_GET['action']) == 'send'){
if(empty($_POST['title']) || empty($_POST['content'])){
  ?>
    <script>
      document.getElementById("emptyDiv").classList.remove("hide");
      document.getElementById("emptyDiv").classList.add("show");
      document.getElementById("emptyMsg").innerHTML = "Du kan inte skicka tomma fält!";
      </script>
      <?php
}else{
  date_default_timezone_set("Europe/Stockholm");
  
  $user_id_query = "SELECT userID FROM users WHERE username = :username";
  $statement1 = $pdo->prepare($user_id_query);
  $statement1->execute([':username' => $_SESSION['username']]);
  $user_id = $statement1->fetch(PDO::FETCH_ASSOC);
  
  $new_post = "INSERT INTO entries (title,content,createdAt,userID)
                  VALUES (:title,:content,:createdAt,:userID)";
    $statement2 = $pdo->prepare($new_post);
    $statement2->execute([':title' => $_POST['title'],':content' => $_POST['content'],':createdAt' => date('Y-m-d, H:i:s'), ':userID' => $user_id['userID']]);
    header("Location: loggedin.php");
    
  }
}

if(isset($_GET['delete'])){

    $delete_query = "DELETE FROM entries WHERE entryID = ?";
    $statement3 = $pdo->prepare($delete_query);
    $statement3->execute([$_GET['delete']]);
    header("Location: loggedin.php");
}
    
?>
    </body>
</html>