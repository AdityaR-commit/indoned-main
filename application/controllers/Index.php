<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Index extends BaseController
{

	protected $template = "app_front";

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_index');
		$this->load->model('m_activity_log');
	}

	public function index()
	{
		$this->data['title'] = 'Beranda';
		$this->render('index');
	}

	public function forgot_password()
	{
		$this->template = "login";
		if ($this->input->post("reset-button")) {
			$result = $this->db->query("SELECT id_user FROM user WHERE email = ?", array($this->input->post("email")));
			if ($result->num_rows() == 0) {
				$this->data["errorMessage"] = "Email tidak ditemukan/tidak valid.";
			} else {
				$hasil = $result->row_array();
				$id_user = $hasil['id_user'];

				$status = $this->db->query("INSERT INTO reset_password (email, reset_code, request_time) VALUES (?, SHA2(CAST(RAND() AS CHAR), 256), NOW())", array($this->input->post("email")));

				if ($status) {
					$result = $this->db->query("SELECT a.reset_code, a.request_time, a.email, b.email FROM reset_password a INNER JOIN user b ON a.email = b.email WHERE a.email = ? ORDER BY a.request_time DESC LIMIT 1", array($this->input->post("email")));

					if ($result->num_rows() > 0) {
						$row = $result->first_row();

						$requestTime = $this->tanggalindo->konversi($row->request_time) . " pukul " . $this->tanggalindo->jam($row->request_time) . " WIB";
						$resetUrl = base_url("forgot_password_reset?token=" . $row->reset_code . "&email=" . $this->input->post("email"));

						$emailContent = $this->load->view("index/forgot_password_email", array("resetUrl" => $resetUrl, "username" => $row->email, "requestTime" => $requestTime), true);

						$status = $this->send_email_aktifasi($row->email, "Reset Password", $emailContent);

						if ($status) {
							$statusReset = $this->db->query("UPDATE reset_password SET send_time = NOW() WHERE email = ? AND reset_time IS NULL", array($row->email));

							if ($statusReset) {
								$this->data["successMessage"] = "Instruksi untuk reset password telah dikirim ke email anda.";
								$this->m_activity_log->insert($id_user, "Request Reset Password", base_url('forgot_password'));
							} else {
								$this->data["errorMessage"] = "Gagal memperbarui reset sent time, silahkan coba lagi.";
							}
						} else {
							$this->data["errorMessage"] = "Gagal mengirim reset code ke email anda, silahkan coba lagi.";
						}
					} else {
						$this->data["errorMessage"] = "Gagal membuat reset code, silahkan coba lagi.";
					}
				} else {
					$this->data["errorMessage"] = "Gagal membuat reset code, silahkan coba lagi.";
				}
			}
		}

		$this->render("forgot_password");
	}

	public function forgot_password_reset()
	{
		$this->load->database('bgskin', FALSE, TRUE);
		$this->template = "login";

		$this->data["token"] = $this->input->get("token");
		$this->data["email"] = $this->input->get("email");

		if ($this->input->post("reset-button") != null) {
			if ($this->input->post("email") == "") {
				$this->data["errorEmailMessage"] = "Email harus diisi.";
			} else if ($this->input->post("password") == "") {
				$this->data["errorPasswordMessage"] = "Password harus diisi.";
				$this->data["pass1_temp"] = $this->input->post("password");
				$this->data["pass2_temp"] = $this->input->post("confirm");
			} else if ($this->input->post("password") != $this->input->post("confirm")) {
				$this->data["errorConfirmPasswordMessage"] = "Konfirmasi Password tidak sesuai dengan password baru.";
				$this->data["pass1_temp"] = $this->input->post("password");
				$this->data["pass2_temp"] = $this->input->post("confirm");
			} else {
				$result = $this->db->query("SELECT email FROM reset_password WHERE reset_code = ? AND email = ? ORDER BY request_time DESC LIMIT 1", array($this->input->get("token"), $this->input->post("email")));

				if ($result->num_rows() == 0) {
					$this->data["errorEmailMessage"] = "Gagal mem-verifikasi email dan token, harap pastikan email yang anda masukkan sudah benar.";
				} else {
					$statusReset = $this->db->query("UPDATE reset_password SET reset_time = NOW() WHERE email = ? AND reset_time IS NULL", array($this->input->post("email")));

					if ($statusReset) {
						$statusChangePassword = $this->db->query("UPDATE user SET password = SHA2(?, 256) WHERE email = ?", array($this->input->post("password"), $this->input->post("email")));

						if ($statusChangePassword) {
							$this->data["successMessage"] = "Password berhasil diperbarui, silahkan kembali untuk login menggunakan password baru anda.";
							redirect("?forgot_password=true");
						} else {
							$this->data["errorMessage"] = "Gagal me-reset password, silahkan coba reset lagi <a href='" . base_url("forgot_password") . "'>disini</a> untuk mendapat link reset password baru.";
						}
					} else {
						$this->data["errorMessage"] = "Gagal flag reset password, silahkan coba lagi.";
						$this->data["pass1_temp"] = $this->input->post("password");
						$this->data["pass2_temp"] = $this->input->post("confirm");
					}
				}
			}

			$this->render("forgot_password_reset");
		} else {
			if ($this->input->get("token") == null || $this->input->get("token") == "") {
				exit("Token kosong.");
			} else {
				$result = $this->db->query("SELECT email FROM reset_password WHERE reset_code = ? AND reset_time IS NULL ORDER BY request_time DESC LIMIT 1", array($this->input->get("token")));

				if ($result->num_rows() == 0) {
					exit("Token tidak valid");
				} else {
					$this->render("forgot_password_reset");
				}
			}
		}
	}


	public function send_email_aktifasi($recipient, $subyek, $message)
	{
		date_default_timezone_set('Asia/Jakarta');

		$CI = &get_instance();
		$CI->config->load('email');
		$sender = $CI->config->item('smtp_user');
		$this->email->from($sender, 'Admin Siakad STAIN Kediri'); //reply to
		$this->email->to($recipient);
		$this->email->subject($subyek);
		$this->email->message($message);
		return $this->email->send();
	}

	public function logout()
	{
		$id_user = $this->session->userdata('bgskin_userId');
		$data = [
			'activity' => "LOG OUT",
			'page_url' => base_url("?logout=true")
		];

		$this->setLog($id_user, $data);

		$this->unSetUserData();
		$this->load->database("default", FALSE, TRUE); //CHANGE DB TO DEFAULT sialogin
		if ($this->session->flashdata('changepassword')) {
			$this->session->set_flashdata('true', 'Ubah password berhasil, silahkan login kembali.');
			// redirect("login?logout=true");
		}
		redirect("?logout=true");
	}

	public function password()
	{
		$this->data['title'] = 'Password';
		if ($this->session->userdata('bgskin_groupName')  == "Administrator") {
			$this->data['title'] = 'Profil';
		}
		$this->data["changePassword"] = $this->input->post("change_password");
		if ($this->input->post("submit-button")) {
			if ($this->input->post("username") == null || $this->input->post("username") == "") {
				$this->data["errorUsernameMessage"] = "Username harus diisi";
			}
			if ($this->input->post("email") == null || $this->input->post("email") == "") {
				$this->data["errorEmailMessage"] = "Email harus diisi";
			}

			if ($this->input->post("change_password") != null && $this->input->post("change_password") == 1) {
				if ($this->input->post("oldpassword") == null || $this->input->post("oldpassword") == "") {
					$this->data["errorOldPasswordMessage"] = "Password Lama harus diisi";
				} else {
					$resultcheck = $this->db->query("SELECT u.id_user FROM user u  WHERE u.id_user = ? AND u.password = SHA2(?,256)", array(intval($this->data["bgskin_userId"]), $this->input->post("oldpassword")));
					if ($resultcheck->num_rows() == 0) {
						$this->data["errorOldPasswordMessage"] = "Password Lama salah";
					}
				}

				if ($this->input->post("password") == null || $this->input->post("password") == "") {
					$this->data["errorPasswordMessage"] = "Password harus diisi";
				}

				if ($this->input->post("confirm") == null || $this->input->post("confirm") == "") {
					$this->data["errorConfirmMessage"] = "Konfirmasi Password harus diisi";
				} else if ($this->input->post("confirm") != null && $this->input->post("confirm") != "" && $this->input->post("password") != $this->input->post("confirm")) {
					$this->data["errorConfirmMessage"] = "Konfirmasi Password tidak sesuai dengan password baru";
				}
			}

			if (!isset($this->data["errorUsernameMessage"]) && !isset($this->data["errorEmailMessage"]) && !isset($this->data["errorOldPasswordMessage"]) && !isset($this->data["errorPasswordMessage"]) && !isset($this->data["errorConfirmMessage"])) {
				$status = false;
				if ($this->input->post("change_password") != null && $this->input->post("change_password") == 1) {
					$status = $this->db->query("UPDATE user SET username = ?, email = ?, password = SHA2(?, 256) WHERE id_user = ?", array($this->input->post("username"), $this->input->post("email"), $this->input->post("password"), $this->data["bgskin_userId"]));
				} else {
					$status = $this->db->query("UPDATE user SET email = ? WHERE id_user = ?", array($this->input->post("email"), $this->data["bgskin_userId"]));
				}

				if ($status) {
					$this->data["successMessage"] = "Profil berhasil diperbarui";
				} else {
					$this->data["errorMessage"] = "Gagal memperbarui data, silahkan coba lagi.";
				}
			} else {
				$this->data["errorMessage"] = "Gagal memperbarui profil, silahkan check data yang error di bawah.";
			}
		}

		$resultquery = $this->db->query("SELECT u.id_user, ug.nama_group, ug.keterangan, u.username, u.email FROM user u LEFT JOIN user_group ug ON u.id_group = ug.id_group WHERE u.id_user = ? AND ug.id_group = ?", array(intval($this->data["bgskin_userId"]), intval($this->data["bgskin_idGroup"])));

		$result = $resultquery->first_row();
		$this->data["detail"] = array(
			"id" => $result->id_user,
			"nama_group" => $result->nama_group,
			"username" => $result->username,
			"email" => $result->email,
			"keterangan" => $result->keterangan,
		);

		$this->render("password");
	}
}
