<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\MembersModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class userController extends Controller
{
    // POST [name, email, password, password_confirmation]
    public function register(Request $request)
    {
        $input = $request->except("_token", "password_confirmation");
        $password = $input['password'];
        $input['password'] = Hash::make($input['password']);

        $create = User::create($input);

        if ($create) {
                return response()->json([
                    "success" => true,
                    "title" => "Başarılı",
                    "message" => "Kullanıcı Kayıt İşlemi Başarılı",
                    "data" => [
                        "id" => $create->id,
                        "name" => $create->name,
                        "email" => $create->email,
                    ]
                ], 201);

        } else {
            return response()->json([
                "success" => true,
                "title" => "Hata",
                "message" => "Kullanıcı kayıt isleminde hata oluştu",
            ],400);
        }
    }

    // POST [email, password]
    public function login(Request $request)
    {
        $input = $request->except("_token");

        $credentials = [
            "email" => $input['email'],
            "password" => $input['password'],
        ];

        $member = User::where("email",$credentials['email'])->first();

        if ($member && Hash::check($credentials['password'],$member->password)) {
            $token = $member->createToken("crm")->accessToken;

            return response()->json([
                "success" => true,
                "title" => "Başarılı",
                "message" => "Giriş İşlemi Başarılı",
                "data" => [
                    "id" => $member->id,
                    "name" => $member->name,
                    "email" => $member->email,
                    "access_token" => $token,
                    "token_type" => "Bearer"
                ]
            ], 200);

        } else {
            return response()->json([
                "success" => false,
                "title" => "Hata",
                "message" => "Kullanıcı Bilgileri Hatalı"
            ], 401);
        }
    }

    // GET [Auth: Token]
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            "success" => true,
            "message" => "Profil Bilgileri",
            "data" => $user
        ]);
    }

    public function update(Request $request)
    {
        $input = $request->except("_token");
        $user = $request->user();

        // email kontrolü
        if ($input['email']!=$user->email){
            $control = User::where("id","!=",$user->id)->where("email",$input['email'])->first();
            if ($control){
                return response()->json([
                    "success" => false,
                    "title" => "Hata",
                    "message" => "E-Mail Adresi Kullanımda"
                ],422);
            }
        }

        // şifre kontrolü
        $input['password'] = $user->password;
        if ($request->password!=""){
            $input['password'] = Hash::make($request->password);
        }

        $update = User::where("id",$user->id)->update($input);

        if ($update) {
            $token = $user->createToken("crm")->accessToken;

            return response()->json([
                "success" => true,
                "title" => "Başarılı",
                "message" => "İşlem başarılı",
                "isLoggedIn" => true,
                "data" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "access_token" => $token,
                    "token_type" => "Bearer"
                ]
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "title" => "Hata",
                "message" => "İşlem başarısız"
            ], 500);
        }
    }

    // GET [Auth: Token]
    public function check(Request $request)
    {
        if ($request->user()){
            $user = $request->user();
            $token = $user->createToken("crm")->accessToken;

            return response()->json([
                "success" => true,
                "isLoggedIn" => true,
                "data" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "access_token" => $token,
                    "token_type" => "Bearer"
                ]
            ],200);
        }else{
            return response()->json([
                "success" => false,
                "isLoggedIn" => false,
                "user" => null
            ],401);
        }
    }

    // GET [Auth: Token]
    public function logout(Request $request)
    {
        $user = $request->user();
        $token = $user->token();
        $token->revoke();

        return response()->json([
            "success" => true,
            "message" => "Çıkış İşlemi Başarılı",
            "token" => $token
        ]);
    }
}
