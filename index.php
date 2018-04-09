<html>
<link rel="stylesheet" href="styles.css">
<body>
<?php
/**
 * Created by PhpStorm.
 * User: Dale
 * Date: 2018/4/9
 * Time: 15:10
 */
    $hostname = "sql2.njit.edu";
    $username = "yl622";
    $password = "evPkHDGVf";
    $conn = NULL;
    try
    {
        $conn = new PDO("mysql:host=$hostname;dbname=$username", $username, $password);
        echo "Connected successfully<br>";
    }
    catch(PDOException $e)
    {
        // echo "Connection failed: " . $e->getMessage();
        http_error("500 Internal Server Error\n\n"."There was a SQL error:\n\n" . $e->getMessage());
    }
    // Runs SQL query and returns results (if valid)
    function runQuery($query) {
        global $conn;
        try {
            $q = $conn->prepare($query);
            $q->execute();
            $results = $q->fetchAll();
            $q->closeCursor();
            return $results;
        } catch (PDOException $e) {
            http_error("500 Internal Server Error\n\n"."There was a SQL error:\n\n" . $e->getMessage());
        }
    }
    function http_error($message)
    {
        header("Content-type: text/plain");
        die($message);
    }
    function print_table($array, $table_title = NULL){
        print("<table>");
        if ($table_title != NULL){
            $col_count = count($array[0]);
            print("<tr><th colspan = $col_count>$table_title</th></tr>");
        }
        foreach ($array as $line_num=>$line){
            if ($line_num == 0){
                print("<tr>");
                $count_col = 0;
                foreach ($line as $col_name => $columns){
                    if ($count_col%2 ==0){
                        print("<th>$col_name</th>");
                    }
                    $count_col++;
                }
                print("</tr>");
            }
            print("<tr>");
            $count_col = 0;
            foreach ($line as $col_name => $columns){
                if ($count_col%2 ==0) {
                    print("<td>$columns</td>");
                }
                $count_col++;
            }
            print("</tr>");
        }
        print("</table>");
    }

    class User {
        function display(){
            $query = "SELECT * FROM accounts";
            $result = runQuery($query);
            return $result;
        }
        function delete($id){
            $query = "DELETE FROM accounts WHERE id = $id";
            $result = runQuery($query);
            return $result;
        }
        function insert($id, $email, $fname, $lname, $phone, $birthday, $gender, $password){
            $query = "INSERT INTO `accounts`(`id`, `email`, `fname`, `lname`, `phone`, `birthday`, `gender`, `password`) VALUES ($id,'$email','$fname','$lname','$phone','$birthday','$gender','$password')";
            $result = runQuery($query);
            return $result;
        }
        function update($id, $password){
            $query = "UPDATE accounts SET password=$password WHERE id = $id";
            $result = runQuery($query);
            return $result;
        }
    }

    $output = new User;
    print_table($output->display(), "Display Table");
    print("<br>");
    $output->insert(30, "yl30@njit.edu", "Y", "L", "123-456-7890", "2000-01-01", "male", "11111");
    print_table($output->display(), "Results After Insert");
    print("<br>");
    $output->update(30, 123456);
    print_table($output->display(), "Results After Update");
    print("<br>");
    $output->delete(30);
    print_table($output->display(), "Results After delete");
?>
</body>
</html>
