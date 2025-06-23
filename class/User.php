<?php
//require_once "classes/Database.php";

class User
{
    private $con;
    private $table = "users";
    public $userID;
    public $platform;
    public $loginEmail;
    public $loginID;
    public $firstName;
    public $lastName;
    public $password;
    public $loginPicture;
    public $userCode;
    public $tocken;
    public $lastLogin;
    public $userType;

    public function __construct()
    {
        $database = new Database();
        $this->con = $database->con;
    }

    ### Register
    public function register($data)
    {
        // Initialize
        $this->platform = "WEB";
        $this->loginEmail = htmlspecialchars(strip_tags($data['useremail']));
        $this->loginID = "";
        $this->firstName = htmlspecialchars(strip_tags($data['firstname']));
        $this->lastName = htmlspecialchars(strip_tags($data['lastname']));
        $this->password = md5(htmlspecialchars(strip_tags($data['password1'])));
        $this->loginPicture = "";
        $this->userCode = time();
        $this->tocken = md5($this->loginEmail . $this->userCode);
        $this->lastLogin = date("Y-m-d H:i:s");
        $this->userType = 1;

        // Check Empty
        if (empty($this->firstName) || empty($this->lastName) || empty($this->loginEmail) || empty($this->password)) {
            ?>
            <div class="alert alert-danger" role="alert">
                <h5 style="text-align: center;">Fields can not be empty!</h5>
            </div>
            <?php
        } else {
            // Check Email Existance
            $queryCheck = "SELECT * from $this->table where loginEmail = '$this->loginEmail'";
            $exe = $this->con->query($queryCheck);
            if (mysqli_num_rows($exe) >= 1) {
                ?>
                <div class="alert alert-warning" role="alert">
                    <h5 style="text-align: center;">The Email already Exists! Please <a href="account.php">Login</a></h5>
                </div>
                <?php
            } else {
                //Enter New Record
                $query = "INSERT INTO $this->table (`platform`, `loginEmail`, `loginID`, `firstName`, `lastName`, `password`, `loginPicture`, `userCode`, `tocken`, `lastLogin`, `userType`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if ($stmt = $this->con->prepare($query)) {
                    $stmt->bind_param("sssssssssss", $this->platform, $this->loginEmail, $this->loginID, $this->firstName, $this->lastName, $this->password, $this->loginPicture, $this->userCode, $this->tocken, $this->lastLogin, $this->userType);
                    if ($stmt->execute()) {
                        ?>
                        <div class="alert alert-success" role="alert">
                            <h5 style="text-align: center;"><b>Successfully Created.</b> <a href="account.php">Login</a></h5>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="alert alert-danger" role="alert">
                            <h5 style="text-align: center;"><?php echo "Execute failed: " . $stmt->error; ?></h5>
                        </div>
                        <?php
                    }
                    $stmt->close();
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <h5 style="text-align: center;"><?php echo "Prepare failed: " . $this->con->error; ?></h5>
                    </div>
                    <?php
                }
            }
        }
    }




    ### Login
    public function login($data)
    {
        // Initialize
        $this->loginEmail = htmlspecialchars(strip_tags($data['useremail']));
        $this->password = md5(htmlspecialchars(strip_tags($data['password'])));

        // Check Empty
        if (empty($this->loginEmail) || empty($this->password)) {
            ?>
            <div class="alert alert-danger" role="alert">
                <h5 style="text-align: center;">Fields can not be empty!</h5>
            </div>
            <?php
        } else {
            // Check Email Existance
            $queryCheck = "SELECT * from $this->table where loginEmail = '$this->loginEmail'";
            $exe = $this->con->query($queryCheck);
            if (mysqli_num_rows($exe) >= 1) {
                $row = $exe->fetch_array(MYSQLI_ASSOC);
                if ($row['password'] == $this->password) {
                    // Password Match
                    $_SESSION['userID'] = $row['userID'];
                    $_SESSION['loginEmail'] = $row['loginEmail'];
                    $_SESSION['firstName'] = $row['firstName'];
                    $_SESSION['lastName'] = $row['lastName'];
                    $_SESSION['loginPicture'] = $row['loginPicture'];
                    $_SESSION['userType'] = $row['userType'];
                    $_SESSION['platform'] = "WEB";


                    // $profile = new Profile();
                    // if ($profile->checkProfile($_SESSION['userID'])) {
                    //     $profile->getProfileByUser($_SESSION['userID']);
                    //     $_SESSION['profileID'] = $profile->profileID;
                    // }

                    if (isset($_SESSION['targetURL']) && $_SESSION['targetURL'] != "") {
                        header("Location: " . $_SESSION['targetURL']);
                    } else {
                        header("Location: index.php");
                    }
                    ?>
                    <div class="alert alert-success" role="alert">
                        <h5 style="text-align: center;">Success Login. Hi <?php echo $row['lastName']; ?></h5>
                    </div>
                    <?php
                } else {
                    // Password Not Match
                    ?>
                    <div class="alert alert-warning" role="alert">
                        <h5 style="text-align: center;"><b>Wrong Password.</b> Try Again</h5>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="alert alert-warning" role="alert">
                    <h5 style="text-align: center;">Sorry, you don't have an account. Please <a href="account.php?signup">Signup</a>
                    </h5>
                </div>
                <?php
            }
        }
    }








    ### Google Login
    public function googleLogin($udata, $code)
    {
        $loginEmail = $udata['email'];
        $loginID = $udata['id'];
        $firstName = $udata['familyName'];
        $lastName = $udata['givenName'];
        $loginPicture = $udata['picture'];
        $userCode = $this->con->real_escape_string($code);
        $lastLogin = date("Y-m-d H:i:s");

        // checking table
        $queryCheck = "SELECT * from $this->table where loginEmail = '$loginEmail' and loginID ='$loginID'";
        $exe = $this->con->query($queryCheck);
        if (mysqli_num_rows($exe) >= 1) {
            // Found
            $rowcheck = $exe->fetch_assoc();

            $sqlupdate = "UPDATE users SET firstName='$firstName', lastName='$lastName', loginPicture='$loginPicture', userCode='$userCode', lastLogin='$lastLogin' where loginID='$loginID'";
            $exeupdate = $this->con->query($sqlupdate);

            /*foreach($udata as $k => $v){
                $_SESSION['login_'.$k] = $v;
            }*/

            $_SESSION['userID'] = $rowcheck['userID'];
            $_SESSION['loginEmail'] = $rowcheck['loginEmail'];
            $_SESSION['firstName'] = $rowcheck['firstName'];
            $_SESSION['lastName'] = $rowcheck['lastName'];
            $_SESSION['loginPicture'] = $rowcheck['loginPicture'];
            $_SESSION['userType'] = $rowcheck['userType'];
            $_SESSION['platform'] = "Google";
            $_SESSION['ucode'] = $code;
            $_SESSION['userID'] = $rowcheck['userID'];

            if (isset($_SESSION['targetURL']) && $_SESSION['targetURL'] != "") {
                header("Location: " . $_SESSION['targetURL']);
            } else {
                header("Location: index.php");
            }
        } else {
            // Not Found
            $sqlinsert = "INSERT INTO users(userID, platform, loginEmail, loginID, firstName, lastName, password, loginPicture, userCode, lastLogin, userType) VALUES (null, 'Google', '$loginEmail','$loginID','$firstName','$lastName', '', '$loginPicture','$userCode','$lastLogin','1')";
            $exeinsert = $this->con->query($sqlinsert);

            if ($exeinsert) {
                $sqlget = "SELECT * from $this->table where loginEmail = '$loginEmail' and loginID ='$loginID'";
                $exeget = $this->con->query($sqlget);
                if (mysqli_num_rows($exeget) >= 1) {
                    // Found
                    $rowget = $exeget->fetch_assoc();

                    $_SESSION['userID'] = $rowget['userID'];
                    $_SESSION['loginEmail'] = $rowget['loginEmail'];
                    $_SESSION['firstName'] = $rowget['firstName'];
                    $_SESSION['lastName'] = $rowget['lastName'];
                    $_SESSION['loginPicture'] = $rowget['loginPicture'];
                    $_SESSION['userType'] = $rowget['userType'];
                    $_SESSION['platform'] = "Google";
                    $_SESSION['ucode'] = $code;
                    $_SESSION['userID'] = $rowget['userID'];
                    if (isset($_SESSION['targetURL']) && $_SESSION['targetURL'] != "") {
                        header("Location: " . $_SESSION['targetURL']);
                    } else {
                        header("Location: index.php");
                    }
                }
            }

        }

    }












    ### Facebook Login
    public function facebookLogin($profile, $code)
    {
        $loginEmail = $profile['email'];
        $loginID = $profile['id'];
        $firstName = "";
        $lastName = $profile['name'];
        $loginPicture = $profile['picture']['data']['url'];
        $userCode = $this->con->real_escape_string($code);
        $lastLogin = date("Y-m-d H:i:s");

        // checking table
        $queryCheck = "SELECT * from $this->table where loginEmail = '$loginEmail' and loginID ='$loginID'";
        $exe = $this->con->query($queryCheck);
        if (mysqli_num_rows($exe) >= 1) {
            // Found
            $rowcheck = $exe->fetch_assoc();

            $sqlupdate = "UPDATE users SET firstName='$firstName', lastName='$lastName', loginPicture='$loginPicture', userCode='$userCode', lastLogin='$lastLogin' where loginID='$loginID'";
            $exeupdate = $this->con->query($sqlupdate);

            $_SESSION['userID'] = $rowcheck['userID'];
            $_SESSION['loginEmail'] = $rowcheck['loginEmail'];
            $_SESSION['firstName'] = $rowcheck['firstName'];
            $_SESSION['lastName'] = $rowcheck['lastName'];
            $_SESSION['loginPicture'] = $rowcheck['loginPicture'];
            $_SESSION['userType'] = $rowcheck['userType'];
            $_SESSION['platform'] = "Facebook";
            $_SESSION['ucode'] = $code;
            $_SESSION['userID'] = $rowcheck['userID'];
            header('location: ./');
            exit;
        } else {
            // Not Found
            $sqlinsert = "INSERT INTO users(userID, platform, loginEmail, loginID, firstName, lastName, password, loginPicture, userCode, lastLogin, userType) VALUES (null, 'Facebook', '$loginEmail','$loginID','$firstName','$lastName', '', '$loginPicture','$userCode','$lastLogin','1')";
            $exeinsert = $this->con->query($sqlinsert);

            if ($exeinsert) {
                $sqlget = "SELECT * from $this->table where loginEmail = '$loginEmail' and loginID ='$loginID'";
                $exeget = $this->con->query($sqlget);
                if (mysqli_num_rows($exeget) >= 1) {
                    // Found
                    $rowget = $exeget->fetch_assoc();

                    $_SESSION['userID'] = $rowget['userID'];
                    $_SESSION['loginEmail'] = $rowget['loginEmail'];
                    $_SESSION['firstName'] = $rowget['firstName'];
                    $_SESSION['lastName'] = $rowget['lastName'];
                    $_SESSION['loginPicture'] = $rowget['loginPicture'];
                    $_SESSION['userType'] = $rowget['userType'];
                    $_SESSION['platform'] = "Facebook";
                    $_SESSION['ucode'] = $code;
                    $_SESSION['userID'] = $rowget['userID'];
                    header('location: ./');
                    exit;
                }
            }

        }

    }


    public function getUserByID($userID)
    {
        $sql = "SELECT * FROM $this->table where userID = '$userID'";
        $exe = $this->con->query($sql);
        if ($exe) {
            $row = $exe->fetch_array();
            $this->userID = $row['userID'];
            $this->platform = $row['platform'];
            $this->loginEmail = $row['loginEmail'];
            $this->loginID = $row['loginID'];
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->password = $row['password'];
            $this->loginPicture = $row['loginPicture'];
            $this->userCode = $row['userCode'];
            $this->tocken = $row['tocken'];
            $this->lastLogin = $row['lastLogin'];
            $this->userType = $row['userType'];
        }
    }


    // Close DB
    public function close()
    {
        //echo "hi";
        $this->con->close();
    }
}
?>