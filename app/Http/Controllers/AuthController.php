<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Info(title="My First API", version="0.1")
 */
class AuthController extends Controller
{


    /**
     * @SWG\Post(
     *      path="/api/register",
     *      operationId="Register testing",
     *      summary="Add User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="name",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *          @SWG\Response(
     *          response=200,
     *          description="Example extended response",
     *       ),
     *     )
     */
    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * @SWG\Post(
     *      path="/api/login",
     *      operationId="Login testing",
     *      summary="Login User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *          @SWG\Response(
     *          response=200,
     *          description="Example extended response",
     *       ),
     *     )
     */
    public function login(LoginRequest $request)
    {
        try{
            if( Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) ) {
                $user = Auth::user();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                if ($request->remember_me){
                    $token->expires_at = Carbon::now()->addHours(1);
                    $token->save();
                }
                return response()->json([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ]);
            }
        } catch (\Exception $e){
            return response()->json(['status' => FALSE, 'error' => $e->getMessage()], 500);
        }

    }

    /**
     * @SWG\Get(
     *   path="/api/logout",
     *   summary="Logout Testing",
     *   operationId="testing",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
    type="string",
    name="Authorization",
    in="header",
    required=true)
     * )
     *
     */
    public function logout(Request $request)
    {
        try{
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e){
            return response()->json(['status' => FALSE,'error' =>$e->getMessage()],500);
        }
    }
}
