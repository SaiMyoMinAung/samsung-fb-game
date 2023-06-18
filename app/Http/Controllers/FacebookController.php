<?php

namespace App\Http\Controllers;

use Rabbit;
use Exception;
use Jorenvh\Share\Share;
use Illuminate\Support\Arr;
use App\Models\GameUsedUser;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Laravel\Socialite\Facades\Socialite;
use Googlei18n\MyanmarTools\ZawgyiDetector;

class FacebookController extends Controller
{
    public function policy()
    {
        return view('policy');
    }

    public function termsOfService()
    {
        return view('terms-of-service');
    }

    public function facebookLogin()
    {
        return view('facebook-login');
    }

    public function share(Request $request)
    {
        $photo = $request->id;

        if (!$photo) {
            return redirect('/');
        }

        $gameUsedUser = GameUsedUser::where('photo', $photo)->first();

        $share = new Share();

        $facebookShareUrl = $share->page(
            route('samsung-tv', ['id' => $photo]),
            'Samung TV',
        )->facebook()->getRawLinks();

        if (!$gameUsedUser) {
            return redirect('/');
        }

        $gameUsedUser->update([
            'shared' => 1
        ]);

        return redirect($facebookShareUrl);
    }

    public function samsungTv(Request $request)
    {
        try {
            $photo = $request->id;

            if (!$photo) {
                return redirect('/');
            }

            $share = new Share();

            $facebookShareUrl = $share->page(
                route('samsung-tv', ['id' => $photo]),
                'Samung TV',
            )->facebook()->getRawLinks();

            $imageUrl = null;
            $textData = null;
            $base64 = null;
            $gameUsedUser = GameUsedUser::where('photo', $photo)->first();

            if ($gameUsedUser) {
                $imageUrl = url('/samsung_tv_photos/' . $photo);
                $textData = json_decode($gameUsedUser->text_data, true);

                $imagePath = public_path('/samsung_tv_photos/' . $photo);
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);

                if (file_exists($imagePath)) {
                    $data = file_get_contents($imagePath);
                } else {
                    return redirect('/');
                }

                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                return view('samsung-tv', [
                    'facebookShareUrl' => $facebookShareUrl,
                    'imageUrl' => $imageUrl,
                    'textData' => $textData,
                    'base64' => $base64,
                    'gameUsedUser' => $gameUsedUser
                ]);
            }

            return redirect('/');
        } catch (Exception $e) {
            report($e);
            return redirect('/');
        }
    }

    public function deleteUserData(Request $request)
    {
        $facebook_id = $request->id;

        if ($facebook_id) {
            $gameUsedUser = GameUsedUser::where('facebook_id', $facebook_id)->first();
            if (file_exists(public_path('samsung_tv_photos/' . $gameUsedUser->facebook_id . '.jpg'))) {
                unlink(public_path('samsung_tv_photos/' . $gameUsedUser->facebook_id . '.jpg'));
                $gameUsedUser->delete();
            }
        }
        return redirect('/');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback(ImageManager $imageManager)
    {
        try {

            $user = Socialite::driver('facebook')->stateless()->user();

            $gameUsedUser = GameUsedUser::where('facebook_id', $user->id)->first();

            if (!$gameUsedUser) {

                $gameUsedUser = GameUsedUser::updateOrCreate(['facebook_id' => $user->id], [
                    'name' => $user->name,
                    'facebook_id' => $user->id,
                    'avatar' => $user->avatar,
                    'shared' => false
                ]);
            }

            // get profile square photo
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $avatarContents = file_get_contents($user->getAvatar(), false, stream_context_create($arrContextOptions));

            // create new image instance
            $avatarImage = $imageManager->make($avatarContents);
            $avatarMask = $imageManager->make(public_path('samsung_support_photos/mask.png'));

            // // fit
            $avatarImage->fit(255, 255);
            $avatarImage->mask($avatarMask, true);

            $data = config('samsung');
            $random = rand(0, count($data) - 1);
            $randomData = $data[$random];

            $backgroundImage = $imageManager->make(public_path($randomData['tv_location']));

            $backgroundImage->insert($avatarImage, 'top-left', 305, 535);

            $detector = new ZawgyiDetector();

            $score1 = $detector->getZawgyiProbability($user->name);

            // // score is 0.0 (The input is definitely Unicode).
            // // score is 1.0 (The input is definitely Zawgyi)
            if ($score1 == 0.0) {
                $profileName = Rabbit::uni2zg($user->name);
            } else {
                $profileName = $user->name;
            }

            $firstTitle = $profileName . ' ' . Rabbit::uni2zg($randomData['tv_first_title']);
            $secondTitle = Rabbit::uni2zg($randomData['tv_second_title']);
            $thirdTitle = Rabbit::uni2zg($randomData['tv_third_title']);

            $randomData['tv_first_title'] = $profileName . ' ' . $randomData['tv_first_title'];

            $imageName = $user->getId() . '-' . rand(1, 10000) . ".jpg";

            $gameUsedUser->update([
                'text_data' => json_encode($randomData),
                'photo' => $imageName
            ]);

            $backgroundImage->text($firstTitle, 950, 450, function ($font) use ($randomData) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color($randomData['text_color']);
                $font->size(38);
            });

            $backgroundImage->text($secondTitle, 950, 510, function ($font) use ($randomData) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color($randomData['text_color']);
                $font->size(38);
            });

            $backgroundImage->text($thirdTitle, 950, 570, function ($font) use ($randomData) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color($randomData['text_color']);
                $font->size(38);
            });

            $x = 950;
            $y = 600;

            foreach (Arr::flatten($randomData['tv_sub_title']) as $eachText) {
                $y += 60;
                $backgroundImage->text(Rabbit::uni2zg($eachText), $x, $y, function ($font) use ($randomData) {
                    $font->file(public_path('Zawgyi-One.ttf'));
                    $font->color($randomData['text_color']);
                    $font->size(38);
                });
            }

            $backgroundImage->save(public_path() . '/samsung_tv_photos/' . $imageName);

            return redirect(route('samsung-tv', ['id' => $imageName]));
        } catch (Exception $e) {
            report($e->getMessage());

            return redirect('/');
        }
    }
}
