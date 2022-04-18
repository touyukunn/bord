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

//ログイン情報
define('DB_host',getenv('host'));
define('DB_user',getenv('user'));
define('DB_name',getenv('name'));
define('DB_pass',getenv('pass'));




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



//データ出力処理
if(!empty($_POST['button1'])){
     if(empty($_POST['sente'])){
      $error_message[] ='未入力は投稿できません';
     }else{$clean['sente']= htmlspecialchars($_POST['sente'],ENT_QUOTES,'UTF-8');
        $clean['sente'] = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u','', $clean['sente']);
        $clean['region']= htmlspecialchars($_POST['region'],ENT_QUOTES,'UTF-8');
        $clean['region'] = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u','', $clean['region']);
        $clean['status']= htmlspecialchars($_POST['status'],ENT_QUOTES,'UTF-8');
        $clean['status'] = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u','', $clean['status']);
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
      $stmt = $pdo->prepare("INSERT INTO messe (region,message,date,status)VALUES(:region,:sente,:c_date,:c_sts)");

      //値をセット:代替文字に追加
     $stmt->bindParam(':region',$clean['region'],PDO::PARAM_STR); 
     $stmt->bindParam(':sente',$clean['sente'],PDO::PARAM_STR);
     $stmt->bindParam(':c_date',$c_date,PDO::PARAM_STR);
     $stmt->bindParam(':c_sts',$clean['status'],PDO::PARAM_STR);
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
        ob_start();
        header('Location:./');
		exit;




      
    }
}

//検索sql実行
if(!empty($_POST['search'])){
    $sql1= "SELECT region,message,date,status FROM messe WHERE message LIKE '%" . $_POST["search"] . "%' OR region LIKE '%" . $_POST["search"] . "%' ";
    //クエリ実行
    $message_array = $pdo->query($sql1);
}else{
    if(empty($error_message)){

    //メッセージを取得する
    $sql= "SELECT region,message,date,status FROM messe ORDER BY date DESC";
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

<div id="view_time"></div>
<br>

<form  method="post">
    <div class ="srch">
     <input type="text" name="search"placeholder="検索内容を入力">
     <input type="submit" name="buttonk" value="検索">
     </div>
        
</form>
<p id ="toukou">上田市</p>
<p id ="toukou">道路状況投稿サイト</p>
<br>

<span id="map">
<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d12845.06998343043!2d138.2293596346558!3d36.40272291786259!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sja!2sjp!4v1649470599292!5m2!1sja!2sjp" width="250" height="300"  style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</span>


<form  method="post" enctype="multipart/formdata" class="cform">
     
     <div id="rgn">
         
         <span id="rg">地域</span>
         <textarea id="text1" name="region" >上田市</textarea>
     </div>
     
     <div id="sts">
        <span id="stsst">状況</span>
      <select name="status">
          <option hidden>選択</option> 
          <option value="事故">事故</option> 
          <option value="渋滞">渋滞</option> 
          <option value="スタック">スタック</option>
          <option value="通行止め">通行止め</option> 
        </select> 
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
      <!--状況-->
     <p class="status_view"><?php echo nl2br(htmlspecialchars( $value['status'], ENT_QUOTES, 'UTF-8'));?></p>
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
