<?php
$conn = new mysqli("localhost", "root", "", "ajax_crud");
if ($conn->connect_error) {
    die("Connection Failed : " . $conn->connect_error);
}
if (isset($_POST['action'])) {

    if ($_POST['action'] == 'insert') {

        $name   = $_POST['name'];
        $email  = $_POST['email'];
        $mobile = $_POST['mobile'];
        $status = $_POST['status'];

        $sql = "INSERT INTO users(name,email,mobile,status)
                VALUES('$name','$email','$mobile','$status')";

        $conn->query($sql);
    }


    if ($_POST['action'] == 'fetch') {

        $data = $conn->query("SELECT * FROM users ORDER BY id DESC");

        while ($row = $data->fetch_assoc()) {

            $statusText = $row['status'] == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            echo "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['mobile']}</td>
                <td>{$statusText}</td>
                <td>{$row['created_at']}</td>
                <td>{$row['updated_at']}</td>

                <td>
                    <button
                        class='edit btn btn-primary btn-sm'
                        data-id='{$row['id']}'
                        data-name='{$row['name']}'
                        data-email='{$row['email']}'
                        data-mobile='{$row['mobile']}'
                        data-status='{$row['status']}'>
                        Edit
                    </button>

                    <button
                        class='delete btn btn-danger btn-sm'
                        data-id='{$row['id']}'>
                        Delete
                    </button>
                </td>
            </tr>
            ";
        }
    }


    if ($_POST['action'] == 'update') {

        $id     = $_POST['id'];
        $name   = $_POST['name'];
        $email  = $_POST['email'];
        $mobile = $_POST['mobile'];
        $status = $_POST['status'];

        $sql = "UPDATE users SET
                    name='$name',
                    email='$email',
                    mobile='$mobile',
                    status='$status'
                WHERE id='$id'";

        $conn->query($sql);
    }


    if ($_POST['action'] == 'delete') {

        $id = $_POST['id'];

        $conn->query("DELETE FROM users WHERE id='$id'");
    }

    exit;
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>PHP AJAX CRUD</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>

<body>

<div class="container mt-5">

    <h2 class="mb-4">PHP AJAX CRUD Operation</h2>


    <div class="card p-4 mb-4">

        <input type="hidden" id="id">

        <div class="row">

            <div class="col-md-3 mb-3">
                <label>Name</label>
                <input type="text" id="name" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Email</label>
                <input type="email" id="email" class="form-control">
            </div>

            <div class="col-md-2 mb-3">
                <label>Mobile</label>
                <input type="text" id="mobile" class="form-control">
            </div>

            <div class="col-md-2 mb-3">
                <label>Status</label>

                <select id="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="col-md-2 mb-3 d-flex align-items-end">

                <button id="save" class="btn btn-success w-100">
                    Save
                </button>

            </div>

        </div>

    </div>

      <table class="table table-bordered table-striped">

        <thead class="table-dark">

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th width="150">Action</th>
            </tr>

        </thead>

        <tbody id="table-data"></tbody>

    </table>

</div>

<script>

$(document).ready(function(){

    function loadData(){

        $.ajax({
            url: "index.php",
            type: "POST",
            data: {action:'fetch'},

            success:function(data){
                $("#table-data").html(data);
            }
        });

    }
    loadData();
    $("#save").click(function(){

        let id      = $("#id").val();
        let name    = $("#name").val();
        let email   = $("#email").val();
        let mobile  = $("#mobile").val();
        let status  = $("#status").val();

        if(name == '' || email == '' || mobile == ''){
            alert("All fields are required");
            return;
        }
        let action = (id == '') ? 'insert' : 'update';

        $.ajax({

            url: "index.php",
            type: "POST",

            data: {
                id:id,
                name:name,
                email:email,
                mobile:mobile,
                status:status,
                action:action
            },
            success:function(){

                loadData();            

                $("#id").val('');
                $("#name").val('');
                $("#email").val('');
                $("#mobile").val('');
                $("#status").val('1');
                $("#save").text('Save');

            }

        });

    });

    $(document).on("click", ".edit", function(){
        $("#id").val($(this).data('id'));
        $("#name").val($(this).data('name'));
        $("#email").val($(this).data('email'));
        $("#mobile").val($(this).data('mobile'));
        $("#status").val($(this).data('status'));
        $("#save").text('Update');

    });

   $(document).on("click", ".delete", function(){
        if(confirm("Are you sure want to delete?")){
            let id = $(this).data('id');
            $.ajax({

                url: "index.php",
                type: "POST",

                data: {
                    id:id,
                    action:'delete'
                },

                success:function(){
                    loadData();
                }

            });

        }

    });

});

</script>

</body>
</html>