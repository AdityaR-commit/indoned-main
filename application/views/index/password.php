<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
      <li class="breadcrumb-item"><a href="<?=site_url()?>">Dashboard</a></li>
      <?php if($this->session->userdata("bgskin_groupName") != "Administrator"): ?>
      <li class="breadcrumb-item"><a href="<?=site_url('index')?>/profile">Profile</a>
      <?php endif;?>
      <li class="breadcrumb-item active"><a href="#"><?=$title?></a></li>
    </ol>
    <!-- <h1 class="df-title"><?=$title?></h1> -->
  </nav>
  <?php echo form_open("index/password", array("id" => "update-form")); ?>	
  <?php if (isset($successMessage)): ?>
  <div class="row">
    <div class="col-md-12">
      <div class="callout callout-success">
        <h4>Success!</h4>
        <p><?php echo $successMessage ?></p>
      </div>
    </div>
  </div>
  <?php elseif (isset($errorMessage)): ?>
  <div class="row">
    <div class="col-md-12">
      <div class="callout callout-danger">
        <h4>Errors!</h4>
        <p><?php echo $errorMessage ?></p>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <div class="row">
    <div class="col-md-6">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><?=$title?></h3>
          <!-- <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div> -->
        </div>
        <div class="box-body">
          <div class="form-group field-username required <?php echo isset($errorUsernameMessage) ? "has-error" : "" ?>">
            <label class="control-label" for="username">Username</label>
            <input type="hidden" id="is_satker" class="form-control" name="id_user" value="<?php echo $detail['id']; ?>">
            <input type="text" readonly id="username" class="form-control" name="username" value="<?php echo $detail['username']; ?>">
            <p class="help-block help-block-error text-danger"></p>
          </div>
          <div class="form-group field-email required <?php echo isset($errorEmailMessage) ? "has-error" : "" ?>">
            <label class="control-label" for="email">Email</label>
            <input type="text" id="email" class="form-control" name="email" value="<?php echo $detail['email']; ?>">
            <p class="help-block help-block-error text-danger"></p>
          </div>
          <div class="form-group field-group required">
            <label class="control-label" for="group">User Group</label>
            <input type="text" id="group" class="form-control" name="group" value="<?php echo $detail['nama_group'] . " [" . $detail['keterangan'] . "]"; ?>" readonly>
            <p class="help-block help-block-error text-danger"></p>
          </div>						
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-default">
       <div class="box-header with-border">
        <h3 class="box-title">Password</h3>
        <!-- <div class="box-tools pull-right">
          <button class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
          </button>
        </div> -->
      </div>
      <div class="box-body">
        <div class="form-group field-user-change_password">
          <div class="checkbox">
            <label>
              <input <?php echo $changePassword != null ? "checked='checked'" : "" ?> id="user-change_password" name="change_password" value="1" type="checkbox">
              Change Password
            </label>
          </div>
        </div>
        <!-- ================================ -->
        <div id="password-box" class="<?php echo $changePassword == null ? "hide" : "" ?>">							
          <div class="form-group field-password required <?php echo isset($errorOldPasswordMessage) ? "has-error" : "" ?>">
            <label>Password Lama</label>
            <div class="input-group mg-b-10">
              <input type="password" id="oldpassword" class="form-control" name="oldpassword">
              <div class="input-group-append">
                <span onClick="mouseoverPass0();" id="eye_o_0" style="display:block;" data-toggle='tooltip' title='' data-original-title='Show Password' class="btn btn-outline-light"><i class="fa fa-eye"></i></span>
                <span onClick="mouseoutPass0();" id="eye_c_0" style="display:none;" data-toggle='tooltip' title='' data-original-title='Hide Password' class="btn btn-light"><i class="fa fa-eye-slash"></i></span>
              </div>
            </div>
            <p class="help-block help-block-error text-danger"><?php echo isset($errorOldPasswordMessage) ? $errorOldPasswordMessage : "" ?></p>
          </div>
          <div class="form-group field-password required <?php echo isset($errorPasswordMessage) ? "has-error" : "" ?>">
            <label>Password Baru</label>
            <div class="input-group mg-b-10">
              <input type="password" id="password" class="form-control" name="password">
              <div class="input-group-append">
                <span onClick="mouseoverPass1();" id="eye_o_1" style="display:block;" data-toggle='tooltip' title='' data-original-title='Show Password' class="btn btn-outline-light"><i class="fa fa-eye"></i></span>
                <span onClick="mouseoutPass1();" id="eye_c_1" style="display:none;" data-toggle='tooltip' title='' data-original-title='Hide Password' class="btn btn-light"><i class="fa fa-eye-slash"></i></span>
              </div>
            </div>
            <p class="help-block help-block-error text-danger"><?php echo isset($errorOldPasswordMessage) ? $errorOldPasswordMessage : "" ?></p>
          </div>
          <div class="form-group field-password required <?php echo isset($errorPasswordMessage) ? "has-error" : "" ?>">
            <label>Confirm Password</label>
            <div class="input-group mg-b-10">
              <input type="password" id="confirm" class="form-control" name="confirm" data-parsley-notequalto="#password" required>
              <div class="input-group-append">
                <span onClick="mouseoverPass2();" id="eye_o_2" style="display:block;" data-toggle='tooltip' title='' data-original-title='Show Password' class="btn btn-outline-light"><i class="fa fa-eye"></i></span>
                <span onClick="mouseoutPass2();" id="eye_c_2" style="display:none;" data-toggle='tooltip' title='' data-original-title='Hide Password' class="btn btn-light"><i class="fa fa-eye-slash"></i></span>
              </div>
            </div>
            <p class="help-block help-block-error text-danger"><?php echo isset($errorOldPasswordMessage) ? $errorOldPasswordMessage : "" ?></p>
          </div>
        </div>
        <!-- ==================================== -->
      </div>
    </div>
    <div class="box box-solid">
      <div class="box-body clearfix">
        <a href="<?=site_url()?>" class="btn btn-warning btn-flat">
          <span class="fa fa-chevron-left"></span> Back
        </a>
        <button type="submit" class="btn btn-primary pull-right" name="submit-button" value="Submit Button">Submit</button>							
      </div>
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>
<!-- </section> -->
</div>
<?php $this->load->view('template/template_scripts') ?>
<script type="text/javascript">
    function mouseoverPass0() {
        document.getElementById('eye_o_0').style.display = 'none';
        document.getElementById('eye_c_0').style.display = 'block';
        var obj = document.getElementById('oldpassword');
        obj.type = "text";
    }
    function mouseoutPass0() {
        document.getElementById('eye_o_0').style.display = 'block';
        document.getElementById('eye_c_0').style.display = 'none';
        var obj = document.getElementById('oldpassword');
        obj.type = "password";
    }
    function mouseoverPass1() {
      document.getElementById('eye_o_1').style.display = 'none';
      document.getElementById('eye_c_1').style.display = 'block';
      var obj = document.getElementById('password');
      obj.type = "text";
    }
    function mouseoutPass1() {
      document.getElementById('eye_o_1').style.display = 'block';
      document.getElementById('eye_c_1').style.display = 'none';
      var obj = document.getElementById('password');
      obj.type = "password";
    }
    function mouseoverPass2() {
      document.getElementById('eye_o_2').style.display = 'none';
      document.getElementById('eye_c_2').style.display = 'block';
      var obj = document.getElementById('confirm');
      obj.type = "text";
    }
function mouseoutPass2() {
  document.getElementById('eye_o_2').style.display = 'block';
  document.getElementById('eye_c_2').style.display = 'none';
  var obj = document.getElementById('confirm');
  obj.type = "password";
}
</script>

<script type="text/javascript">
    var site_url = "<?=site_url()?>";
</script>