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

//データ出力処理
if(!empty($_POST['button1'])){
     if(empty($_POST['sente'])){
      $error_message[] ='未入力は投稿できません';
     }else{$clean['sente']= htmlspecialchars($_POST['sente'],ENT_QUOTES,'UTF-8');
        $clean['sente'] = preg_replace('/\\r\\n|\\n|\\r/','<br>',$clean['sente']);
     }
     if(empty($error_message)){

     
       if($file_handle = fopen(FILENAME,"a")){
       $c_date = date("Y-m-d H:i:s");
      //書き込み内容の取得
      $data = "'".$clean['sente']."'"."','".$c_date."'\n" ;
      

      //書き込み
       fwrite($file_handle,$data);
        fclose($file_handle);}
        echo <<<EOM
      <script type="text/javascript">
      alert("投稿が完了しました")
      </script>
EOM;
    }
}

//ファイルの読込
if ($file_handle = fopen(FILENAME,'r')){
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

  <p><?php echo nl2br($value['senten']);?></p>
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