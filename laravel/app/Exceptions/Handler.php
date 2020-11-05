<?php

namespace App\Exceptions;
 
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
        $this->renderable(function (BindingResolutionException $e, $request) {
            return responder()->error(404, 'Erro 404')->respond(404);
        });
        $this->renderable(function (AuthenticationException $e, $request) {
            return responder()->error(401,'Acesso negado')->respond(401);
        });
        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            if($e->getMessage() == 'Token not provided') $message = 'Token não fornecido.';
            if($e->getMessage() == 'Token has expired') $message = 'O token expirou.';
            if($e->getMessage() == 'Token Signature could not be verified.') $message = 'Token inválido.';
            if($e->getMessage() == 'Could not decode token: Error while decoding to JSON: Syntax error') $message = 'Token inválido.';
            
            return responder()->error(401, $message)->respond(401);
        });
        $this->renderable(function (ValidationException $e, $request) {
            $errors = [];
            foreach ($e->validator->getMessageBag()->toArray() as $k => $v) {
 
                $k = preg_replace('/\.[0-9+]+/', '', $k);
                $exists = array_search($k, array_column($errors, 'fieldname'));
 
                if ($exists !== false)
                    continue;
 
                $errors[] = [
                    'fieldname' => $k,
                    'message' => $v[0]
                ];
            }
 
            return responder()->error(422,'Ocorreu um erro de validação')->data([
                'errors'=>$errors
            ])->respond( 422);
        });
    }
 
//    public function render($request, Throwable $e)
//    {
//        dd($e); //descomente para verificar o nome da classe que está gerando a exception
//    }
 
}
