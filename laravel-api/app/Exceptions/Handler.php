<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use App\Http\Controllers\Api\Oauth\InvalidCredentialsException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e) {
            return $this->handleException($e);
        });
    }

    /**
     * @param Throwable $e
     * @return JsonResponse
     */
    public function handleException(Throwable $e) {

        if ($e instanceof AccessDeniedHttpException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage(),
                    'status_code'   => 403
                ], 403);
        }


        if ($e instanceof AuthenticationException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage(),
                    'status_code'   => 401
                ], 401);
        }

        if ($e instanceof \InvalidArgumentException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage(),
                    'status_code'   => $e->getCode()
                ], 404);
        }

        if ($e instanceof NotFoundHttpException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage() != '' ? $e->getMessage() : 'Sorry, the resource you are looking for could not be found..',
                    'status_code'   => $e->getCode()
                ], 404);
        }

        if ($e instanceof MissingScopeException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'You do not have permission to access this resource..',
                    'status_code'   => 403
                ], 403);
        }

        if ($e instanceof AuthorizationException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'This action is unauthorized. You do not have permission to access this resource..',
                    'status_code'   => 403
                ], 403);
        }

        if ($e instanceof MethodNotAllowedHttpException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Method is not allowed.',
                    'status_code'   => 405
                ], 405);
        }

        if ($e instanceof UnauthorizedHttpException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Provided login credentials were incorrect ...',
                    'status_code'   => 401
                ], 401);
        }

        if ($e instanceof InvalidCredentialsException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Provided login credentials were incorrect ...',
                    'status_code'   => 401
                ], 401);
        }

        if ($e instanceof JsonEncodingException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Invalid data provided ...',
                    'status_code'   => 400
                ], 400);
        }

        if ($e instanceof DecryptException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'The MAC is invalid. CHeck application keys',
                    'status_code'   => 401
                ], 401);
        }

        if ($e instanceof ModelNotFoundException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage(),
                    'status_code'   => $e->getCode()
                ], $e->getCode());
        }

        if ($e instanceof OAuthServerException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage(),
                    'status_code'   => $e->getCode()
                ], $e->getCode());
        }

        if ($e instanceof \Illuminate\Validation\ValidationException){
            return response()->json(
                [
                    'error'         => true,
                    'message'       => $e->getMessage(),
                    'errors'         => $e->errors(),
                    'status_code'   => 422
                ], 422);
        }
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function dataResponse($data)
    {
        return response()->json(['content' => $data], Response::HTTP_OK);
    }

    /**
     * @param string $message
     * @param $code
     * @return JsonResponse
     */
    public function successResponse(string $message, $code = Response::HTTP_OK)
    {
        return response()->json(['success' => $message, 'code' => $code], $code);
    }

    /**
     * Error Response
     * @param $message
     * @param int $code
     * @return JsonResponse
     *
     */
    public function errorResponse($message, $code = Response::HTTP_BAD_REQUEST)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}
