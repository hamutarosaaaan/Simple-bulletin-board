<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Simple bulletin board DBver</title>
</head>
<body>
     <?php
        $messege = "";
        //コメントボタンを押した時の処理(新規コメント)
        if(isset($_POST["com_sub"]) && $_POST["edit_num"]==""){
            //空欄があるかの確認
            if($_POST["name"]!="" && $_POST["comment"]!="" && $_POST["password"]!=""){
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $pass = $_POST["password"]; 
                $date = date("Y/m/d H:i:s");
                // DB接続
                $dsn = 'mysql:dbname=データベース名;host=localhost';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                //テーブルの作成
                $sql = "CREATE TABLE IF NOT EXISTS Passtest"
                ." ("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "name char(32),"
                . "comment varchar(400),"
                . "date varchar(50),"
                . "password varchar(50)"
                .");";
                $stmt = $pdo->query($sql);
                //新規コメントのINSERT
                $sql = "INSERT INTO Passtest (name, comment, date, password) VALUES (:name, :comment, :date, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
                $stmt->execute();
                //データベースの切断
                $pdo =  null;
            }
            else{
                $messege = "空欄を全て埋めてください";
            }
        }
        
        //削除ボタンを押した時の処理
        elseif(isset($_POST["del_sub"])){
            //空欄がないかの確認
            if($_POST["delete"]!="" && $_POST["delete_pass"]!=""){
                //DB接続
                $dsn = 'mysql:dbname=データベース名;host=localhost';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                //パスワードの照会
                $delete_num = $_POST["delete"];
                $delete_pass = $_POST["delete_pass"]; 
                $sql = 'SELECT * FROM Passtest WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll(); 
                foreach ($results as $row){
                    if($delete_pass == $row['password']){
                        //DB上の削除処理    
                        $sql = 'delete from Passtest where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT);
                        $stmt->execute();
                        //データベースの切断
                        $pdo =  null;
                    }
                    else{
                        $messege =  "パスワードが間違っています";
                    }
                }
            }
            //空欄があった場合の処理
            else{
                $messege = "削除番号を入力してください";
            }
            //ファイルが存在しない場合の処理？
        }
            
        //編集ボタンを押したときの処理
        elseif(isset($_POST["edit_sub"])){
            //空欄が存在するか確認
            if($_POST["editnum"]!="" && $_POST["edit_pass"]!=""){
                //DB接続
                $dsn = 'mysql:dbname=データベース名;host=localhost';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                //edienumのidをselectし、そのレコードを取り出す
                $editnum = $_POST["editnum"];
                $editpass = $_POST["edit_pass"];
                $sql = 'SELECT * FROM Passtest WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $editnum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll(); 
                foreach ($results as $row){
                    if($editpass == $row['password']){
                        $editname = $row['name'];
                        $editcomment = $row['comment'];
                    }
                    else{
                        $messege =  "パスワードが間違っています";
                    }
                }
                //DB切断
                $pdo =  null;
            }
            //空欄がある時の処理
            else{
                $messege =  "空欄を全て埋めてください";
            }
            //ファイルが存在しない時？
        }
            
        //編集された入力が送られてきたときの処理
        elseif(isset($_POST["com_sub"],$_POST["edit_num"])){
            "編集コメントの投稿処理";
            //空欄がないかの確認
            if($_POST["name"]!="" && $_POST["comment"]!="" && $_POST["password"]!=""){
                 //DB接続
                $dsn = 'mysql:dbname=データベース名;host=localhost';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                //DB上の編集処理
                $editnum = $_POST["edit_num"];
                $editname = $_POST["name"];
                $editcomment = $_POST["comment"];
                $editpass = $_POST["password"];
                $date = date("Y/m/d H:i:s")."(編集済)";
                $sql = 'UPDATE Passtest SET name=:name,comment=:comment,date=:date, password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $editname, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $editcomment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $editpass, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':id', $editnum, PDO::PARAM_INT);
                $stmt->execute();
                //編集が終わったら各変数を初期化
                $editname = "";
                $editcomment = "";
                $editpass = "";
                $editnum = "";
                //DB切断
                $pdo =  null;
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
        <input type="password" name="password" placeholder="パスワードを入力" value= "<?php if(isset($editpass)){echo $editpass;} ?>">  
        <input type="submit" name="com_sub" value="コメント"><br>
        
        <input type="number" name="delete" placeholder="削除対象番号を入力">
        <input type="password" name="delete_pass" placeholder="パスワードを入力" value= "">    
        <input type="submit" name="del_sub" value="削除"><br>
        
        <input type="number" name="editnum" placeholder="編集番号を入力">
         <input type="password" name="edit_pass" placeholder="パスワードを入力" value= "">    
        <input type="submit" name="edit_sub" value="編集"><br>
        
        <input type="hidden" name="edit_num" value= "<?php if(isset($editnum)){echo $editnum;} ?>">
    </form>
    
    <!--ファイルを読み込みブラウザに表示 --!>
    <?php
        if($messege!=""){
            echo $messege."<br>";
        }
        echo "<hr>";
        echo "【mission5-1掲示板】"."<br>";
        echo "<hr>";
        //DB接続
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //全データの取得、表示
        $sql = 'SELECT * FROM Passtest';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' '.$row['name'].' '.$row['comment'].' '.$row['date'].'<br>';
        }
        echo "<hr>";
        //DB切断
        $pdo =  null;
    ?>
</body>
</html>