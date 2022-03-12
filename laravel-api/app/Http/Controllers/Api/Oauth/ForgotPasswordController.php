<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 06/11/2019
 * Time: 03:59
 */

namespace App\Http\Controllers\Api\Oauth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\CommunicationMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends ApiController
{
    /**
     * ForgotPasswordController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function __invoke(Request $request)
    {
      //
    }

    /**
     * @param ForgotPasswordRequest $request
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->all();
        $email = $data['email'];
        $user = User::where('email', $email)->first();

        if (!isset($user))
            $user = Landlord::where('email', $email)->first();

        if (!isset($user))
            $user = Tenant::where('email', $email)->first();

        if(isset($user)){
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => Str::random(20),
                'created_at' => Carbon::now()
            ]);
            $tokenData = DB::table('password_resets')
                ->where('email', $email)->first();

            if(isset($tokenData) && isset($user))
                CommunicationMessage::send(RESET_PASSWORD, $user, $tokenData);
        }
    }


    /**
     * @param ResetPasswordRequest $request
     * @return array
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->all();
        $password = $data['password'];
        $token = $data['token'];

        $tokenData = DB::table('password_resets')
            ->where('token', $token)->first();

        if (isset($tokenData)){
            $user = User::where('email', $tokenData->email)->first();
            if (!isset($user))
                $user = Landlord::where('email', $tokenData->email)->first();
            if (!isset($user))
                $user = Tenant::where('email', $tokenData->email)->first();

            if (isset($user)){
                $user->password = $password;
                $user->update();

                DB::table('password_resets')->where('email', $user->email)->delete();
                return $this->respondWithSuccess('Password Changed successfully');
            }
        }
    }
}
