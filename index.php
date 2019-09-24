<?php
echo 'test';
$dsn = "mysql:dbname=test;host=localhost";
$user = "root";
$pass = "987643210";
$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

try {

    $connect = new PDO ($dsn, $user, $pass , $option);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch (PDOException $e) {

    echo "failed" . $e;
}

if (isset($_POST['EditBtn'])){
    $EditBtn = $_POST['EditBtn'];
    $users = $connect->prepare('select * from users
                                         where id = ? Limit 1');
    $users->execute(array($EditBtn));
    $result_users = $users->fetch();
    echo json_encode($result_users);
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test</title>
</head>
<body>

<h1>User Table</h1>
<table style="width: 100%;text-align: center" border="3">
    <tr>
        <th>id</th>
        <th>name</th>
        <th>type</th>
        <th>edit</th>
    </tr>
    <?php
    $users = $connect->prepare('select users.*, 
                                         type.name as typeN
                                         from users
                                         inner join type
                                         on users.type = type.id
                                         ');
    $users->execute(array());
    $result_users = $users->fetchAll();
    foreach ($result_users as $row){
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['typeN'].'</td>';
        echo '<td><button value="'.$row['id'].'" class="EditBtn">Edit</button></td>';
        echo '</tr>';
    }
    ?>
</table>
<hr>
<h1>Type Table</h1>
<table style="width: 100%;text-align: center" border="3">
    <tr>
        <th>id</th>
        <th>name</th>
    </tr>
    <?php
    $type = $connect->prepare('select type.*
                                         from type
                                         ');
    $type->execute(array());
    $result_type = $type->fetchAll();
    foreach ($result_type as $row){
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '</tr>';
    }
    ?>
</table>
<hr>
<h1>Edit User momo From Load</h1>
<label for="name">Name</label>
<input type="text" id="name" value="<?php echo $result_users[2]['name']?>">

<label for="type">Type</label>
<select id="type">
    <option value="">choose</option>
    <?php
    foreach ($result_type as $row){
        echo '<option value="'.$row['id'].'"';
        if ($row['id'] === $result_users[2]['type']){
            echo ' selected ';
        }else{
            echo '';
        }
        echo '>';
        echo $row['name'];
        echo '</option>';
    }
    ?>
</select>
<hr>
<h1>Edit User From Ajax</h1>
<label for="EditName">Name</label>
<input type="text" id="EditName">

<label for="EditType">Type</label>
<select id="EditType">
    <option value="">Choose</option>
    <?php
    foreach ($result_type as $row){
        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }
    ?>
</select>
<script src="jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('.EditBtn').click(function () {
            let EditBtn = $(this).val();
            $.ajax({
                url:'index.php',
                method: 'post',
                data:{EditBtn:EditBtn},
                success:function(data) {
                    let json = JSON.parse(data);
                    $('#EditName').val(json['name']);
                    $('#EditType').val(json['type']);
                }
            });
        });
    });
</script>
</body>
</html>