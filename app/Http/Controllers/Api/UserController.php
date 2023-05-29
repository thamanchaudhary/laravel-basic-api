<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\HasApiTokens;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $query = User::select('email','name');
        // if($flag == 1)
        // {
        // }
        // elseif($flag == 0)
        // {
        // }
        // else
        // {
        //     return response()->json([
        //         'message'    =>   'nvalid parameter passed if can be either 1 or 0',
        //         status     => 0
        //     ])
        // }

        $users = User::all();
        if (count($users) > 0) {
            //user exitst
            $response = [
                'message' => count($users) . 'users found',
                'status' => 1,
                'data' => $users
            ];
            return response()->json($response, 200);
        } else {
            //does'nt exist

            $response = [
                'message' => count($users) . 'users found',
                'status' => 0,
                'data' => $users
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:5', 'confirmed'],
            'password_confirmation' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];
            //dd($data);
            DB::beginTransaction();;
            try {
                $user  = User::create($data);
                DB::commit();
            } catch (\Exception $e) {
                dd($e->getMessage());
                $user =  null;
            }
            if ($user != null) {
                return response()->json([
                    'message' => "User registered successfully"
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Internal Server error'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message'          => 'User not found',
                'status'           => 0,
            ];
        } else {
            $response = [
                'message'       => 'User found',
                'status'        => 1,
                'data'          =>  $user
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(
                [
                    'status'   =>  0,
                    'message'     => "User does not exists"
                ],
                400
            );
        } else {
            DB::beginTransaction();
            try {
                $user->name                  =  $request['name'];
                $user->email                 =  $request['email'];
                $user->contact              =   $request['contact'];
                $user->pincode               =  $request['pincode'];
                $user->address               =  $request['address'];
                $user->save();
                DB::commit();
            } catch (\Exception $err) {

                DB::rollBack();
                $user = null;
            }
            if (is_null($user)) {
                return response()->json(
                    [
                        'status'     => 0,
                        'message'    => 'Intenal Server Error',
                        'error'      => $err->getMessage(),
                    ],
                    500
                );
            } else {
                return response()->json(
                    [
                        'status'  => 1,
                        'message' => 'User data update Successfully',
                    ],
                    200
                );
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message'    => 'User doesn exits',
                'status'     => 0,
            ];
            $respCode    = 404;
        } else {
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                $response = [
                    'message'  => 'User deleted successfully',
                    'status'     => 1,
                ];
                $respCode    = 200;
            } catch (\Exception $err) {
                DB::rollBack();
                $response = [
                    'message'  => 'Internal Server error',
                    'status'     => 0,
                ];
                $respCode    = 500;
            }
        }
        return response()->json($response, $respCode);
    }

    //Change Password 
    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'status'    => 0,
                'message'     => 'User date does not exits'

            ], 400);
        } else {

            if ($user->password = $request['old_password']) {
                // dd($request['old_password']);
                if ($request['new_password'] == $request['confirm_password']) {
                    DB::beginTransaction();
                    try {
                        $user->password  =  $request['new_password'];
                        $user->save();
                        DB::commit();
                    } catch (\Exception $err) {
                        $user = null;
                        DB::rollBack();
                    }
                    if (is_null($user)) {
                        return response()->json(
                            [
                                'status'     => 0,
                                'message'    => 'Intenal Server Error',
                                'error'      => $err->getMessage(),
                            ],
                            500
                        );
                    } else {
                        return response()->json(
                            [
                                'status'  => 1,
                                'message' => 'Password update Successfully',
                            ],
                            200
                        );
                    }
                } else {
                    return response()->json([
                        'status'  =>   1,
                        'message'   =>  'New password & confirm password dows not match'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'  =>   1,
                    'message'   =>  'Old password does not match'
                ], 400);
            }
        }
    }

    //Auth Register
    public function register(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'name'     =>     'required',
            'email'    =>    ['required', 'email'],
            'password'  =>  ['min:8', 'confirmed']
        ]);

        $user =  User::create($validatedData);
        $token  =  $user->createToken("auth_token")->accessToken;
        return response()->json([
            'token'     =>    $token,
            'user'      =>    $user,
            'message'   =>    "user created successfully",
            'status'    => 1
        ]);
    }
    //Login function
    public function login(Request $request)
    {
        dd($request->all());
        $validatedData = $request->validate([
            'email'    =>    ['required', 'email'],
            'password'  =>   ['required']
        ]);

        $user = User::where(['email' =>  $validatedData['email'], 'password' => $validatedData['password']])->first();
        // dd($user);
    }
}
