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

define('DB_host','localhost');
define('DB_user','root');
define('DB_name','bord');
session_start();

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



//データ出力処理
if(!empty($_POST['button1'])){
     if(empty($_POST['sente'])){
      $error_message[] ='未入力は投稿できません';
     }else{$clean['sente']= htmlspecialchars($_POST['sente'],ENT_QUOTES,'UTF-8');
        $clean['sente'] = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u','', $clean['sente']);
        $clean['region']= htmlspecialchars($_POST['region'],ENT_QUOTES,'UTF-8');
        $clean['region'] = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u','', $clean['region']);

     }
     if(empty($error_message)){

     
      
        //書き込み日時取得
       $c_date = date("Y-m-d H:i:s");
      //書き込み内容の取得
      $data = "'".$clean['sente']."'"."','".$c_date."'\n" ;
     
     //トランザクション開始
     $pdo->beginTransaction();  
     try{
      //SQL作成:代替文字
      $stmt = $pdo->prepare("INSERT INTO messe (region,message,date)VALUES(:region,:sente,:c_date)");

      //値をセット:代替文字に追加
     $stmt->bindParam(':region',$clean['region'],PDO::PARAM_STR); 
     $stmt->bindParam(':sente',$clean['sente'],PDO::PARAM_STR);
     $stmt->bindParam(':c_date',$c_date,PDO::PARAM_STR);
     //SQL実行
     $stmt->execute();

     //コミット
     $res = $pdo->commit();
     }catch(Exception $e)
     {
      //エラー時はロールバック
      $pdo->rollBack();
     }
     
     if($res){
        
        
     $_SESSION['success_message'] = 'メッセージを書き込みました。';
     } else{
            $error_message[] ='書き込みに失敗しました';
            echo <<<EOM
            <script type="text/javascript">
         alert("投稿に失敗しました")
         </script>
     EOM;
        }

        //プリペアド削除
        $stmt =null;


        header('Location: ./');
		exit;




      
    }
}

//検索sql実行
if(!empty($_POST['search'])){
    $sql1= "SELECT region,message,date FROM messe WHERE message or region LIKE '%" . $_POST["search"] . "%' ";
    //クエリ実行
    $message_array = $pdo->query($sql1);
}else{
    if(empty($error_message)){

    //メッセージを取得する
    $sql= "SELECT region,message,date FROM messe ORDER BY date DESC";
    //クエリ実行
    $message_array = $pdo->query($sql);
 }
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
    

<?php if( empty($_POST['button1']) && !empty($_SESSION['success_message']) ): ?>
    <p class="success_message">
         <script type="text/javascript">
         alert("投稿が完了しました")
         </script>
     </p>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>


   <?php //エラーメッセージ
   if(!empty($error_message)):?>
    
    <ul class = "error_message">
        <?php foreach($error_message as $value): ?>
            <li id="er"><?php echo $value; ?></li>
        <?php endforeach;?>
    </ul>
 <?php endif; ?>


 <form  method="post">
 
     <input type="text" name="search"placeholder="検索内容を入力">
       <input type="submit" name="buttonk" value="検索">

      
</form>

    <form  method="post" enctype="multipart/formdata" class="cform">
     
     
  
     <div id="rgn">
         <p id ="toukou">道路状況投稿サイト</p><br>
         <span id="rg">地域</span>
                
         
         <textarea id="text1" name="region"></textarea>
     </div>
  

     <div id="sente">
         <label> <br>
                <textarea id="text" name="sente" placeholder="投稿内容を入力してください"></textarea>
         </label>
     </div>
         
     

     <div id="button1">
        <input class ="btn" type="submit" name="button1"value="投稿"> 
     </div>
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


</body>
</html>