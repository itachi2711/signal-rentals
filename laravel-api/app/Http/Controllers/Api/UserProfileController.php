<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/28/2021
 * Time: 8:31 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Requests\UserProfileRequest;
use App\Http\Resources\UserResource;
use App\Rental\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends ApiController
{
    /**
     * @var UserInterface
     */
    protected $userRepository, $loginProxy;

    /**
     * UserProfileController constructor.
     * @param UserInterface $userInterface
     * @param LoginProxy $loginProxy
     */
    public function __construct(UserInterface $userInterface, LoginProxy $loginProxy)
    {
        $this->userRepository = $userInterface;
        $this->loginProxy = $loginProxy;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user = $this->userRepository->getById($user->id);

        if (!$user) {
            return $this->respondNotFound('User not found.');
        }
        return $this->respondWithData(new UserResource($user));
    }

    /**
     * @param UserProfileRequest $request
     * @param $uuid
     * @return array|mixed|void
     */
    public function update(UserProfileRequest $request, $uuid)
    {
        $user = Auth::user();
        if (!isset($user) || $user->id != $uuid) {
            $this->loginProxy->logout();
            return;
        }
        $doNotUpdate = [
            'role' => 1,
            'role_id' => 1,
            'confirmed' => 1
        ];
        $data = array_diff_key($request->all(), $doNotUpdate);
        $save = $this->userRepository->update(array_filter($data), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! User has been updated.');
        }
    }

    /**
     * @param Request $request
     */
    public function uploadPhoto(Request $request)
    {
        $data = $request->all();
        // Upload logo
        if ($request->hasFile('photo')) {
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('photo')->storeAs('profile_photos', $fileNameToStore);
            $data['photo'] = $fileNameToStore;
        }
        // also, delete previous image file from server
        $this->userRepository->update(array_filter($data), $data['id']);
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function fetchPhoto(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();

        $file_path = $user->photo;
        if (array_key_exists('file_path', $data) && $file_path == null) {
            $file_path = $data['file_path'];
        }
        $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR . 'profile_photos' . DIRECTORY_SEPARATOR . $file_path;
        return response()->file($local_path);
    }
}
