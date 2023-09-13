<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Simple bulletin board</title>
</head>
<body>
     <?php
        $filename = "m_3-5.txt";
        $messege = "";
        
        var_dump($_POST["edit_num"]);
        
        //コメントボタンを押した時の処理(新規コメント)
        if(isset($_POST["com_sub"]) && $_POST["edit_num"]==""){
            //空欄があるかの確認
            if($_POST["name"]!="" && $_POST["comment"]!="" && $_POST["passward"]!=""){
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $passward = $_POST["passward"]; 
                $date = date("Y/m/d H:i:s");
                
                //投稿番号の処理
                if (file_exists($filename)){
                    $num = count(file($filename)) + 1;
                }
                else{
                    $num = 1;
                }
                //テキストファイルへの書き込み
                $fp = fopen($filename,"a");
                $output = $num."<>".$name."<>".$comment."<>".$date."<>".$passward."<>";
                fwrite($fp,$output.PHP_EOL);
                fclose($fp);
            }
            //空欄がある時の処理
            else{
                $messege =  "空欄を全てを埋めてください";
            }
        }
            
            
        //削除ボタンを押した時の処理
        elseif(isset($_POST["del_sub"])){
            //ファイルが存在するかの確認
            if(file_exists($filename)){
                //空欄がないかの確認
                if($_POST["delete"]!="" && $_POST["delete_pass"]!=""){
                    $delete_num = $_POST["delete"];
                    $lines = file($filename);
                    //削除番号のパスワードの探索
                    foreach($lines as $line){
                        $bunkatu = explode("<>",$line);
                        if($bunkatu[0] == $delete_num){
                            $pass = $bunkatu[4];
                        }
                    }
                    //パスワードの照会
                    if($pass == $_POST["delete_pass"]){
                        $j=0;
                        //テキストファイル上の削除処理
                        $fp = fopen($filename,"w");
                        foreach($lines as $line){
                            $bunkatu = explode("<>",$line);
                            if($bunkatu[0] == $delete_num){
                                $j = 1;
                            }
                            else{
                                $output = $bunkatu[0]-$j."<>".$bunkatu[1]."<>".$bunkatu[2]."<>".$bunkatu[3]."<>".$bunkatu[4]."<>";
                                fwrite($fp,$output.PHP_EOL);
                            }
                        }
                        fclose($fp);
                    }
                    //パスワードが間違っている場合の処理
                    else{
                        $messege =  "パスワードが間違っています";
                    }
                }
                //空欄があった場合の処理
                else{
                    $messege =  "空欄を全て埋めてください";
                }
                
            }
            //ファイルが存在しない場合の処理
            else{
                $messege =  "ファイルが存在しません";
            }
        }    
            
        //編集ボタンを押したときの処理
        elseif(isset($_POST["edit_sub"])){
            //ファイルが存在するか確認
            if(file_exists($filename)){
                //空欄が存在するか確認
                if($_POST["editnum"]!="" && $_POST["edit_pass"]!=""){
                    $lines = file($filename);
                    $editnum = $_POST["editnum"];
                    //編集番号の投稿のパスワード探索
                    foreach($lines as $line){
                        $bunkatu = explode("<>",$line);
                        if($bunkatu[0] == $editnum){
                            $pass = $bunkatu[4];
                        }
                    }
                    //パスワード照会
                    if($_POST["edit_pass"] == $pass){
                        foreach($lines as $line){
                            $bunkatu = explode("<>",$line);
                            //editnumと同じ投稿番号の投稿を取得する
                            if($bunkatu[0] == $editnum){
                                $editname = $bunkatu[1];
                                $editcomment = $bunkatu[2];
                                $editpassward = $bunkatu[4];
                            }
                        }
                    }
                    //パスワードが間違っている場合の処理
                    else{
                        $messege =  "パスワードが間違っています";
                    }
                }
                //空欄がある時の処理
                else{
                    $messege =  "空欄を全て埋めてください";
                }
            }
            //ファイルが存在しない時
            else{
                $messege =  "ファイルが存在しません";
            }
        }
        
        //編集された入力が送られてきたときの処理
        elseif(isset($_POST["com_sub"],$_POST["edit_num"])){
            //空欄がないかの確認
            if($_POST["name"]!="" && $_POST["comment"]!="" && $_POST["passward"]!=""){
                $lines = file($filename);
                $editnum = $_POST["edit_num"];
                $editname = $_POST["name"];
                $editcomment = $_POST["comment"];
                $editpassward = $_POST["passward"];
                $date = date("Y/m/d H:i:s");
                $fp = fopen($filename,'w');
                foreach($lines as $line){
                    $bunkatu = explode("<>",$line);
                    if($bunkatu[0] == $editnum){
                        $output =  $bunkatu[0]."<>".$editname."<>".$editcomment."<>".$date."(編集済)"."<>".$editpassward."<>";
                        fwrite($fp,$output.PHP_EOL);
                    }
                    else{
                        $output = $bunkatu[0]."<>".$bunkatu[1]."<>".$bunkatu[2]."<>".$bunkatu[3]."<>".$bunkatu[4]."<>";
                        fwrite($fp,$output.PHP_EOL);
                    }
                }
                fclose($fp);    
                //編集が終わったら編集番号を初期化して空白に戻す
                $editnum = "";
            }
            //空欄があるときの処理
            else{
                $messege =  "全ての空欄を埋めてください";
            }
        }
    ?>
    
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前を入力" value= "<?php if(isset($editname)){echo $editname;} ?>">
        <input type="text" name="comment" placeholder="コメントを入力" value= "<?php if(isset($editcomment)){echo $editcomment;} ?>">
        <input type="text" name="passward" placeholder="パスワードを入力" value= "<?php if(isset($editpassward)){echo $editpassward;} ?>">        
        <input type="submit" name="com_sub" value="コメント"><br>
    
        <input type="number" name="delete" placeholder="削除対象番号を入力">
        <input type="text" name="delete_pass" placeholder="パスワードを入力" value= "">    
        <input type="submit" name="del_sub" value="削除"><br>
        
        <input type="number" name="editnum" placeholder="編集番号を入力">
        <input type="text" name="edit_pass" placeholder="パスワードを入力" value= "">    
        <input type="submit" name="edit_sub" value="編集"><br>
        
        <input type="hidden" name="edit_num" value= "<?php if(isset($editnum)){echo $editnum;} ?>">
    </form>
    
    <!--ファイルを読み込みブラウザに表示 --!>
    <?php
        if($messege!=""){
            echo $messege."<br>";
        }
        
        echo "【簡易掲示板】"."この掲示板のテーマ：今日の夕食"."<br>";
        if(file_exists($filename)){
            $lines = file($filename);
            foreach($lines as $line){
                $bunkatu = explode("<>",$line);
                echo "$bunkatu[0] $bunkatu[1] $bunkatu[2] $bunkatu[3]"."<br>";
            }
        }
    ?>
</body>
</html>