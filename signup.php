<?php
    include("php/header.php");
    include("php/util.php");
    $header = logStatus();

    include("php/database.php");
    include("php/session.php");
    $db = DbConn('userInfo'); // DB接続
    $result = mkTbIF('basicProfile', 'email VARCHAR(256),password VARCHAR(256),displayName VARCHAR(256),attempts INT(2)', $db); // テーブル作成
    $errorMessage = [];
    if (count($_POST) > 0){ // 注意：フォームを送信しなくても、$_POSTはそもそもスーパーグローバル変数だから既に空の配列として存在している
        $exisEmail = fldArray('email', 'basicProfile', $db); // テーブルから既存email値を配列形式で取得
        $exisDName = fldArray('displayName', 'basicProfile', $db); // テーブルから既存displayName値を配列形式で取得
        // displayName確認
        if (in_array($_POST['displayName'], $exisDName)){ // 既に使用されていないか
            $errorMessage[] = 'Display name taken';
        }
        // メールアドレス確認
        if (!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/', $_POST['email'])){ // フォーマット確認
            $errorMessage[] = 'Please check your email address';
        } else if (in_array($_POST['email'], $exisEmail)){ // 既に使用されていないか
            session_start();
            $_SESSION['email'] = $_POST['email'];
            $errorMessage[] = 'Email already in use: <a href="login.php">LOGIN HERE</a>';
        } 
        // パスワード確認
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_\-!#*@&])[A-Za-z\d_\-!#*@&]{8,30}$/', $_POST['password'])){ // フォーマット確認
            $errorMessage[] = 'Password requirements not met';
        } 
        // エラー無しならデータ登録
        if (count($errorMessage) == 0) {
            $errorMessage = []; // 配列を空にする
            session_start();
            logIn(); // セッションオブジェクトを更新
            $_SESSION['email'] = $_POST['email'];
            $_POST['attempts'] = 0; // ログインセキュリティ用
            $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT); // 登録用pwdハッシュ化
            $status = addData('basicProfile', 'email,password,displayName,attempts', $db, $_POST); // データ登録
            header('refresh:1;url=dashboard.php'); // ラグは念の為（上記配列処理用）
        }
    }
?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/style.css?<?php echo date('YmdHis')?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Sign up</title>
</head>
<body id="signup_page">
    <header><?=$header?></header>
    <div id="signup_body">
        <div id="signup_message">
            <p>Join our community today</p>
            <ul id="signup_pointers">
                <li>
                    <i class="material-icons">new_releases</i>
                    the answer never comes before questions
                </li>
                <li>
                    <i class="material-icons">lock</i>
                    questions and answers are published in fixed forms
                </li>
                <li>
                    <i class="material-icons">unfold_less</i>
                    less is more
                </li>
                <li>
                    <i class="material-icons">attach_money</i>
                    earn reputation and rewards
                </li>
            </ul>
        </div>
        <div id="signup_form">
            <div id="signup_google">
                <button data-provider="google" data-oauthserver="https://accounts.google.com/o/oauth2/auth" data-oauthversion="2.0">
                    <svg aria-hidden="true" class="native svg-icon iconGoogle" width="18" height="18">
                        <path d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18Z" fill="#4285F4"></path>
                        <path d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2a4.8 4.8 0 0 1-7.18-2.54H1.83v2.07A8 8 0 0 0 8.98 17Z" fill="#34A853"></path>
                        <path d="M4.5 10.52a4.8 4.8 0 0 1 0-3.04V5.41H1.83a8 8 0 0 0 0 7.18l2.67-2.07Z" fill="#FBBC05"></path>
                        <path d="M8.98 4.18c1.17 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.4L4.5 7.49a4.77 4.77 0 0 1 4.48-3.3Z" fill="#EA4335"></path>
                    </svg>
                    Sign up with Google
                </button>
            </div>
            <div id="signup_github">
                <button class="flex--item s-btn s-btn__icon s-btn__github bar-md ba bc-black-100" data-provider="github" data-oauthserver="https://github.com/login/oauth/authorize" data-oauthversion="2.0">
                    <svg aria-hidden="true" class="svg-icon iconGitHub" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M9 1a8 8 0 0 0-2.53 15.59c.4.07.55-.17.55-.38l-.01-1.49c-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82a7.42 7.42 0 0 1 4 0c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48l-.01 2.2c0 .21.15.46.55.38A8.01 8.01 0 0 0 9 1Z" fill="#010101"></path>
                    </svg>
                    Sign up with GitHub
                </button>
            </div>
            <form id="signup_email" method="post" action="">
                <div>
                    <label for="email">Display name:</label>
                    <input id="user_displayName" type="text" name="displayName" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input id="user_email" type="text" name="email" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input class="userpwd" id="userpwd_1" type="password" placeholder="1+ a~z/A~Z/#/special each, 4+ characters" required>
                </div>
                <i class="material-icons togglepwd" id="toggle1">remove_red_eye</i>
                <div>
                    <label for="password">Confirm password:</label>
                    <input class="userpwd" id="userpwd_2" name="password" type="password" placeholder="1+ a~z/A~Z/#/special each, 4+ characters" required>
                </div>
                <i class="material-icons togglepwd" id="toggle2">remove_red_eye</i>
                    <?php if ($errorMessage !== []): ?>
                        <?php for ($i=0;$i<count($errorMessage);$i++): ?>
                            <span style='color:red'><?=$errorMessage[$i]?></span>
                        <?php endfor; ?>
                    <?php endif; ?>
                <input id="signup_submit" type="button" value="Sign up">
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // toggle password
        for (let i = 0; i<2; i++){
            const $togglePassword = document.getElementsByClassName("togglepwd");
            const $pwd = document.getElementsByClassName("userpwd");
            $togglePassword[i].addEventListener("click", function (){
                const inputtype = $pwd[i].getAttribute("type") != "password" ? "password" : "text";
                $pwd[i].setAttribute("type", inputtype);
            })
        }

        // check password match
        const $signup_submit = document.getElementById("signup_submit");
        $signup_submit.addEventListener("click", function(){
            const $userpwd1 = document.getElementById("userpwd_1");
            const $userpwd2 = document.getElementById("userpwd_2");
            let password = "";
            if (($userpwd2 != "") && ($userpwd1.value === $userpwd2.value)){ // 両パスが合わないと、type submitに変更されずPHPにデータが送信されない
                $signup_submit.setAttribute("type", "submit");
            } else {
                $userpwd1.style.borderColor = "red";
                $userpwd2.style.borderColor = "red";
            }
        })
    </script>
</body>
</html>