<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GuzzleHttpRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class ApiController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['new']]);
    }

    public function new(Request $request)
    {
        $validator = $this->validation_request($request);

        if ($validator->fails()) {
            $response = ["ok" => false, "messages" => $validator->messages()];
        } else {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            if ($user->save()) {

                $data = [
                    "id" => $user->id,
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => bcrypt($request->password),
                    "address" => $request->address,
                    "phone" => $request->phone,
                    "profession" => $request->profession
                ];

                $response = (GuzzleHttpRequest::GuzzleHttpRequest(env('MOCKAPI_URL'), "POST", $data)) ?
                    ["message" => "User saved successfully."] :
                    ["message" => "Failed to save user"];
            } else {

                $response = ["message" => "Failed to save user"];
            }
        }
        return response()->json($response);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = JWTAuth::user();

        if ($user) {

            $data = GuzzleHttpRequest::GuzzleHttpRequest(env('MOCKAPI_URL') . "/{$user->id}", "GET", []);
            $response = ($data) ? $data :
                ["message" => "User information not found."];
        } else {
            $response = ["message" => "User not found."];
        }

        return response()->json($response);
    }

    public function update_me()
    {
        $user = JWTAuth::user();

        return response()->json(["ok" => true, "update" => true]);
    }

    public function delete_me()
    {
        $user = JWTAuth::user();

        return response()->json(["ok" => true, "delete" => true]);
    }


    private function validation_request($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:50',
            'password' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'profession' => 'required|string'
        ]);

        return $validator;
    }
}
