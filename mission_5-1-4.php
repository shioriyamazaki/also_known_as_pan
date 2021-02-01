<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>mission_5-1-4</title>
</head>
<body>
<?php

error_reporting(E_ALL & ~E_NOTICE);  //NOTICEのエラーのみを表示しなくする。

//やることリスト：
//データベース接続
//投稿用
//削除
//編集
//html
//表示

//データベース接続設定
    $dsn = 'mysql:dbname='データベース名';host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    
//***************************************************************************************************************

//投稿用プログラム
    if( !empty($_POST['name']) && !empty($_POST['comment'])){  

        
//ポスト受信
        $name = $_POST['name'];            //名前用変数
        $comment = $_POST['comment'];      //コメント用変数
        $date = date("Y/m/d H:i:s");       //日付用変数
        $password1 = $_POST['password1'];  //投稿パスワード用変数
        
//テーブルの作成(4-2)
        $sql = "CREATE TABLE IF NOT EXISTS mission5_1_3"  //mission5_1という名前のテーブルを作る
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"          //idは自動で登録されているナンバリング
        . "name char(32),"                              //名前を入れる。文字列、半角英数で32文字
        . "comment TEXT,"                               //コメントを入れる。文字列、長めの文章も入る。
        . "date TEXT,"  
        . "password1 char(32)"                          //パスワードを入れる。
        .");";
        $stmt = $pdo->query($sql);                      //テーブルを作成するsql文を実行する
        
//データを入力(4-5)
        if(empty($_POST["editnum"]) && !empty($_POST['password1'])){ //もし編集対象番号が空であれば新規投稿
            $sql = $pdo -> prepare("INSERT INTO mission5_1_3 (name, comment, date, password1) VALUES (:name, :comment, :date, :password1)");

            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);  

            $sql -> bindParam(':password1', $password1, PDO::PARAM_STR);
            $sql -> execute();
            echo "投稿しました"; 
            
        }elseif(!empty($_POST["editnum"])){ //もし編集対象番号が空でなければ編集(4-7)
            $editnum = $_POST["editnum"] ; //ポスト受信   
            $id = $editnum;                //変更する投稿番号
            
            $name = $_POST["name"];        
            $comment = $_POST["comment"];  
            
            $sql = 'UPDATE mission5_1_3 SET name=:name,comment=:comment, date=:date, password1=:password1 WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password1', $password1, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $stmt->execute();  //sql文を実行可能にする
            
            echo "編集しました";  //編集に成功したことを伝える
        }
    }
    
//***************************************************************************************************************
    
//削除機能(4-8参照)
    if( !empty($_POST['dnum']) && !empty($_POST['password2'])){ //もしも削除対象番号と削除用パスワードが空でなければ 
        $deletenum = $_POST['dnum'];       //削除番号受信
        $password2 = $_POST['password2'];  //削除用パスワード受信
        $id = $deletenum;  //idが削除対象番号と一致するものだけ削除 
        
        $sql = 'SELECT * FROM mission5_1_3';   //mission5-1のすべてのデータを取ってくるsql文
        $stmt = $pdo->query($sql);           //SQL文を実行した結果を入れる
        //$stmt = $pdo->prepare($sql);
        //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        //$stmt->execute();
        $results = $stmt->fetchAll();  //fetchAll: すべての結果行を含む配列を返す
        
        foreach($results as $row){ //$rowの中にはテーブルのカラム名が入る
            if($row['id'] == $deletenum && $row['password1'] == $password2){ //もしidと削除対象番号が一致し、password1とpassword2が一致するならば
                //削除を実行する
                $sql = 'delete from mission5_1_3 where id=:id';  //idが削除番号と一致するところのデータを消すSQL文
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                echo "削除しました";    
                
            }elseif($row['id'] == $deletenum && $row['password1'] != $password2){ //もしidと編集対象番号が一致し、password1とpassword2が一致しないならば 
                echo "パスワードが違います";
            }
        }
    }elseif(!empty($_POST['dnum']) or !empty($_POST['password'])){ //削除対象番号か削除用のパスワードのいずれかが空欄の場合は
        echo "削除対象番号、パスワードを入力してください";
    } 
    
//***************************************************************************************************************
    
//編集選択機能
    if( !empty($_POST['edit']) && !empty($_POST['password3'])){ //もし編集対象番号と編集用のパスワードが空でなければ
        $edit = $_POST['edit']; 
        $password3 = $_POST['password3'];
        $id = $edit; //idが編集対象番号と一致するものだけ編集
        
        $sql = 'SELECT * FROM mission5_1_3';  
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        
        foreach ($results as $row){ //$rowの中にはテーブルのカラム名が入る
            if($row['id'] == $edit && $row['password1'] == $password3){//もしidと編集対象番号が一致し、password1とpassword3が一致するならば
                $editname =  $row['name'];
                $editcomment = $row['comment'];
                $editnumber = $row['id'];        
            }elseif($row['id'] == $edit && $row['password1'] != $password3){ //もしidと編集対象番号が一致し、password1とpassword3が一致しないならば    
                echo "パスワードが違います";
            }
        }        
    }elseif( !empty($_POST['edit']) or !empty($_POST['password3'])){ //編集対象番号か編集用のパスワードのいずれかが空欄の場合は
            echo "編集対象番号、パスワードを入力してください";
    } 
          
?>

<form action = "" method = "post">
    <input type = "text" name = "name" placeholder = "名前" value = "<?php echo $editname; ?>"> <br>
    <input type = "text" name = "comment" placeholder = "コメント" value = "<?php echo $editcomment; ?>"> <br>
    <input type = "hidden" name = "editnum" value = "<?php echo $editnumber; ?>">
    <input type = "text" name = "password1" placeholder = "パスワード">
    <input type = "submit" name = "s_button" value = "送信"> <br>
</form>

<form action = "" method = "post">
    <input type = "text" name = "dnum" placeholder = "削除対象番号">
    <input type = "text" name = "password2" placeholder = "パスワード">
    <input type = "submit" name = "d_button" value = "削除">
</form>

<form action = "" method = "post">
    <input type = "text" name = "edit" placeholder = "編集対象番号">
    <input type = "text" name = "password3" placeholder = "パスワード">
    <input type = "submit" value = "編集">
</form>

    <?php
        //表示(4-6参照)
        $sql = 'SELECT * FROM mission5_1_3';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
        echo "<hr>";
        }   
    ?>
</body>
</html>