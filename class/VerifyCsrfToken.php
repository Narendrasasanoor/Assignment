<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Session;
use Redirect;
use Closure;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends BaseVerifier
{

    


    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getTokenFromRequest($request)
    {
        
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        

        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header, static::serialized());
        }

        return $token;
    }

    public function handle($request, Closure $next)
    {
        $token = getTokenFromRequest($request);
        if ($token && Session::get('UsrId')) {
            if (isset($decoded['url'])) {
                return Redirect::to($decoded['url']);
            }
            return Redirect::to('/home');
        }else{

            throw new TokenMismatchException;
            return Redirect::to('/logout');
        }

    }
}
