<h1>BG Skin</h1>
<h2>Informasi Akun Member Area BG Skin</h2>

<p>Hi, <?php echo isset($name) ? $name : $username; ?> <br>
	Anda telah bergabung menjadi <?= $member_type ?> BG Skin</p>

<p>Username : <b><?php echo $username ?></b><br>
	Password : <b><?php echo $password ?></b></p>

<br>

<p>Silahkan mencatat informasi tersebut.<br>
	Silahkan gunakan akun tersebut untuk login dengan <a href="<?= site_url('login') ?>">Klik di sini</a>.<br>
	Jangan memberikan informasi akun Anda kepada siapapun.<br>
	Bila mempunyai pertanyaan silahkan hubungi admin.
</p>
<br>
<p>Hormat Kami,<br>
	Administrator BG Skin</p>