<!DOCTYPE html>
<?php
//保存先宣言
define('FILENAME','./message.txt');
//タイムゾーン
date_default_timezone_set('Asia/Tokyo');

//変数初期化
$c_date = null;
$data =null;
$file_handle = null;
$sp_data = null;
$message = array();
$message_array = array();
$succ_message=null;
$error_message=array();
$clean =array();
$pdo=null;
$stmt=null;
$res=null;
$option=null;

//DB接続
try{
     $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
     );
   $pdo = new PDO('mysql:charset=UTF8;dbname=bord;host=localhost','root',);

}catch(PDOException $e){

    $error_message[]= $e->getMessage();
}


//データ出力処理
if(!empty($_POST['button1'])){
     if(empty($_POST['sente'])){
      $error_message[] ='未入力は投稿できません';
     }else{$clean['sente']= htmlspecialchars($_POST['sente'],ENT_QUOTES,'UTF-8');
        $clean['sente'] = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u','', $clean['sente']);
     }
     if(empty($error_message)){

     
       if($file_handle = fopen(FILENAME,"a")){
        //書き込み日時取得
       $c_date = date("Y-m-d H:i:s");
      //書き込み内容の取得
      $data = "'".$clean['sente']."'"."','".$c_date."'\n" ;
     
     //トランザクション開始
     $pdo->beginTransaction();  
     try{
      //SQL作成:代替文字
      $stmt = $pdo->prepare("INSERT INTO message (message,post_date)VALUES(:sente,:c_date)");

      //値をセット:代替文字に追加
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
         $success_message ="書き込み完了しました";
         echo <<<EOM
         <script type="text/javascript">
         alert("投稿が完了しました")
         </script>
     EOM;
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
    




      
    }}
}

if(empty($error_message)){

    //メッセージを取得する
    $sql= "SELECT message,post_date FROM message ORDER BY post_date DESC";
    //クエリ実行
    $message_array = $pdo->query($sql);
}

//DB切断
$pdo = null;


//ファイルの読込
/*if ($file_handle = fopen(FILENAME,'r')){
   while($data = fgets($file_handle)){
       $splite_data =preg_split('/\'/',$data);
       $message = array(
         'senten'=> $splite_data[1],
         'post_date'=> $splite_data[4],);
     
     array_unshift($message_array,$message);}
         
     print $splite_data[5];
     $succ_message='完了';
     
     
    //ファイルを閉じる
    fclose($file_handle);

}
*/
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿</title>
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

    <form  method="post" enctype="multipart/formdata" class="cform">

     <div id="sente">
         <label> 道路状況 <br>
                <textarea id="text" name="sente"></textarea>
         </label>
     </div>
         
     <div id="button1">
        <input type="submit" name="button1"value="投稿"> 
     </div>
    </form>
<hr>
<section>
<?php if(!empty($message_array)):?>
<?php foreach($message_array as $value):?>
 <article>
    <div class="info">
      <h2><time><?php echo date('Y年m月d日 H:i',strtotime($value['post_date']));?></h2>

     </time>
   </div>

  <p><?php echo nl2br($value['message']);?></p>
 </article>

<?php endforeach;?>
<?php endif; ?>

</section>
<script>/*
const button = document.getElementById('button1');
//const error= document.getElementById('er');
button.addEventListener('click',function(){
   alert("完了");
   
})*/
</script>
</body>
</html>