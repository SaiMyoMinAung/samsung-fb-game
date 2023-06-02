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

    public function faceboolLogin()
    {
        return view('facebook-login');
    }

    public function samsungTv(Request $request)
    {
        $share = new Share();
        $facebookShareUrl = $share->page(
            route('samsung-tv', ['id' => $request->id]),
            'Samung TV',
        )->facebook()->getRawLinks();

        $facebook_id = $request->id;

        $imageUrl = null;
        $textData = null;
        $base64 = null;
        $gameUsedUser = GameUsedUser::where('facebook_id', $facebook_id)->first();

        if ($gameUsedUser) {
            $imageUrl = url('/samsung_tv_photos/' . $facebook_id . '.jpg');
            $textData = json_decode($gameUsedUser->text_data, true);

            $imagePath = public_path('/samsung_tv_photos/' . $facebook_id . '.jpg');
            $type = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $imageUrl = url('/samsung_support_photos/default.jpg');
        }

        return view('samsung-tv', [
            'facebookShareUrl' => $facebookShareUrl,
            'imageUrl' => $imageUrl,
            'textData' => $textData,
            'base64' => $base64
        ]);
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

                $gameUsedUser = GameUsedUser::updateOrCreate(['email' => $user->email], [
                    'name' => $user->name,
                    'facebook_id' => $user->id,
                    'avatar' => $user->avatar
                ]);

                // return redirect()->intended('dashboard');
            }

            // get profile square photo
            $avatarContents = file_get_contents($user->getAvatar());

            // get orginal photo
            // https://developers.facebook.com/docs/graph-api/reference/user/picture
            // $fileContents = file_get_contents($user->avatar_original . "&access_token=" . $user->token);

            // get orignal photo with custom size
            // $fileContents = file_get_contents($user->avatar_original . "&width=500&height=500&large&access_token=" . $user->token);

            // save photo to public folder
            // File::put(public_path() . '/user_profiles/' . $user->getId() . ".jpg", $avatarContents);


            //To show picture 
            $picture = public_path('user_profiles/' . $user->getId() . ".jpg");

            // create new image instance
            $avatarImage = $imageManager->make($avatarContents);
            $avatarMask = $imageManager->make(public_path('samsung_support_photos/mask.png'));

            // fit
            $avatarImage->fit(220, 220);
            $avatarImage->mask($avatarMask, false);
            $avatarImage->save($picture);
            // $avatarImage->composite
            // crop
            // $tvImage->crop(1024, 960);

            $backgroundImage = $imageManager->make(public_path('samsung_support_photos/TV.jpg'));

            $backgroundImage->insert($avatarImage, 'top-left', 250, 440);
            // $backgroundImage->insert($avatarImage, 'top-left', 0, 0);

            $detector = new ZawgyiDetector();

            $score1 = $detector->getZawgyiProbability($user->name);

            // // score is 0.0 (The input is definitely Unicode).
            // // score is 1.0 (The input is definitely Zawgyi)
            if ($score1 == 0.0) {
                $profileName = Rabbit::uni2zg($user->name);
            } else {
                $profileName = $user->name;
            }

            $firstTitle = $profileName . ' က';
            $secondTitle = ' Neo QLED 8K လေးပါ';
            $allFunnyText = [
                [
                    '. အေကာင္းဆုံးအရာေတြကို ပိုင္ဆိုင္ခ်င္သူေလးပါ။'
                ],
                [
                    '. အလွပဆုံးေနတတ္ၾကတယ္။'
                ],
                [
                    '. အေကာင္းဆုံးအရာေတြကို ပိုင္ဆိုင္ခ်င္သူေလးပါ။'
                ],
                [
                    '. အလွပဆုံးေနတတ္ၾကတယ္။'
                ]
            ];
            $random_keys = array_rand($allFunnyText, 3);
            $funnyText = [
                $allFunnyText[$random_keys[0]],
                $allFunnyText[$random_keys[1]],
                $allFunnyText[$random_keys[2]],
            ];

            $gameUsedUser->update([
                'text_data' => json_encode([
                    'first_title' => $firstTitle,
                    'second_title' => $secondTitle,
                    'funny_text' => Arr::flatten($funnyText)
                ])
            ]);

            $backgroundImage->text($firstTitle, 900, 400, function ($font) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color('#fafbfc');
                $font->size(40);
            });

            // $backgroundImage->text($secondTitle, 950, 450, function ($font) {
            //     $font->file(public_path('Zawgyi-One.ttf'));
            //     $font->color('#09c9eb');
            //     $font->size(40);
            // });

            $x = 800;
            $y = 550;

            foreach (Arr::flatten($funnyText) as $eachText) {
                $y += 50;
                $backgroundImage->text($eachText, $x, $y, function ($font) {
                    $font->file(public_path('Zawgyi-One.ttf'));
                    $font->color('#fafbfc');
                    $font->size(30);
                });
            }

            $backgroundImage->save(public_path() . '/samsung_tv_photos/' . $user->getId() . ".jpg");

            return redirect(route('samsung-tv', ['id' => $user->getId()]));
        } catch (Exception $e) {
            report($e->getMessage());

            return redirect(route('samsung-tv'));
        }
    }
}
