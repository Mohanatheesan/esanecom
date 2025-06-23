<?php

class Category
{
    protected $con;
    private $table = "category";

    public $catID;
    public $catTitle;
    public $catURL;
    public $code;
    public $active;

    // DB Connection
    public function __construct()
    {
        $database = new Database();
        $this->con = $database->con;
    }

    // SEO Friendly URL
    public function makeSeoFriendlyUrl($url)
    {
        // Remove Special Characters
        $url = preg_replace('/[^a-zA-Z0-9\s]/', '', $url);
        // Convert to lowercase
        $url = strtolower($url);
        // Replace spaces with hyphens
        $url = preg_replace('/\s+/', '-', $url);
        // Remove multiple hyphens
        $url = preg_replace('/-+/', '-', $url);
        return $url;
    }

    // Create New Record
    public function createCategory($data)
    {
        // Initialize
        $this->catTitle = $this->con->real_escape_string($data['catTitle']);
        $this->catURL = $this->makeSeoFriendlyUrl($this->catTitle);
        $this->code = time();
        $this->active = '1';

        // Check table
        $sqlChk = "SELECT catURL from $this->table where catURL='$this->catURL' and active='1'";
        $exeChk = $this->con->query($sqlChk);
        if (mysqli_num_rows($exeChk) >= 1) {
            ?>
            <div class="alert pt-5 pb-0 alert-warning" role="alert">
                <h5 style="text-align: center;">The Category already Exists!</h5>
            </div>
            <?php
        } else {
            // Create New Record
            $sqlInsert = "INSERT INTO $this->table VALUES (null, '$this->catTitle', '$this->catURL', '$this->code', '$this->active')";
            $exeInsert = $this->con->query($sqlInsert);
            if ($exeInsert) {
                header("Location: category.php?state=001");
            } else {
                ?>
                <div class="alert pt-5 pb-0 alert-danger" role="alert">
                    <h5 style="text-align: center;"><?php echo "Execute failed: " . $this->con->error; ?></h5>
                </div>
                <?php
            }
        }
    }


    // Update Record
    public function updateCategory($data)
    {
        // Initialize
        $this->catTitle = $this->con->real_escape_string($data['catTitle']);
        $this->catURL = $this->makeSeoFriendlyUrl($this->catTitle);

        //$this->code = $this->con->real_escape_string($data['code']);

        $sqlUpdate = "UPDATE $this->table SET catTitle='$this->catTitle', catURL='$this->catURL' where catID='$this->catID'";
        $exeUpdate = $this->con->query($sqlUpdate);
        if ($exeUpdate) {
            header("Location: category.php?edit=$this->catID");
            ?>
            <div class="alert pt-5 pb-0 alert-success" role="alert">
                <h5 style="text-align: center;"><b>Successfully Updated.</b></h5>
            </div>
            <?php
        } else {
            ?>
            <div class="alert pt-5 pb-0 alert-danger" role="alert">
                <h5 style="text-align: center;"><?php echo "Execute failed: " . $this->con->error; ?></h5>
            </div>
            <?php
        }
    }


    // Delete Record
    public function deleteCategory($data)
    {
        $this->code = $this->con->real_escape_string($data['code']);

        $sqlDelete = "UPDATE $this->table SET active = '0' where code = '$this->code'";
        $exeDelete = $this->con->query($sqlDelete);
        if ($exeDelete) {
            header("Location: category.php?state=100");
        }
    }


    // Fetch All Records
    public function selectAll($order = "ASC")
    {
        $sqlGet = "SELECT * from $this->table where active='1' order by catTitle $order";
        $exeGet = $this->con->query($sqlGet);
        if ($exeGet) {
            $rows = [];
            while ($row = $exeGet->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }


    // Fetch One Record
    public function selectOne($catID)
    {
        $sqlGet = "SELECT * from $this->table where active='1' and catID='$catID'";
        $exeGet = $this->con->query($sqlGet);
        if (mysqli_num_rows($exeGet)) {
            $rowGet = $exeGet->fetch_assoc();

            $this->catID = $rowGet['catID'];
            $this->catTitle = $rowGet['catTitle'];
            $this->catURL = $rowGet['catURL'];

        }
        //return $rowGet;
    }

    public function selectOneByURL($URL)
    {
        $sqlGet = "SELECT * from $this->table where active='1' and catURL='$URL'";
        $exeGet = $this->con->query($sqlGet);
        if (mysqli_num_rows($exeGet)) {
            $rowGet = $exeGet->fetch_assoc();

            $this->catID = $rowGet['catID'];
            $this->catTitle = $rowGet['catTitle'];
            $this->catURL = $rowGet['catURL'];

        }
        //return $rowGet;
    }


    // Close DB
    public function close()
    {
        //echo "hi";
        $this->con->close();
    }
}
?>