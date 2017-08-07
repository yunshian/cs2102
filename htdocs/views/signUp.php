<div class="form">
    <h1>Sign Up Now!<span>Sign up and tell us what you think of the site!</span></h1>
    <form action="/cs2102/signup" method="POST">
        <div class="section">Username &amp; Password</div>
        <div class="inner-wrap">
            <label>Your username <input type="text" name="username" required/></label>
            <label>Password <input type="password" id="password" name="password" required/></label>
            <label>Confirm Password <input type="password" id="confirm_password" name="confirm_password" required/></label>
        </div>

        <div class="button-section">
             <input type="submit" value="Sign Up" name="submit"/>
             <span class="privacy-policy">
                <input type="checkbox" name="termsandcondition" required>You agree to our Terms and Policy. 
             </span>
        </div>
    </form>

    <!-- Ensure the password and confirm_password fields are the same -->
    <script>
        var password = document.getElementById("password")
        , confirm_password = document.getElementById("confirm_password");

        function validatePassword(){
          if(password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Passwords don't match");
          } else {
            confirm_password.setCustomValidity('');
          }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    </script>

    <?php if ($usernameTaken): ?>
        <span class="errorMessage">
            The username you chose has already been taken. Please choose another.
        </span>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="button-section">
            <span class="successfulMessage">
                You have successfully created your account!
            </span>
            <a href="http://localhost/cs2102/main">
                <input type="button" value="Return to main page" name="return"/>
            </a>
        </div>
    <?php endif; ?>
</div>