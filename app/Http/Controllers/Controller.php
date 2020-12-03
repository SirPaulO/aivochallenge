<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController {
  /**
   * The request instance.
   *
   * @var Request
   */
  protected $request;

  /**
   * Create a new controller instance.
   *
   * @param Request $request
   *
   * @return void
   */
  public function __construct(Request $request) {
    $this->request = $request;
  }

  /**
   * Quick JSON response. It is a success by default.
   *
   * @param $msg
   * @param bool $isError
   * @param null $code
   * @param null $result
   *
   * @return JsonResponse
   */
  public function quickResponse($msg, $isError = false, $code = null, $result = null) {
    $result = $result != null ? $result : ($isError ? ['error'] : ['success']);
    $code = $code != null ? $code : ($isError ? 400 : 200);
    return response()->json(['result' => $result, 'msg' => $msg], $code);
  }
}
