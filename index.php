<?php

$insert = false;
$update = false;

# connect to database
$serverName = "localhost";
$username = "root";
$password = "";
$database = "myDB";

# create connection
$conn = mysqli_connect(
    $serverName,
    $username,
    $password,
    $database,
);

# check connection
if (!$conn) {
    die("Failed to connect" . mysqli_connect_error());
}

# create database 

// $sql = "CREATE DATABASE myDB";

#  check the database creation
// if (mysqli_query($conn, $sql)) {
//   echo "<br>Database created successfully<br>";
// } else {
//   echo "Error creating Database: " . mysqli_error($conn) . "<br><hr/>";
//   exit();
// };

# Create new table in the data base 

// $sql = "CREATE TABLE `users` (`sno` INT(11) NOT NULL AUTO_INCREMENT , `title` VARCHAR(50) NOT NULL , `description` VARCHAR(80) NOT NULL , `created at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`sno`)) 
// ​";

# check the table is created 
// if ($conn->query($sql) === TRUE) {
//   echo "Table created successfully";
// } else {
//   echo "Error creating table: " . $conn->error;
// }
// $conn->close()

# update or insert 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['snoEdit'])) {
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];

        #update query
        $sql = "UPDATE `users` SET `description` = ?, `title` = ? WHERE `sno` = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $description, $title, $sno);

        if (mysqli_stmt_execute($stmt)) {
            $update = true;
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {

        $title = $_POST["title"];
        $description = $_POST["description"];

        # Insert query 
        $sql = ("INSERT INTO `users` (`sno`, `title`, `description`, `created at`) VALUES (NULL, '$title', '$description', CURRENT_TIMESTAMP());
      ");
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $insert = true;
        } else {
            echo "the record was not created: " . $conn->error;
        }
    }
}

# delete 
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    # delete query
    $sql = "DELETE FROM `users` WHERE `sno`= $sno";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>php crud</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <form action="/cred/index.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="snoEdit" id="snoEdit">
                        <div class="form-group">
                            <label for="title">Note Title</label>
                            <input type="text" class="form-control" id="titleEdit" name="titleEdit" aria-describedby="emailHelp">
                        </div>

                        <div class="form-group">
                            <label for="desc">Note Description</label>
                            <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-block mr-auto">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <?php
    if ($insert) {
        echo "<div class='container my-3 alert alert-success' role='alert'>
              Note has been added successfully!
            </div>";
    }
    ?>
    <?php
    if ($update) {
        echo "<div class='container my-3 alert alert-warning' role='alert'>
              Note has been updated successfully!
            </div>";
    }
    ?>

    <div class="container my-5">
        <h2>add a note</h2>

        <form action="/cred/index.php" method="post">
            <div class="form-group">
                <label for="title">Note Title </label>
                <input type="text" class="form-control" name="title" id="title" />
            </div>
            <div class="form-group">
                <label for="description">Note description </label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Add Note</button>
        </form>

    </div>
    <div class="container" id="myTable">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.no.</th>
                    <th scope="col">Title</th>
                    <th scope="col">description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `users`";
                $result = mysqli_query($conn, $sql);
                $sno = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $sno = $sno + 1;

                    echo   " <tr>
            <th scope='row'>" . $sno . "</th>
            <td>" . $row['title'] . "</td>
            <td>" . $row['description'] . "</td>
            <td>
            <button class='edit btn btn-sm btn-primary' id=" . $row['sno'] . "'>Edit</button>
  
            <button class='delete btn btn-sm btn-danger' id=d" . $row['sno'] . "'>Delete</button>
            </td>
          </tr";
                }
                ?>

            </tbody>
        </table>
    </div>


    <script>
        edits = document.getElementsByClassName('edit');
        Array.from(edits).forEach((element) => {
            element.addEventListener('click', (e) => {
                console.log("edits", );
                tr = e.target.parentNode.parentNode;
                title = tr.getElementsByTagName('td')[0].innerText;
                description = tr.getElementsByTagName('td')[1].innerText;
                console.log(title, description);
                titleEdit.value = title;
                descriptionEdit.value = description;
                snoEdit.value = e.target.id
                console.log(e.target.id);
                $('#editModal').modal('toggle');
            })
        })
        deletes = document.getElementsByClassName('delete');
        Array.from(deletes).forEach((element) => {
            element.addEventListener('click', (e) => {
                console.log("delete button clicked", );

                sno = e.target.id.substr(1)
                sno = sno.replace(/[^0-9]/g, '');
                console.log(sno);
                if (confirm("do you want to delete this note? ")) {
                    console.log("yes");
                    window.location = `/cred/index.php?delete=${sno}`
                } else {
                    console.log("no");

                }

            })
        })
    </script>

</body>

</html>