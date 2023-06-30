<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalogs\CatProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            User::$withoutAppends = false;
            $users = User::select([
                'id',
                'username',
                'name',
                'cat_profile_id',
                'is_active',
            ])
                ->with([
                    'profile:id,name'
                ])
                ->searchUsers($data['search'])
                ->paginate($data['pagination']['rowsPerPage']);

            return response()->json([
                'success' => true,
                'users' => $users,
                'message' => ':)'
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $success = false;
            $message = '';
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'name' => 'required|string',
                'password' => 'required|string',
                'cat_profile_id' => 'required|integer'
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
            $finUser = User::searchUsers($data['username'])->first();
            if (!$finUser) {
                $newUser = new User();
                $newUser->fill($data);
                $newUser->password = Hash::make($data['password']);
                $newUser->save();
                DB::commit();
                $message = 'Usuario creado correctamente';
                $success = true;
            } else {
                $message = 'Ya existe el usuario.';
            }

            return response()->json([
                'success' => $success,
                'message' => $message
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find(decrypt($id));
            $user->is_active = false;
            $user->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Se desactivó correctamente'
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function activeUser(string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find(decrypt($id));
            $user->is_active = true;
            $user->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Se activó correctamente'
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function userCatalogs()
    {
        try {
            return response()->json([
                'success' => true,
                'profiles' => CatProfile::select([
                    'id',
                    'name'
                ])->get()
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
