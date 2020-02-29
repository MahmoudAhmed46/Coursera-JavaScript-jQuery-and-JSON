<?php
require_once  'pdo.php';
session_start();
?>
<html>
    <head>
        <title>Mahmoud Ahmed Mahmoud</title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet"
              href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
              integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
              crossorigin="anonymous">

        <link rel="stylesheet"
              href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
              integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
              crossorigin="anonymous">

        <script
                src="https://code.jquery.com/jquery-3.2.1.js"
                integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
                crossorigin="anonymous"></script>
    </head>
    <body>
    <div class="content">
        <h1>Chuck Severance's Resume Registry</h1>
        <?php
        if(isset($_SESSION['user_id']))
        echo "<p><a href=\"logout.php\">Logout</a></p>";
        else
           echo "<p><a href=\"login.php\">Please log in</a></p>"
            ?>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<h3 style='color: forestgreen;text-align: center;'>" . $_SESSION['success'] . "</h3>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<h3 style='color: red;text-align: center;'>" . $_SESSION['error'] . "</h3>";
            unset($_SESSION['error']);
        }
        $stmt = $pdo->prepare("SELECT DISTINCT profile_id, first_name,last_name , headline from users join  Profile on  profile.user_id = :id");
        if(isset($_SESSION['user_id'])){
            $id=$_SESSION['user_id'];
            $stmt->execute(array(":id"=>$id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //echo $_SESSION['user_id'];
       // $stmt->execute(array(":id"=>$id));
        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
        foreach($rows as $res) {
            if($i==0){
                echo('<table border="1">' . "\n");
                echo "<tr><th>Name</th><th>HeadLine</th><th>Action</th><tr>";
            $i=1;
            }
            echo "<tr><td>";
            //echo "<a href='view.php?profile_id='".$res['profile_id'].">". htmlentities($res['first_name']) . " " . htmlentities($res['last_name']) . "</a></td>";
            //echo ('<a href="view.php?profile_id='.$res['profile_id'].'">View</a></td>');
            echo("<a href='view.php?profile_id=" . $res['profile_id'] . "'>" . $res['first_name']." ". $res['last_name']  . "</a>");
            echo("</td>");
            echo "<td>" . htmlentities($res['headline']) . "</td>";
            echo "<td>";
            echo('<a href="edit.php?profile_id=' . $res['profile_id'] . '">Edit</a> ');
            echo('<a href="delete.php?profile_id=' . $res['profile_id'] . '">Delete</a>');
            echo "</td></tr>\n";
        }
    }
        ?>
        <a href="add.php">Add New Entry</a>
        <p>
            <b>Note:</b> Your implementation should retain data across multiple
            logout/login sessions.  This sample implementation clears all its
            data periodically - which you should not do in your implementation.
        </p>
        </table>
    </div>
    </body>
</html>