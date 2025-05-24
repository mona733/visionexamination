                <?php
                include 'config.php';

                // Handle all database operations
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Edit existing record
                    if (isset($_POST['edit'])) {
                        $table = $_POST['table'];
                        $id = $_POST['id'];
                        $field = $_POST['field'];
                        $value = mysqli_real_escape_string($con, $_POST['value']);
                        
                        mysqli_query($con, "UPDATE $table SET $field = '$value' 
                                        WHERE ".getPrimaryKey($table)." = $id");
                    }
                    
                    // Add new record
                    if (isset($_POST['add'])) {
                        $table = $_POST['table'];
                        $columns = [];
                        $values = [];
                        
                        foreach ($_POST as $key => $val) {
                            if (!in_array($key, ['table', 'add', 'created_at', 'updated_at'])) {
                                $columns[] = $key;
                                $values[] = "'".mysqli_real_escape_string($con, $val)."'";
                            }
                        }
                        
                        mysqli_query($con, "INSERT INTO $table (".implode(',', $columns).")
                                        VALUES (".implode(',', $values).")");
                    }
                    
                    // Delete record
                    if (isset($_POST['delete'])) {
                        $table = $_POST['table'];
                        $id = $_POST['id'];
                        
                        mysqli_query($con, "DELETE FROM $table 
                                        WHERE ".getPrimaryKey($table)." = $id");
                    }
                }

                function getPrimaryKey($table) {
                    switch ($table) {
                        case 'vision_tests': return 'test_id';
                        case 'health_tips': return 'tip_id';
                        case 'articles': return 'article_id';
                    }
                }

                function displayTable($con, $tableName) {
                    $result = mysqli_query($con, "SELECT * FROM $tableName");
                    $columns = [];
                    
                    echo "<div class='table-container'>";
                    echo "<h3>".ucfirst(str_replace('_', ' ', $tableName))."</h3>";
                    
                    // Display table
                    echo "<table class='editable-table'>";
                    echo "<thead><tr>";
                    while ($field = mysqli_fetch_field($result)) {
                        $columns[] = $field->name;
                        echo "<th>".ucfirst($field->name)."</th>";
                    }
                    echo "<th></th></tr></thead>";
                    
                    // Table body
                    echo "<tbody>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        foreach ($row as $key => $value) {
                            if (in_array($key, ['created_at', 'updated_at'])) {
                                echo "<td>$value</td>";
                            } else {
                                echo "<td data-table='$tableName' 
                                        data-field='$key' 
                                        data-id='{$row[getPrimaryKey($tableName)]}'
                                        class='editable'>$value</td>";
                            }
                        }
                        echo "<td>
                                <form method='POST'>
                                <input type='hidden' name='table' value='$tableName'>
                                <input type='hidden' name='id' value='{$row[getPrimaryKey($tableName)]}'>
                                <button type='submit' name='delete' class='btn-delete'>حذف</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    
                    // Add new form
                    echo "<div class='add-form'>
                            <h4> اضافة صف جديد</h4>
                            <form method='POST'>
                            <input type='hidden' name='table' value='$tableName'>";
                    foreach ($columns as $col) {
                        if (!in_array($col, [getPrimaryKey($tableName), 'created_at', 'updated_at'])) {
                            echo "<input type='text' name='$col' placeholder='$col' required>";
                        }
                    }
                    echo "<button type='submit' name='add'>اضافة</button>
                            </form>
                        </div></div>";
                }
                ?>

                <!DOCTYPE html>
                <html lang="en"  dir="rtl">
                <head>
                    <meta charset="UTF-8">
                    <title>لوحةالتحكم-الصفحه الرئيسية</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
                    </script>
                    <style>
                        .table-container {
                            margin: 2rem;
                            padding: 1rem;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            background: white;
                            border:3px groove #8cb4e8;
                        }
                        
                        .editable-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 1rem 0;
                        }
                        
                        .editable-table td, .editable-table th {
                            border: 1px solid #ddd;
                            padding: 8px;
                            min-width: 100px;
                        }
                        
                        .editable-table th {
                            background: #1e3799;
                            color: white;
                        }
                        
                        .editable {
                            cursor: pointer;
                            transition: background 0.3s;
                        }
                        
                        .editable:hover {
                            background: #f0f8ff;
                        }
                        
                        .btn-delete {
                            background: #dc3545;
                            color: white;
                            border: none;
                            padding: 4px 8px;
                            border-radius: 4px;
                            cursor: pointer;
                        }
                        
                        .add-form {
                            margin-top: 1rem;
                            padding: 1rem;
                            border-top: 2px solid #eee;
                            border:3px groove #8cb4e8;
                        }
                        
                        .add-form input {
                            margin: 4px;
                            padding: 6px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            width: 200px;
                            border:3px groove #8cb4e8;
                        }
                        
                        .add-form button {
                            background: #1e3799;
                            color: white;
                            border: none;
                            padding: 8px 16px;
                            border-radius: 4px;
                            cursor: pointer;
                        }
                        
                        .non-editable {
                            background: #1e3799;
                            cursor: not-allowed;
                        }
                        h1
                        {
                        color: #1e3799;
                        }
                    </style>
                </head>
                <body>
                    <div id="nav"></div>
                    <div class="container-fluid">
                        <h1 class="my-4">لوحةالتحكم-الصفحه الرئيسية</h1>
                        
                        <?php
                        $tables = ['vision_tests', 'health_tips', 'articles'];
                        foreach ($tables as $table) {
                            displayTable($con, $table);
                        }
                        ?>
                    </div>

                    <script>
                    document.querySelectorAll('.editable').forEach(cell => {
                        cell.addEventListener('click', function() {
                            const value = this.innerText;
                            const input = document.createElement('input');
                            input.value = value;
                            
                            this.innerHTML = '';
                            this.appendChild(input);
                            input.focus();
                            
                            input.addEventListener('blur', () => saveChanges(this, input.value));
                            input.addEventListener('keypress', (e) => {
                                if (e.key === 'Enter') saveChanges(this, input.value);
                            });
                        });
                    });
                    
                    function saveChanges(cell, newValue) {
                        const formData = new FormData();
                        formData.append('edit', true);
                        formData.append('table', cell.dataset.table);
                        formData.append('field', cell.dataset.field);
                        formData.append('id', cell.dataset.id);
                        formData.append('value', newValue);
                        
                        fetch('', {
                            method: 'POST',
                            body: formData
                        }).then(() => {
                            cell.innerHTML = newValue;
                            location.reload();
                        });
                    }
                    </script>
                </body>
</html>