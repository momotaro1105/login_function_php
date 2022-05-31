<?php
    // file is created on page loaded
    // whenever a file is opened, a new line is created so that 
    $email = $_POST["email"];
    $file = fopen("data/email.txt","a");
    fwrite($file, $email."\n");
    fclose($file);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Sign up</title>
</head>
<body id="signup_page">
    <header></header>

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
                    <label for="email">Email:</label>
                    <input id="user_email" type="text" name="email" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input class="userpwd" id="userpwd_1" type="password" required>
                </div>
                <i class="material-icons togglepwd" id="toggle1">remove_red_eye</i>
                <div>
                    <label for="password">Re-enter password:</label>
                    <input class="userpwd" id="userpwd_2" type="password" required>
                </div>
                <i class="material-icons togglepwd" id="toggle2">remove_red_eye</i>
                <input id="signup_submit" type="submit" value="Sign up"></button>
            </form>
        </div>
    </div>

    <script src="./js/firebase.js" type="module"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="module">
        import { changeHeader } from "./js/firebase.js";
        changeHeader();

        // toggle password
        for (let i = 0; i<2; i++){
            const togglePassword = document.getElementsByClassName("togglepwd");
            const pwd = document.getElementsByClassName("userpwd");
            togglePassword[i].addEventListener("click", function (){
                const inputtype = pwd[i].getAttribute("type") != "password" ? "password" : "text";
                pwd[i].setAttribute("type", inputtype);
            })
        };

        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.8.1/firebase-app.js";
        import { getAuth, createUserWithEmailAndPassword, updateProfile, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/9.8.1/firebase-auth.js";

        const $signup_submit = document.getElementById("signup_submit")
        // authenticate user with email and password
        function registerUser(){
            const auth = getAuth();

            // password check
            const $userpwd1 = document.getElementById("userpwd_1");
            const $userpwd2 = document.getElementById("userpwd_2");
            let password = "";
            if (($userpwd2 != "") && ($userpwd1.value == $userpwd2.value)){
                password = $userpwd2.value;
            } else {
                $userpwd1.style.borderColor = "red";
                $userpwd2.style.borderColor = "red";
            }

            const $email = document.getElementById("user_email");
            const email = $email.value;
            // NOTE: when user is created, firebase automatically retains a signed in status
            // BUT: if the page does not reload or visually change, you can re-use the form to add a new user
            createUserWithEmailAndPassword(auth, email, password)
                .then(() => {
                    $email.value = "";
                    $userpwd1.value = "";
                    $userpwd2.value = "";
                    console.log("user created");
                    window.open("dashboard.html");
                })
                .catch((error) => {
                    console.log("error creating user", error);
                })
        }
        $signup_submit.addEventListener("click", registerUser);
    </script>
</body>
</html>