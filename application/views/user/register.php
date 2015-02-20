<div class="centered">
      <h1 class="in-eyecatcher text-contrast">Register</h1>
      <p>Please enter your information below. You can extend your information later. All here requested information is neccessarray.</p>

      <?php echo form_open("user/register", array('class' => 'large'));?>
            <div class="input">
                  <?php echo form_label('Username: *', 'username');?><?php echo form_input($username);?>
            </div>

            <div class="input">
                  <?php echo form_label('E-Mail: *', 'email');?><?php echo form_input($email);?>
            </div>

            <div class="input">
                  <?php echo form_label('Password: *', 'password');?><?php echo form_input($password);?>
            </div>

            <div class="input">
                  <?php echo form_label('Confirm password: *', 'password_confirm');?><?php echo form_input($password_confirm);?>
            </div>
            <div class="input">
                  <div class="centered"><?= $captcha ?></div>
            </div>

            <?php if(count($errors) > 0 && is_array($errors)) : ?>
            <div class="input">
                  <ul>
                  <?php
                        foreach($errors as $e) {
                              echo "<li>" . $e . "</li>";
                        }                  
                  ?>
                  </ul>
            </div>
            <?php endif; ?>

            <div class="input">
                  <?php echo form_submit('submit', 'Register', 'class="centered"');?>
            </div>

      <?php echo form_close();?>
</div>