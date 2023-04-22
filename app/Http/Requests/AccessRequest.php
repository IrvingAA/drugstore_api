<?php namespace App\Http\Requests;

use App\Models\User;
use Auth;
use DB;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LdapRecord\Auth\PasswordRequiredException;
use LdapRecord\Auth\UsernameRequiredException;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class AccessRequest extends FormRequest
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
    public function authenticateSu($data, $request): void
    {
        try {
            $userSystem = User::whereUsername($data['username'])->first();

            $this->ensureIsNotRateLimited($request);
            $this->ensureIsNotActiveSession($userSystem);

            $connection = Container::getConnection('default');
            $user = LdapUser::where([
                'samaccountname' => $data['username'],
                'useraccountcontrol' => '512'
            ])->first();

            if ($userSystem && $user && $connection->auth()->attempt($user->getDn(), $data['password'])) {
                RateLimiter::clear($this->throttleKey($data));
                Auth::loginUsingId($userSystem->id);
                $userSystem->isLoggedIn = true;
                $userSystem->save();
            } else {
                RateLimiter::hit($this->throttleKey($data), 600);
                throw ValidationException::withMessages([
                    'error' => __('auth.failed')
                ]);
            }
        } catch (PasswordRequiredException|UsernameRequiredException $e) {
            RateLimiter::hit($this->throttleKey($data), 600);

            throw ValidationException::withMessages([
                'error' => __('auth.failed')
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
    public function ensureIsNotRateLimited($data): void
    {
        try {
            if (!RateLimiter::tooManyAttempts($this->throttleKey($data), 5)) {
                return;
            }

            event(new Lockout($this));

            $seconds = RateLimiter::availableIn($this->throttleKey($data));

            throw ValidationException::withMessages([
                'error' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
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
    public function throttleKey($data): string
    {
        return Str::lower($data['username']) . '|' . $data['ip'];
    }
}
