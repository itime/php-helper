<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Encryption;

interface Encrypter {

	/**
	 * Encrypt the given value.
	 *
	 * @param mixed $value
	 * @param bool  $serialize
	 * @return string
	 * @throws \Xin\Contracts\Encryption\EncryptException
	 */
	public function encrypt($value, $serialize = true);

	/**
	 * Decrypt the given value.
	 *
	 * @param string $payload
	 * @param bool   $unserialize
	 * @return mixed
	 * @throws \Xin\Contracts\Encryption\DecryptException
	 */
	public function decrypt($payload, $unserialize = true);

}
