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

    public function trySamsungTv(Request $request)
    {
        $share = new Share();
        $facebookShareUrl = $share->page(
            route('try-samsung-tv', ['id' => $request->id]),
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

        return view('try-samsung-tv', [
            'facebookShareUrl' => $facebookShareUrl,
            'imageUrl' => $imageUrl,
            'textData' => $textData,
            'base64' => $base64
        ]);
    }

    public function samsungTv(Request $request)
    {
        $share = new Share();
        $facebookShareUrl = $share->page(
            route('try-samsung-tv', ['id' => $request->id, 'tryButton' => 1]),
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
            }

            // get profile square photo
            $avatarContents = file_get_contents($user->getAvatar());

            // create new image instance
            $avatarImage = $imageManager->make($avatarContents);
            $avatarMask = $imageManager->make(public_path('samsung_support_photos/mask.png'));

            // fit
            $avatarImage->fit(220, 220);
            $avatarImage->mask($avatarMask, false);

            $data = config('samsung');
            $random = rand(0, count($data) - 1);
            $randomData = $data[$random];

            $backgroundImage = $imageManager->make(public_path($randomData['tv_location']));

            $backgroundImage->insert($avatarImage, 'top-left', 250, 440);

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

            $gameUsedUser->update([
                'text_data' => json_encode($randomData)
            ]);

            $backgroundImage->text($firstTitle, 800, 400, function ($font) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color('#fafbfc');
                $font->size(33);
            });

            $backgroundImage->text($secondTitle, 800, 450, function ($font) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color('#fafbfc');
                $font->size(33);
            });

            $backgroundImage->text($thirdTitle, 820, 500, function ($font) {
                $font->file(public_path('Zawgyi-One.ttf'));
                $font->color('#fafbfc');
                $font->size(33);
            });

            $x = 820;
            $y = 510;

            foreach (Arr::flatten($randomData['tv_sub_title']) as $eachText) {
                $y += 50;
                $backgroundImage->text(Rabbit::uni2zg($eachText), $x, $y, function ($font) {
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
