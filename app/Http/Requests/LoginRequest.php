<?php namespace App\Http\Requests;

use App\Models\User;
use Auth;
use DB;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;
use LdapRecord\Auth\PasswordRequiredException;
use LdapRecord\Auth\UsernameRequiredException;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'EL CAMPO DE NOMBRE DE USUARIO ES REQUERIDO',
            'username.string' => 'EL CAMPO DE NOMBRE DE USUARIO DEBE DE SER UNA CADENA VALIDA',
            'password.required' => 'EL CAMPO DE CONTRASEÑA ES REQUERIDA',
            'password.string' => 'EL CAMPO DE CONTRASEÑA DEBE DE SER UNA CADENA VALIDA',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function authenticateSu(): void
    {
        RateLimiter::clear($this->throttleKey());
        $userSystem = User::where('username', strtoupper(trim($this->get('username'))))
            ->orWhere('username', strtolower($this->get('username')))
            ->first();
        $this->ensureIsNotRateLimited();
        $this->ensureIsNotActiveSession($userSystem);
        try {
            if ($userSystem && isset($userSystem->id)) {
                if (Auth::attempt([
                        'username' => $userSystem->username,
                        'password' => $this->get('password')
                    ]) && (($userSystem->is_active == 1) || $userSystem->is_active == true)) {
                    RateLimiter::clear($this->throttleKey());
                    Auth::loginUsingId($userSystem->id);
                    $userSystem->is_logged_in = true;
                    $userSystem->save();
                } else {
                    RateLimiter::hit($this->throttleKey(), 600);
                    throw ValidationException::withMessages([
                        'error' => ('Datos incorrectos')
                    ]);
                }
            }
            if(!$userSystem){
                RateLimiter::hit($this->throttleKey(), 600);
                throw ValidationException::withMessages([
                    'error' => ['Datos incorrectos']
                ]);
            }
        } catch (PasswordRequiredException|UsernameRequiredException $e) {
            RateLimiter::hit($this->throttleKey(), 600);
            throw ValidationException::withMessages([
                'error' => ['auth.failed']
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if ( !RateLimiter::tooManyAttempts($this->throttleKey(), 5) ) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'error' => trans($this->getTimeMessage($seconds), [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function getTimeMessage(int $seconds): string
    {
        if ($seconds < 60) {
            return "Demasiados intentos, trate nuevamente en $seconds segundos.";
        } else {
            $minutes = floor($seconds / 60);
            $seconds = $seconds - ($minutes * 60);
            $formattedTime = gmdate('s', $seconds);
            return "Demasiados intentos, trate nuevamente en $minutes:$formattedTime minutos.";
        }
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @param $user
     *
     * @return void
     */
    public function ensureIsNotActiveSession($user): void
    {
        if ($user && $user->isLoggedIn) {
            Auth::guard('web')->logout();

            DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->update(['revoked' => true]);

            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            $user->isLoggedIn = false;
            $user->save();
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('username')) . '|' . $this->ip();
    }
}
