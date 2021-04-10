<?php

namespace App\Http\Controllers\Api;

use App\Helpers\GuzzleHttpRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
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
                    ["ok" => true, "message" => "User saved successfully."] :
                    ["ok" => false, "message" => "Failed to save user"];
            } else {

                $response = ["ok" => false, "message" => "Failed to save user"];
            }
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
