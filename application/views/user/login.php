<div class="centered">
      <h1 class="in-eyecatcher text-contrast">Login</h1>
      <p>Please enter your login information below.</p>

      <?php echo form_open("user/login", array('class' => 'large'));?>
            <div class="input">
                  <?php echo form_label('E-Mail or Username', $username_email['id']);?><?php echo form_input($username_email);?>
            </div>

            <div class="input">
                  <?php echo form_label('Password', $password['id']);?><?php echo form_input($password);?>
            </div>

            <div class="input">
                  <?php echo form_label('Stay logged in', $stay_logged_in['id']);?><?php echo form_input($stay_logged_in);?>
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
                  <?php echo form_submit('submit', 'Login', 'class="centered"');?> <a href="<?= site_url('user/register') ?>">or register</a>
            </div>

      <?php echo form_close();?>
</div>