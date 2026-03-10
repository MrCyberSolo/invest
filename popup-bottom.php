<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<title>Smooth Modal</title>
<style>

  
    /* Styling for modal container */
    .invest-modal-container {
        display: none;
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        transition: opacity 0.3s ease;
        z-index: 9999;
    }

    /* Styling for modal content */
    .invest-modal-content {
        position: relative;
        background-color: #fff;
        margin: auto;
        padding: 10px;
        width: 100%;
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

     /* Styling for close button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }

    .invest-modal-container p{
        margin: 0;
        line-height: 17px;
        font-size: 13px;
        
    }

    .invest-modal-container .inner {
        width: 100%;
    }

    .invest-modal-container .inner .top{
        background-color: #5564E6;
        padding: 8px;
        color: white;


    }

    .invest-modal-container img{
        width: 17px;
    }

    .invest-modal-container .botom{
        font-size: 17px;
        background-color: #FCFAED;
    }

    .invest-modal-container .botom small, .right{
        color: #EBA73C;
    }

    .modal-body .inner{
        display: flex; 
        justify-content: space-between;
        padding: 10px;
        
    }

    .modal-body .inner p{
        font-size: 14px;
    }

    .modal-body .inner p:first-child{
        color: rgb(149, 149, 149);
    }

    .invest-modal-container .modal-header{
        padding-top: 40px;
    }


   
</style>

    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
<body>

<button onclick="openModal()">Open Modal</button>

<div id="InvestModalContainer" class="invest-modal-container ">
    <div id="InvestModalContent" class="invest-modal-content  ">

        <span class="close-btn text-end fs-5 text-secondary" onclick="closeModal()"><i class="bi bi-x-circle"></i></span>

        <div class="modal-header d-flex justify-content-between gap-2">
            <div class="inner">
                <div class="top">
                    <p>0.00RS</p>
                    <p>Balance wallet</p>
                </div>
                <div class="botom   d-flex justify-content-between align-items-center px-2">
                    <div class="left">
                    <img src="img/icons/invitation.png" alt="" >
                    <small>invitation</small>
                    </div>
                    <div class="right">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </div>
               
            </div>

            <div class="inner">
                <div class="top">
                    <p>0.00RS</p>
                    <p>Balance wallet</p>
                </div>
                <div class="botom   d-flex justify-content-between align-items-center px-2">
                    <div class="left">
                    <img src="img/icons/recharge.png" alt="" >
                    <small>Recharge</small>
                    </div>
                    <div class="right ">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </div>
               
            </div>
        </div>


        <div class="modal-body">
           <div class="inner">
            <p>Price</p>
            <p>6300.00RS</p>
           </div>

           <a href="" class="inner text-decoration-none text-dark">
            <p>Discount coupon</p>
            <p>please choose ></p>
           </a>

           <div class="inner">
            <p>The amount actually paid</p>
            <p>6300.00RS</p>
           </div>
        </div>

        <div class="modalFooter d-grid pb-2">
            <button class="btn btn-dark rounded-pill py-2">Confirm</button>
        </div>
        
    </div>
</div>

<script>
    var InvestModalContainer = document.getElementById("InvestModalContainer");
    var InvestModalContent = document.getElementById("InvestModalContent");

    function openModal() {
        InvestModalContainer.style.display = "block";
        setTimeout(function () {
            InvestModalContent.style.transform = "translateY(0%)";
        }, 10);
    }

    function closeModal() {
        InvestModalContent.style.transform = "translateY(100%)";
        setTimeout(function () {
            InvestModalContainer.style.display = "none";
        }, 300);
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
