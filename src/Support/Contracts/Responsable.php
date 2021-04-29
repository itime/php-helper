<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author BD<657306123@qq.com>
 */
namespace Xin\Support\Contracts;

interface Responsable{

	/**
	 * Create an HTTP response that represents the object.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function toResponse($request);
}
