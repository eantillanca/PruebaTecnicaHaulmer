<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GuzzleHttpRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
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
            $response = ["messages" => $validator->messages()];
        } else {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            if ($user->save()) {

                $data = [
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => bcrypt($request->password),
                    "address" => $request->address,
                    "phone" => $request->phone,
                    "profession" => $request->profession
                ];

                try {

                    $created = GuzzleHttpRequest::GuzzleHttpRequest(env('MOCKAPI_URL'), "POST", $data);
                    $user->mockapi_id = $created->id;
                    $user->save();
                    $response = ["message" => "User saved successfully."];
                } catch (Exception $e) {
                    $response = ["message" => "Failed to save user."];
                }
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

            $data = GuzzleHttpRequest::GuzzleHttpRequest(env('MOCKAPI_URL') . "/{$user->mockapi_id}", "GET", []);
            $response = ($data) ? $data :
                ["message" => "User information not found."];
        } else {
            $response = ["message" => "User not found."];
        }

        return response()->json($response);
    }

    public function update_me(Request $request)
    {
        $user_id = (JWTAuth::user()) ? JWTAuth::user()->id : null;

        if (!$user_id) {
            return response()->json(["message" => "User not found."]);
        }

        $validator = $this->validation_request($request);

        if ($validator->fails()) {
            $response = ["messages" => $validator->messages()];
        } else {

            $user = User::find($user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            if ($user->save()) {

                $data = [
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => bcrypt($request->password),
                    "address" => $request->address,
                    "phone" => $request->phone,
                    "profession" => $request->profession
                ];

                $response = (GuzzleHttpRequest::GuzzleHttpRequest(env('MOCKAPI_URL') . "/{$user->mockapi_id}", "PUT", $data)) ?
                    ["message" => "User updated successfully."] :
                    ["message" => "Failed to update user."];
            } else {

                $response = ["message" => "Failed to update user."];
            }
        }
        return response()->json($response);
    }

    public function delete_me()
    {
        $user_id = (JWTAuth::user()) ? JWTAuth::user()->id : null;

        if (!$user_id) {
            return response()->json(["message" => "User not found."]);
        }

        $user = User::find($user_id);

        if ($user->delete()) {
            $response = (GuzzleHttpRequest::GuzzleHttpRequest(env('MOCKAPI_URL') . "/{$user->mockapi_id}", "delete", [])) ?
                ["message" => "User deleted successfully."] :
                ["message" => "Failed to delete user."];
        } else {
            $response = ["message" => "Failed to delete user."];
        }
        return response()->json($response);
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
