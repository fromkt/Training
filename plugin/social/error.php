<?php
if (!defined('_GNUBOARD_')) exit;
?>
<!DOCTYPE html>
	<head>
		<meta name="robots" content="NOINDEX, NOFOLLOW">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=yes">
		<title><?php e__('Social login'); ?> - <?php echo $provider; ?></title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <style>
        .error-container{padding:1em}
        .bs-callout {
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #eee;
            border-left-width: 5px;
            border-radius: 3px;
        }
        .bs-callout-danger {
            border-left-color: #ce4844;
        }
        .bs-callout-danger h4 {
            color: #ce4844;
        }
        </style>
	</head>
	<body>
        <div class="error-container">
            <h4>Error : <?php echo $code; ?></h4>
            <div class="alert alert-danger" role="alert">
              <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
              <span class="sr-only">Error:</span>
              <?php echo $get_error; ?>
            </div>
            <div class="bs-callout bs-callout-danger" id="callout-images-ie-rounded-corners">
                <?php if(isset($code) && ($code <= 0 && $code > 10) ){ ?>
                <p><?php e__('Please try again later.'); ?></p>
                <?php } ?>
                <a href="<?php echo GML_URL; ?>" class="btn btn-primary go_home"><?php e__('Go to Home'); ?></a>
                <a href="<?php echo GML_URL; ?>" class="btn btn-default close" style="display:none"><?php e__('Close this page'); ?></a>
            </div>
        </div>
	</body>
    <script>
    jQuery(function($){
        $(".go_home.btn").click(function(e){
            if( window.opener ){
                e.preventDefault();
                window.opener.location.href = $(this).attr("href");
                window.close();
            }
        });
        
        if( window.opener ){
            $(".close.btn").show();
        }

        $(".close.btn").click(function(e){
            window.close();
        });
    });
    </script>
</html>
<?php
die();
?>