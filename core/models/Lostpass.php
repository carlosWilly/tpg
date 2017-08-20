<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Lostpass extends Models implements OCREND {

	public function __construct() {
		parent::__construct();
	}

	final public function RepairPass(array $data) : array {

		$mail = $this->db->scape($data['user-email']);
		$user = $this->db->select('u_id,u_nombres','jc_usuarios',"u_email='$mail'",'LIMIT 1');

		if(false == $user) {
			$success = 0;
			$message = 'El <b>Email </b>entered does not exist.';
		} else {
			$id = $user[0]['u_id'];
			$u = uniqid();
			$keypass = time();

			$HTML = 'Hello <b>'. $user[0]['u_nombres'] .'</b>, You have requested to recover your lost password, if you have not done this you do not need to do anything.
					<br />
					<br />
					To change your password  <a href="'. URL .'lostpass/cambiar/'.$keypass.'" target="_blank">click here</a>.';

			Helper::load('emails');
			$dest[$mail] = $user[0]['u_nombres'];
			$email = Emails::send_mail($dest,Emails::plantilla($HTML),'Recover lost password');
			if(true === $email) {
				$e = array(
					'u_keypass' => $keypass,
					'u_keypass_tmp' => $u
				);
				$this->db->update('jc_usuarios',$e,"u_id='$id'",'LIMIT 1');
				$success = 1;
				$message = 'You have sent an email to  <b>' . $mail . '</b> retrieve your password.';
			} else {
				$success = 0;
				$message = $email;
			}

		}

		return array('success' => $success, 'message' => $message);
	}

	final public function UpdatePass() {
		$u = $this->db->select('u_id,u_keypass_tmp','jc_usuarios',"u_keypass='$this->id' AND u_keypass <> '0'",'LIMIT 1');

		if(false != $u) {

			Helper::load('strings');

			$id = $u[0]['u_id'];
			$pass = $u[0]['u_keypass_tmp'];
			$hash = Strings::hash($pass);

			$e = array(
				'u_keypass' => 0,
				'u_keypass_tmp' => '',
				'u_pass' => $hash
			);
			$this->db->update('jc_usuarios',$e,"u_keypass='$this->id' AND u_keypass <> '0' AND u_id='$id'");

			return $pass;
		}

		return false;
	}

	public function __destruct() {
		parent::__destruct();
	}
}

?>
