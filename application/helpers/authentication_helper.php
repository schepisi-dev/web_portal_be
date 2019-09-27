<?php
	if (!defined('BASEPATH'))
		exit('No direct script access allowed');

	if (!function_exists('crypto_rand_secure')) {
		function crypto_rand_secure ( $min, $max ) {
			$range = $max - $min;
			if ( $range < 1 ) return $min; // not so random...
			$log = ceil( log( $range, 2 ) );
			$bytes = (int)($log / 8) + 1; // length in bytes
			$bits = (int)$log + 1; // length in bits
			$filter = (int)(1 << $bits) - 1; // set all lower bits to 1
			do {
				$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
				$rnd = $rnd & $filter; // discard irrelevant bits
			} while ( $rnd > $range );
			return $min + $rnd;
		}
	}

	if (!function_exists('getToken')) {
		function getToken ( $length ) {
			$token = "";
			$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
			$codeAlphabet .= "0123456789";
			$max = strlen( $codeAlphabet ); // edited

			for ( $i = 0; $i < $length; $i++ ) {
				$token .= $codeAlphabet[ crypto_rand_secure( 0, $max - 1 ) ];
			}

			return $token;
		}
	}

	if (!function_exists('checkPassword')) {
		function checkPassword($password, $field = 'password') {
			
			$CI =& get_instance();
			$CI->load->model( 'Configs_model', 'configs' );
			$pw_config =  unserialize($CI->configs->get_config( 'pw_config' ));
			
			$max_length = $pw_config['pw_max_length'];
			$min_length = $pw_config['pw_min_length'];
			$allow_special_characters = $pw_config['pw_allow_special_character'];

			$error = array(
				'e_required' => "The {$field} field is required",
				'e_lowercase' => "The {$field} field must have at least one lowercase letter",
				'e_uppercase' => "The {$field} field must have at least one uppercase letter",
				'e_number' => "The {$field} field must have at least one number",
				'e_special_character' => "The {$field} field must have at least one special character",
				'e_min_length' => "The {$field} field must bet at least {$min_length} characters in length",
				'e_max_length' => "The {$field} field cannot exceed {$max_length} characters in length",

			);

			$password = trim($password);

			//Rules
			$regex_lowercase = '/[a-z]/';
			$regex_uppercase = '/[A-Z]/';
			$regex_number = '/[0-9]/';
			$regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

			if (empty($password)){ return $error['e_required']; }

			if (preg_match_all($regex_lowercase, $password) < 1){ return $error['e_lowercase']; }

			if (preg_match_all($regex_uppercase, $password) < 1){ return $error['e_uppercase']; }

			if (preg_match_all($regex_number, $password) < 1){ return $error['e_number']; }

			if ($allow_special_characters=='TRUE' AND (preg_match_all($regex_special, $password) < 1)){ return $error['e_special_character']; }

			if (strlen($password) < $min_length){ return $error['e_min_length']; }

			if (strlen($password) > $max_length){ return $error['e_max_length']; }
			
			return TRUE;
		}
	}