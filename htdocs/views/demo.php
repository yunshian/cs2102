<html>
    <head>
        <title>Demo</title>
    </head>
    <body>
      <?php
        
        require_once 'autoload.php';
        use Valitron\Validator as V;
        
        $fields = ['date1'=>'2016-01-01', 'date2'=>'2016-1-1', 'date3'=>'2015-02-29'];
        $v = new V($fields);
        $v->rule('dateFormat', ['date1','date2','date3'], 'Y-m-d');
        if ($v->validate()) {
            echo "YAY";
        } else {
            print_r($v->errors());
        }
        ?>
    </body>
</html>