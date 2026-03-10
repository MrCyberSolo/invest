<?PHP 
include('user_menu/database_connect.php');
//$userid_access = $_SESSION['username'];
if(isset($_GET['pac']))
{

  $blog_id = $_GET['pac'];
  $blog_details = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `blog` WHERE `b_id`='$blog_id'"));
    $blog_id = $blog_details['b_id'];
    $blog_title = $blog_details['b_title'];
    $blog_image = $blog_details['b_image'];
    $blog_des = $blog_details['b_details'];
    $blog_create_date = $blog_details['b_create_date'];
    $b_image2 = $blog_details['b_image2'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>news details</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#213955;
        --light-blue:#009a5f;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule{
        max-width: 641px;
        margin: auto;
        

       }

       header{
        background-color:#005a36;

       }

       
       .appBody{
        padding: 3.5rem 0;
       }
       

       .n-details p{
        font-size:large;
        border-bottom: #0000004d 1px solid;
        padding: 10px 0;
        
       }



    
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body class="bg-light">


<div class="appCapsule container ">
  <header class="row fixed-top ">
        <div class="text-white py-3">
            <div class="px-3 d-flex align-items-center justify-content-between">
                <a href="news.php"  class=" text-white text-decoration-none left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6 >News Details</h6>

                <div class="px-3"></div>

        </div>
        </div>

  </header>
  <div class="row appBody">
    <div class="col-12 n-details px-4 mt-3">
        <h4><?php echo $blog_title; ?></h4>
        <p>News <span><?php echo $blog_create_date; ?></span></p>
       <!--  <h3>🔊🔊STANLEY latest notification</h3> -->
        <br>
		<img src="https://png.pngtree.com/png-vector/20231127/ourmid/pngtree-demo-red-flat-icon-isolated-demo-icon-png-image_10722763.png" class="img-fluid" alt="">
		
        <p><?php echo $blog_des; ?></p>
        <img src="https://png.pngtree.com/png-vector/20231127/ourmid/pngtree-demo-red-flat-icon-isolated-demo-icon-png-image_10722763.png" class="img-fluid" alt="">
        <br>
    </div>
  </div>

</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  
  </body>
</html>