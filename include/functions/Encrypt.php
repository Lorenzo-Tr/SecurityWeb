<?php

function generate_key(): array
{
	$ivlen = openssl_cipher_iv_length("aes256");
	$iv = openssl_random_pseudo_bytes($ivlen);
	$key = openssl_random_pseudo_bytes(256/4);
	return ['iv' => $iv, 'key' => $key];
}

function encrypt_text($text, $key, $iv){
	return openssl_encrypt($text, "aes256", $key, $options=0, $iv);
}

function decrypt_text($ciphertext, $iv, $key){
	return openssl_decrypt($ciphertext, "aes256", $key, $options=0, $iv);
}
