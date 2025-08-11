<?php
require('../includes/connection.php');
require('session.php');
if (isset($_POST['btnlogin'])) {
    $users = trim($_POST['user']);
    $upass = trim($_POST['password']);
    $h_upass = sha1($upass);
    
    if ($upass == '') {
        ?>    
        <script type="text/javascript">
            alert("Password is missing!");
            window.location = "login.php";
        </script>
        <?php
    } else {
        // Create SQL statement             
        $sql = "SELECT ID, e.FIRST_NAME, e.LAST_NAME, e.GENDER, e.EMAIL, e.PHONE_NUMBER, 
                       j.JOB_TITLE, l.PROVINCE, l.CITY, t.TYPE, u.TYPE_ID
                FROM `users` u
                JOIN `employee` e ON e.EMPLOYEE_ID = u.EMPLOYEE_ID
                JOIN `location` l ON e.LOCATION_ID = l.LOCATION_ID
                JOIN `job` j ON e.JOB_ID = j.JOB_ID
                JOIN `type` t ON t.TYPE_ID = u.TYPE_ID
                WHERE `USERNAME` = '" . $users . "' AND `PASSWORD` = '" . $h_upass . "'";
        
        $result = $db->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $found_user = mysqli_fetch_array($result);
                // Store session variables
                $_SESSION['MEMBER_ID'] = $found_user['ID']; 
                $_SESSION['FIRST_NAME'] = $found_user['FIRST_NAME']; 
                $_SESSION['LAST_NAME'] = $found_user['LAST_NAME'];  
                $_SESSION['GENDER'] = $found_user['GENDER'];
                $_SESSION['EMAIL'] = $found_user['EMAIL'];
                $_SESSION['PHONE_NUMBER'] = $found_user['PHONE_NUMBER'];
                $_SESSION['JOB_TITLE'] = $found_user['JOB_TITLE'];
                $_SESSION['PROVINCE'] = $found_user['PROVINCE']; 
                $_SESSION['CITY'] = $found_user['CITY']; 
                $_SESSION['TYPE'] = $found_user['TYPE'];
                $_SESSION['TYPE_ID'] = $found_user['TYPE_ID']; // Store TYPE_ID in session
                
                // Redirect based on TYPE_ID
                switch($found_user['TYPE_ID']) {
                    case 1: // Admin
                        $redirect = "index.php";
                        break;
                    case 2: // User
                        $redirect = "index.php";
                        break;
                    case 3: // Retraité
                        $redirect = "index.php";
                        break;
                    case 4: // Active
                        $redirect = "index.php";;
                        break;
                    case 5: // Global
                        $redirect = "index.php";
                        break;
                    default:
                        $redirect = "index.php";
                }
                ?>
                <script type="text/javascript">
                    alert("<?php echo $_SESSION['FIRST_NAME']; ?> Welcome!");
                    window.location = "<?php echo $redirect; ?>";
                </script>
                <?php        
            } else {
                ?>
                <script type="text/javascript">
                    alert("Username or Password Not Registered! Contact Your administrator.");
                    window.location = "index.php";
                </script>
                <?php
            }
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }
    }       
} 
$db->close();
?>