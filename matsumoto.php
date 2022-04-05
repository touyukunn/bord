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

define('DB_host',getenv('host'));
define('DB_user',getenv('user'));
define('DB_name',getenv('name'));
define('DB_pass',getenv('pass'));
session_start();



//DB接続
try{
     $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
     );
   $pdo = new PDO('mysql:charset=UTF8;dbname='.DB_name.';host='.DB_host,DB_user,DB_pass);

}catch(PDOException $e){

    $error_message[]= $e->getMessage();
}





//検索sql実行
    if(empty($error_message)){

    //メッセージを取得する
    $sql= "SELECT region,message,date FROM messe WHERE region LIKE '%" . "松本" . "%'  ORDER BY date DESC";
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
    <title>道路状況サイト</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<?php //エラーメッセージ
   if(!empty($error_message)):?>
    
    <ul class = "error_message">
        <?php foreach($error_message as $value): ?>
            <li id="er"><?php echo $value; ?></li>
        <?php endforeach;?>
    </ul>
    
<?php endif; ?>

<div id="view_time"></div>
<a href="index.php">home</a>  
<form  method="post" enctype="multipart/formdata" class="cform">
     
     
  
     <div id="rgn">
         <p id ="toukou">道路状況投稿サイト</p><br>
         <span id="rg">地域</span>
                
   

   
 </form>
 <hr color = "blue">
<section>
<?php if(!empty($message_array)):?>
<?php foreach($message_array as $value):?>
 <article>
    <div >
      <h2 class = "time"><time><?php echo date('Y年m月d日 H:i',strtotime($value['date']));?></h2>

     </time>
   </div>
 <!--地域-->
 <p class="rgn1"><?php echo nl2br(htmlspecialchars( $value['region'], ENT_QUOTES, 'UTF-8'));?></p>
<!--投稿内容-->
  <p class="mess"><?php echo nl2br(htmlspecialchars( $value['message'], ENT_QUOTES, 'UTF-8'));?></p>
  <hr>
 </article>
<?php endforeach;?>

<?php endif; ?>

</section>

<script type="text/javascript">
 function set2fig(num) {
  // 桁数が1桁だったら先頭に0を加えて2桁に調整する
  var ret;
  if( num < 10 ) { ret = "0" + num; }
  else { ret = num; }
  return ret;
 }

 function getNow() {
	var now = new Date();
	var year = now.getFullYear();
	var mon = now.getMonth()+1; //１を足すこと
	var day = now.getDate();
	var hour = set2fig( now.getHours() );
	var min = set2fig( now.getMinutes() );
	var sec =  set2fig( now.getSeconds() );

	//出力用
  var ss = "現在時刻"+"  "+hour + ":" + min + ":" + sec + "";
 document.getElementById("view_time").innerHTML = ss;};
 
 setInterval('getNow()',1000);
</script>

</body>
</html>
