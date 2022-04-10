<!DOCTYPE html>
<?php



//タイムゾーン
date_default_timezone_set('Asia/Tokyo');

//変数初期化
$region = array();
$c_date = null;
$message = array();
$message_array = array();
$succ_message=null;
$error_message=array();
$pdo=null;
$stmt=null;
$res=null;
$option=null;
session_start();

$_SESSION['admin_login']=false;

// 管理ページのログインパスワード
define( 'PASSWORD', 'adminPassword');

define('DB_host','localhost');
define('DB_user','root');
define('DB_name','bord');

//DB接続
try {
     $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
     );
   $pdo = new PDO('mysql:charset=UTF8;dbname='.DB_name.';host='.DB_host,DB_user);

}catch(PDOException $e){

    $error_message[]= $e->getMessage();
}




if(!empty($_POST['admin_password'])){
 
    if( !empty($_POST['admin_password']) && $_POST['admin_password'] == PASSWORD ){
		$_SESSION['admin_login'] = true;
       
	} else {
		 echo <<<EOM
            <script type="text/javascript">
         alert("投稿に失敗しました")
         </script>;
         EOM;
	}
}   


if(empty($error_message)){
    

    //メッセージを取得する
    $sql= "SELECT region,message,date FROM messe ORDER BY date DESC";
    //クエリ実行
    $message_array = $pdo->query($sql);
}

//DB切断
$pdo = null;

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理ページ</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<?php if( !empty($success_message) ): ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>


    
   <?php //エラーメッセージ
   if(!empty($error_message)):?>
    
    <ul class = "error_message">
        <?php foreach($error_message as $value): ?>
            <li id="er"><?php echo $value; ?></li>
        <?php endforeach;?>
    </ul>
 <?php endif; ?>


    <form  method="post" enctype="multipart/formdata" class="cform">
        
     <div id="rgn">
         <p>道路状況投稿サイト管理ページ</p>
         
     </div>
  

     <div id="sente">
        
     </div>
         
     

     
        
     
    </form>
 <hr color = "blue">
 <section>

 <?php if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ): ?>

 <?php if(!empty($message_array)):?>
 <?php foreach($message_array as $value):?>
 <article>
    <div >
      <h2 class = "time"><time><?php echo date('Y年m月d日 H:i',strtotime($value['date']));?></h2>

     </time>
   </div>

 <p class="rgn1"><?php echo nl2br(htmlspecialchars( $value['region'], ENT_QUOTES, 'UTF-8'));?></p>

  <p class="mess"><?php echo nl2br(htmlspecialchars( $value['message'], ENT_QUOTES, 'UTF-8'));?></p>
 </article>
 
 

 <?php endforeach;?>
 <?php endif; ?>

 <?php else: ?>

    <form method="post">
    <div id="button1">
        <label for="admin_password">ログインパスワード</label>
        <input id="admin_password" type="password" name="admin_password" value="">
    </div>
    <input type="submit" name="btn_submit" value="ログイン">
</form>
<?php endif; ?>

</section>



</body>
</html>