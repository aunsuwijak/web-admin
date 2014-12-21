<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="lib/bootstrap.min.css">
    <script src="lib/jquery-2.1.1.min.js"></script>
    <script src="lib/bootstrap.min.js"></script>
    <script src="lib/angular.min.js"></script>
    <title>Shipment Service</title>
    <style type="text/css">
      ul {
        padding: 0px;
      }

      li {
        list-style-type: none;
        padding-left: 20px;
        padding-right: 20px;
      }

      body {
        margin: 0px;
      }

      .list { }

      .element {
        display: inline-block;
        border: 1px solid;
        border-radius: 8px;
        width: 116px;
        height: 42px;
        margin: 2px;
        padding-top: 10px;
      }

      .detail {
        display: block;
        border: 1px solid;
        border-radius: 8px;
        padding-left: 20px;
        padding-right: 20px;
        padding-top: 5px;
        padding-bottom: 5px;
        width: 358px;

      }
    </style>
  </head>
  <?php
   session_start(); //starts the session
   if($_SESSION['user']){ // checks if the user is logged in  
   }
   else{
      header("location: login.php"); // redirects if user is not logged in
   }
   $accessToken = $_SESSION['user']; //assigns user value
   ?>
  <body>
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Shipment</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a class="access" href="">Login</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<ul class="shipments">

</ul>
    <script type="text/javascript">

      $(function() {
        var accessToken = "<?php echo $accessToken; ?>";

        if (accessToken) {
          $(".access").text("Logout");
          $(".access").click(function() {
            accessToken = null;
          });
          $(".access").attr('href',"/web-admin/logout.php");
        }
        else {
          $(".access").text("Login");
          $(".access").attr('href','/web-admin/login.php');
        }

        var shipments;

        var setShipment = function() {
          var accessToken = "<?php echo $accessToken; ?>"; 

          var xmlhttp = new XMLHttpRequest(); 

          xmlhttp.open("GET","http://158.108.233.11:8080/shipments",true);
          xmlhttp.setRequestHeader("Authorization",accessToken);
          xmlhttp.send();

          xmlhttp.onreadystatechange = function() {
            if ( xmlhttp.readyState == 4 ) {
              shipments = jQuery.parseJSON(xmlhttp.responseText).shipments.shipment;

              console.log(shipments);
              var str = "";

              for ( var i = 0 ; i < shipments.length ; i++ ) {
                str = "<li class=\"list\"><div class=\"element\"><center>ID : " + shipments[i].id +
                "</center></div><div class=\"element\"><center><select id=" + shipments[i].id +
                 "><option>" + statuses[0] + 
                "</option><option>" + statuses[1] +
                "</option><option>" + statuses[2] +
                "</option><option>" + statuses[3] +
                "</option></select></center></div><div class=\"element\"><center>Total Cost : " + shipments[i].total_cost +
                "</center></div><div class=\"detail\">Sender : " + shipments[i].courier_name + 
                "<br>Receiver : " + shipments[i].receive_name + 
                "</div></li>";

                $(".shipments").append(str);

                $("#" + shipments[i].id).val(shipments[i].status);
              }

              $(".detail").hide();

              $(".element").click(function () {
                $(".detail").toggle();
              });

              $("select").change(function() {
                var option = $(this).val();
                var id = $(this).attr("id");
                var shipment;
                console.log(id);
                for ( var i = 0 ; i < shipments.length ; i++ ) {
                  if ( shipments[i].id = id ) {
                    shipment = shipments[i];
                    break;
                  }
                }
                saveStatus(shipment,option);
              });
            }
          }

        };

        var statuses = [ "Create", "Picked Up", "In Transit", "Received"];

        var saveStatus = function(shipment,option) {

          var xmlhttp = new XMLHttpRequest(); 

          xmlhttp.open("PUT","http://track-trace.tk:8080/shipments/"+shipment.id,true);
          xmlhttp.setRequestHeader("Authorization",accessToken);
          xmlhttp.setRequestHeader("Content-Type","application/xml");

          var param = ["<shipment>",
                        "<status>" + option + "</status>",
                       "</shipment>"].join("");

          console.log(param);

          xmlhttp.send(param);

          xmlhttp.onreadystatechange = function() {
            if ( xmlhttp.readyState == 4 ) 
              console.log(xmlhttp.status);
          }
        };

        var showDetail = function(shipment) {
          shipment.detail = ( shipment.detail || false ) ? false:true;
        };

        setShipment();

      });


    </script>
  </body>
</html>
