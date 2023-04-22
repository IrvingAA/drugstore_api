<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Spc\SpcCatalogs\SreCatGroupOfficesSreCatOffice;
use App\Models\Spc\SpcCatalogs\SreCatOffice;
use App\Models\User;
use Auth;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticateSu();

            $credentials = $request->validated();
            $user = User::where('username', strtoupper(trim($credentials['username'])))
                ->orWhere('username', strtolower(trim($credentials['username'])))
                ->first();
            $token = $user->createToken('drugstore_token')->accessToken;

            return response()->json([
                'success' => true,
                'authenticated' => true,
                'user' => $this->getAccountInfo($user->id),
                'session' => (object)[
                    'token' => $token,
                    'hash' => encrypt($user->id)
                ]
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function accessCard(Request $request)
    {
        try {
            $success = false;
            $message = 'No se pude procesar su solicitud';
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'EL CAMPO username ES REQUERIDO',
                'username.string' => 'EL CAMPO username DEBE DE SER UNA CADENA VALIDA',
                'password.required' => 'EL CAMPO password ES REQUERIDA',
                'password.string' => 'EL CAMPO password DEBE DE SER UNA CADENA VALIDA',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 500);
            }
            $data = $request->all();
            $data['ip'] = $request->ip();
            $access = new AccessRequest();
            $access->authenticateSu($data, $request);
            $user = User::whereUsername($data['username'])->firstOrFail();
            $token = $user->createToken('drugstore_token')->accessToken;
            return response()->json([
                'success' => true,
                'authenticated' => true,
                'user' => $this->getAccountInfo($user->id),
                'session' => (object)[
                    'token' => $token,
                    'hash' => encrypt($user->id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->getMessage()
            ], 500);
        }
    }


    public function logout(): JsonResponse
    {
        $user = User::find(Auth::user()->id);

        $user->isLoggedIn = false;
        $user->save();

        Auth::guard('web')->logout();

        DB::table('oauth_access_tokens')
            ->where('user_id', Auth::user()->id)
            ->update(['revoked' => true]);

        return response()->json([
            'authenticated' => false,
            'message' => 'Su sesion se cerro correctamente',
        ]);
    }

    public function getUserInfo($userId): JsonResponse
    {
        try {
            if (Auth::user()->id !== decrypt($userId)) {

                Auth::guard('web')->logout();

                DB::table('oauth_access_tokens')
                    ->where('user_id', Auth::user()->id)
                    ->update(['revoked' => true]);

                return response()->json([
                    'authenticated' => false
                ], 401);
            }

            return response()->json([
                'user' => $this->getAccountInfo(decrypt($userId))
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error. Intenta de nuevo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function checkGroup($office)
    {
        $country = [];

        SreCatOffice::$withoutAppends = true;
        $country = SreCatGroupOfficesSreCatOffice::where('sre_cat_office_id', $office)->select('sre_cat_group_office_id')->get();
        if (!empty($country)) {
            return $country->pluck('sre_cat_group_office_id')->toArray();
        }
        return $country;


    }

    public static function checkConsulate($office_id)
    {
        $check = false;

        $array = [6, 5];// si es consulado o embajada

        $exists = SreCatOffice::where('id', $office_id)->whereIN('sre_cat_office_type_id', $array)->select('sre_cat_office_type_id', 'id')->first();
        if (!empty($exists)) {
            $check = true;
        }

        return $check;
    }

    public static function checkCountry($office)
    {
        $group = null;

        SreCatOffice::$withoutAppends = true;
        $group = SreCatOffice::where('id', $office)->select('geo_cat_country_id')->first();

        return $group && isset($group->geo_cat_country_id) ? $group->geo_cat_country_id : null;

    }

    public static function getAccountInfo($id)
    {
        return User::select(
            [
                'id',
                'name',
                'username',
                'cat_profile_id',
                'is_active'
            ]
        )
            ->with(['profile', 'roles'])
            ->find($id);
    }

    private static function getFieldIfExists($object, $field, $default = null)
    {
        return $object && isset($object->{$field}) ? $object->{$field} : $default;
    }

}
