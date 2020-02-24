<!-- Start of Header -->
<head>
    <title><?php echo $title ?></title>
    <?php echo '<link rel="stylesheet" type="text/css" href="'. $path . 'style/argon.min.css" />'; ?>
    <link href="https://fonts.googleapis.com/css?family=Baloo+Bhai&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        html {
          position: relative;
          min-height: 100%;
        }
        body {
            margin-bottom: 100px;
            background-color: #d3d3d3;
            background-image: linear-gradient(315deg, #d3d3d3 0%, #7f8c8d 74%);
        }
        /**
            Footer 

        **/
        footer {
              position: absolute;
              bottom: 0;
              width: 100%;
              margin-top: 30px;
              color: white;
              height: 100px; //same height as the margin to the bottom of the body
        } 
        /**
            Navigation
        **/
        .title {
            font-size: 25px;
            font-family: 'Baloo Bhai', cursive;
        }
        
        .nav-link {
            font-size: 20px !important;
        }
        .navbar {
            background: linear-gradient(90deg, #FC466B 0%, #3F5EFB 100%);
            background-color: #990000;
background-image: linear-gradient(147deg, #990000 0%, #ff0000 74%);
        }
        /*
            Make the tiles (all screens) change the padding for mobile and desktop
        */
        .tiles {
            padding-top: 20px;
        }
        @media only screen and (min-width: 600px) {
            .tiles {
                padding-top: 60px;
            }
        }

        /**
        Changes the viewing attributes of the tables
        - How the are positioned
        - The rows are colored (odd/even colors)
        **/
        th,
        td {
            text-align: left;
            padding: 8px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .table tr:nth-child(odd) {
            background-color: lightgray;
        }

        /**
            Help Block Form
        **/
        .help-block {
            padding: 5px;
            font-weight: bold;
        }
        .btn-circle.btn-lg {
            width: 50px;
            height: 40px;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.33;
            border-radius: 25px;
        }
 
        .event-icon-placholder {
            height: 100px;
            width: 100px;
            background-image: linear-gradient(70deg, #FC466B 0%, #3F5EFB 100%);
        }
    </style>
</head>
<!-- / End of Header -->