<?php


use App\Models\Admin;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Notifications\NewNotification;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\NotificationDashboard;
use Illuminate\Support\Facades\Storage;

if (!function_exists('isArabic')) {
    function isArabic(): bool
    {
        return app()->getLocale() == "ar";
    }
}

if (!function_exists('getDirection')) {

    function getDirection()
    {
        return isArabic() ? "rtl" : 'ltr';
    }
}

if (!function_exists('isDarkMode')) {

    function isDarkMode(): bool
    {
        return session('theme_mode') === "dark";
    }
}

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return session($key, $default);
    }
}


if (!function_exists('uploadImageToDirectory')) {
    function uploadImageToDirectory($imageFile, $model = '')
    {
        $model     = Str::plural($model);
        $model     = Str::ucfirst($model);
        $path      = "/Images/$model";
        $imageName = str_replace(' ', '', 'lms_' . time() . "_" . random_int(10, 99) . $imageFile->getClientOriginalName());  // Set Image name
        $imageFile->storeAs($path, $imageName, 'public');
        return $imageName;
    }
}


if (!function_exists('adminTypeName')) {
    function adminTypeName($type)
    {
        return match ($type) {
            'consultant' => __('consultant'),
            'contractor' => __('contractor'),
            'amana' => __('amana'),
            default => __('unknown'),
        };
    }
}


if (!function_exists('getAttachmentPathFromDirectory')) {
    function getAttachmentPathFromDirectory($fileName = null, $directory = null)
    {
        $directory = Str::plural($directory);
        $directory = Str::ucfirst($directory);

        if ($fileName && $directory) {
            return asset("storage/$directory/$fileName");
        }

        return null;
    }
}

if (!function_exists('uploadAttachmentToDirectory')) {
    function uploadAttachmentToDirectory($file, $folder)
    {
        $folder = trim($folder, '/'); // clean up input
        $fileName = 'attachment_' . time() . '_' . preg_replace('/\s+/', '', $file->getClientOriginalName());
        $file->storeAs($folder, $fileName, 'public');

        return  $fileName;
    }
}

if (!function_exists('deleteAttachmentFromDirectory')) {
    function deleteAttachmentFromDirectory($attachment, $folder = 'missions/attachments')
    {
        $folder = trim($folder, '/');
        // Assuming the attachment contains only the path after the 'public' disk (i.e. 'missions/attachments/filename')
        $path = $attachment->file;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}



if (!function_exists('updateModelImage')) {

    function updateModelImage($model, $imageFile, $directory)
    {
        deleteImageFromDirectory($model->image, $directory);
        return uploadImageToDirectory($imageFile, $directory);
    }
}


// if (!function_exists('formatNumber')) {
//     function formatNumber($number)
//     {
//         if ($number >= 1000 && $number < 1000000) {
//             return number_format($number / 1000, 3);
//         } elseif ($number >= 1000000) {
//             return number_format($number / 1000000, 3);
//         } else {
//             return $number;
//         }
//     }
// }


if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        // آلاف
        if ($number >= 1_000 && $number < 1_000_000) {
            return number_format($number / 1_000, 3) . __("K");
        }
        // ملايين
        elseif ($number >= 1_000_000 && $number < 1_000_000_000) {
            return number_format($number / 1_000_000, 3) . __("M");
        }
        // مليارات (حتى 100 مليار)
        elseif ($number >= 1_000_000_000 && $number <= 100_000_000_000) {
            return number_format($number / 1_000_000_000, 3) . __("B");
        }
        // أقل من 1000 أو أكبر من 100 مليار
        else {
            return number_format($number, 2);
        }
    }
}


if (!function_exists('deleteImageFromDirectory')) {

    function deleteImageFromDirectory($imageName, $model)
    {
        $model = Str::plural($model);
        $model = Str::ucfirst($model);

        if ($imageName != 'default.png') {
            $path = "/Images/" . $model . '/' . $imageName;
            Storage::disk('public')->delete($path);
        }
    }
}

if (!function_exists('deleteImagesFromDirectory')) {

    function deleteImagesFromDirectory($imageNames, $model)
    {
        $model = Str::plural($model);
        $model = Str::ucfirst($model);
        foreach ($imageNames as $imageName) {
            if ($imageName != 'default.png') {
                $path = "/Images/" . $model . '/' . $imageName;
                Storage::disk('public')->delete($path);
            }
        }
    }
}


if (!function_exists('getImagePathFromDirectory')) {

    function getImagePathFromDirectory($imageName = null, $directory = null, $defaultImage = 'default.svg')
    {
        $directory = Str::plural($directory);
        $directory = Str::ucfirst($directory);

        $imagePath         = "/storage/Images/$directory/$imageName";
        $callbackImagePath = "placeholder_images/$directory/$defaultImage";
        if ($imageName && $directory && file_exists(public_path($imagePath)))
            return asset($imagePath);
        else if (file_exists($callbackImagePath))
            return asset($callbackImagePath);
        else
            return asset("/placeholder_images/$defaultImage");
    }
}

if (! function_exists('isActiveRoute')) {
    function isActiveRoute($patterns, $class = 'active')
    {
        foreach ((array) $patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return $class;
            }
        }
        return '';
    }
}

if (!function_exists('isTabActive')) {

    function isTabActive($path)
    {
        if (request()->routeIs($path))
            return 'active';
    }
}

if (!function_exists('isTabHere')) {

    function isTabHere($path)
    {
        if (request()->routeIs($path))
            return 'here';
    }
}

if (!function_exists('isTabBold')) {

    function isTabBold($path)
    {
        if (request()->routeIs($path))
            return 'font-weight: 800';
    }
}
if (!function_exists('isTabActiveBg')) {
    /**
     * Return active background style if current route matches.
     *
     * @param string|array $path
     * @param string $bgColor
     * @return string|null
     */
    function isTabActiveBg($path, $bgColor = '#E5F5FF')
    {
        // لو $path مصفوفة، نتحقق من أي عنصر فيها
        if (is_array($path)) {
            foreach ($path as $p) {
                if (request()->routeIs($p)) {
                    return "background-color: {$bgColor};";
                }
            }
        } else {
            if (request()->routeIs($path)) {
                return "background-color: {$bgColor};";
            }
        }

        return null;
    }
}

if (!function_exists('isTabOpen')) {

    function isTabOpen($path)
    {

        if (request()->segment(2) === $path)
            return 'menu-item-open';
    }
}


if (!function_exists('getClassIfUrlContains')) {
    function getClassIfUrlContains($class, $word)
    {

        if ($word == "/" && count(request()->segments()) == 1)
            return $class;

        return in_array($word, request()->segments()) ? $class : '';
    }
}


if (!function_exists('notifyAdmin')) {
    function notifyAdmin($adminId, $title, $message, $type = 'info', $link = null)
    {
        try {
            return \App\Models\Notification::create([
                'admin_id' => $adminId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => $link,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('notifyStudentsByGrade')) {
    // إشعار جماعي لطلاب صف معين + أولياء أمورهم
    function notifyStudentsByGrade($grade, $title, $message, $type = 'info', $link = null)
    {
        $students = \App\Models\Admin::where('type', 'student')->where('grade', $grade)->get();
        foreach ($students as $student) {
            notifyAdmin($student->id, $title, $message, $type, $link);
            foreach ($student->parents as $parent) {
                notifyAdmin($parent->id, $title, $message, $type, $link);
            }
        }
    }
}

if (!function_exists('brand')) {
    // الهوية البصرية: لو الداخل مدرس وعنده هوية خاصة → المشروع كله باسمه
    // غير كده: الهوية العامة من settings
    function brand($key, $default = null)
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $cache = \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $cache = [];
            }
        }
        try {
            $me = auth('admin')->user();
            if ($me && $me->type === 'teacher') {
                $mine = [
                    'site_name' => $me->brand_name ?: $me->name,
                    'primary_color' => $me->brand_primary,
                    'secondary_color' => $me->brand_secondary,
                    'logo' => $me->brand_logo,
                ];
                if (! empty($mine[$key])) {
                    return $mine[$key];
                }
                // لو المدرس مش مظبط لون → يقع على العام
            }
        } catch (\Throwable $e) {
        }
        $defaults = [
            'site_name' => 'منصة نوران التعليمية',
            'primary_color' => '#1b84ff',
            'secondary_color' => '#17c653',
            'logo' => 'assets/logo/lms-logo-letter-design-initials-linked-circle-uppercase-monogram-typography-technology-business-real-estate-brand-393870301.webp',
        ];
        return $cache[$key] ?? $defaults[$key] ?? $default;
    }
}

if (!function_exists('abilities')) {
    function abilities()
    {
        if (is_null(cache()->get('abilities'))) {
            $abilities = Cache::remember('abilities', .1, function () {
                return auth('admin')->user()->abilities();
            });
        } else {
            $abilities = cache()->get('abilities');
        }


        return $abilities;
    }
}

if (!function_exists('getFullPathOfImagesFromDirectory')) {
    function getFullPathOfImagesFromDirectory($images, $directory)
    {
        $updatedImages = [];
        foreach ($images as $image) {
            array_push($updatedImages, getImagePathFromDirectory($image, $directory));
        }

        return $updatedImages;
    }
}

if (!function_exists('getRelationWithColumns')) {

    // function getRelationWithColumns($relations): array
    // {
    //     $relationsWithColumns = [];

    //     foreach ($relations as $relation => $columns) {
    //         array_push($relationsWithColumns, $relation . ":" . implode(",", $columns));
    //     }

    //     return $relationsWithColumns;
    // }

    function getRelationWithColumns($relations): array
    {
        $out = [];

        foreach ($relations as $relation => $value) {
            // Support numeric keys like ['mission', 'user']
            if (is_int($relation) && is_string($value)) {
                $out[] = $value;
                continue;
            }

            // Relation with closure constraint
            if ($value instanceof Closure) {
                $out[$relation] = $value; // keep as keyed closure for with()
                continue;
            }

            // Relation with columns array or single string
            if (is_array($value)) {
                $out[] = $relation . ':' . implode(',', $value);
                continue;
            }

            if (is_string($value)) {
                $out[] = $relation . ':' . $value;
                continue;
            }
        }

        return $out;
    }
}

if (!function_exists('getDateRangeArray')) { // takes 'Y-m-d - Y-m-d' and returns [ Y-m-d 00:00:00 , Y-m-d 23:59:59 ]

    function getDateRangeArray($dateRange): array
    {
        $dateRange = explode(' - ', $dateRange);

        return [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'];
    }
}

if (!function_exists('sanitizeNumber')) {
    /**
     * Remove commas from numeric inputs.
     */
    function sanitizeNumber($value)
    {
        return $value !== null ? str_replace(',', '', $value) : null;
    }
}

if (!function_exists('getModelData')) {

    function getModelData(Model $model, $relations = [], $orsFilters = [], $andsFilters = [], $searchingColumns = null, $onlyTrashed = false): array
    {

        $columns              = $searchingColumns ?? $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
        $relationsWithColumns = getRelationWithColumns($relations); // this fn takes [ brand => [ id , name ] ] then returns : brand:id,name to use it in with clause

        /** Get the request parameters **/
        $params = request()->all();

        // set passed filters from controller if exist
        if (!$onlyTrashed)
            $model = $model->query()->with($relationsWithColumns);
        else
            $model = $model->query()->onlyTrashed()->with($relationsWithColumns);


        /** Get the count before search **/
        $itemsBeforeSearch = $model->count();

        // general search
        if (isset($params['search']['value'])) {

            if (str_starts_with($params['search']['value'], '0'))
                $params['search']['value'] = substr($params['search']['value'], 1);

            /** search in the original table **/
            foreach ($columns as $column)
                array_push($orsFilters, [$column, 'LIKE', "%" . $params['search']['value'] . "%"]);
        }

        // filter search
        if ($itemsBeforeSearch == $model->count()) {

            $searchingKeys = collect($params['columns'])->transform(function ($entry) {

                return $entry['search']['value'] != null && $entry['search']['value'] != 'all' ? Arr::only($entry, ['data', 'name', 'search']) : null; // return just columns which have search values

            })->whereNotNull()->values();


            /** if request has filters like status **/
            if ($searchingKeys->count() > 0) {

                /** search in the original table **/
                foreach ($searchingKeys as $column) {
                    if (!($column['name'] == 'created_at' or $column['name'] == 'date'))
                        array_push($andsFilters, [$column['name'], '=', $column['search']['value']]);
                    else {
                        if (!str_contains($column['search']['value'], ' - ')) // if date isn't range ( single date )
                            $model->orWhereDate($column['name'], $column['search']['value']);
                        else
                            $model->orWhereBetween($column['name'], getDateRangeArray($column['search']['value']));
                    }
                }
            }
        }

        $model = $model->where(function ($query) use ($orsFilters) {
            foreach ($orsFilters as $filter)
                $query->orWhere([$filter]);
        });

        if ($andsFilters)
            $model->where($andsFilters);

        if (isset($params['order'][0])) {
            $model->orderBy($params['columns'][$params['order'][0]['column']]['data'], $params['order'][0]['dir']);
        }

        $response = [
            "recordsTotal" => $model->count(),
            "recordsFiltered" => $model->count(),
            'data' => $model->skip($params['start'])->take($params['length'])->get()
        ];

        return $response;
    }
}

/**
 * push firebase notification .
 * Author : Khaled
 * created By Khaled @ 15-06-2021
 */
// if (!function_exists('storeAndPushNotification')) {
//     function storeAndPushNotification($title, $description, $icon, $color, $url)
//     {
//         /** add notification to first Admin **/
//         $date         = \Carbon\Carbon::now()->diffForHumans();
//         $notification = new NewNotification($title, $description, $date, $icon, $color, $url);
//         $admin        = Admin::first();
//         $admin->notify($notification);

//         /** push notifications to all admins **/
//         $firebaseToken  = Admin::whereNotNull('device_token')->pluck('device_token')->all();
//         $SERVER_API_KEY = "AAAAdaClkUc:APA91bFvu0zGXOeq2_lBLwoHBUGH37Bdk_zBjh8Dg__55nmsWRtwk_njpEzCLc29Ik5S2KHW34vxNiQb2RcihiEJXZVPh8A3FPawZPxVH_MDf06x06VzBPXEt7iKhTur4tbzzg_RD_8H";

//         $data = [
//             "registration_ids" => $firebaseToken,
//             "notification" => [
//                 "alert_title" => $title,
//                 "title" => $title,
//                 "description" => $description,
//                 "body" => $description,
//                 "date" => $date,
//                 "alert_icon" => $icon,
//                 "icon" => asset(getImagePathFromDirectory(setting('fav_icon'), 'Settings')),
//                 "icon_color" => $color,
//                 "url" => $url,
//                 "id" => $admin->notifications->last()->id,
//             ],
//             "webpush" => [
//                 "fcm_options" => [
//                     "link" => $url
//                 ]
//             ]
//         ];

//         $response = Http::withHeaders([
//             "Authorization" => "key=$SERVER_API_KEY",
//         ])->post('https://fcm.googleapis.com/fcm/send', $data);

//         return $response;
//     }
// }
function base64UrlEncode($data)
{
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function generateAccessToken($serviceAccountFilePath)
{
    $serviceAccount = json_decode(file_get_contents($serviceAccountFilePath), true);

    $jwtHeader = [
        'alg' => 'RS256',
        'typ' => 'JWT',
    ];

    $now = time();
    $jwtClaim = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600,
    ];

    // Encode JWT Header and Claim
    $jwtHeaderEncoded = base64UrlEncode(json_encode($jwtHeader));
    $jwtClaimEncoded = base64UrlEncode(json_encode($jwtClaim));

    // Create the unsigned JWT
    $unsignedJwt = $jwtHeaderEncoded . '.' . $jwtClaimEncoded;

    // Sign the JWT using the private key from the service account
    $signature = '';
    openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], 'sha256');

    $signedJwt = $unsignedJwt . '.' . base64UrlEncode($signature);

    // Make a request to the Google OAuth 2.0 token endpoint to get the access token
    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $signedJwt,
    ]);
    if ($response->successful()) {
        return $response->json()['access_token'];
    }

    throw new \Exception('Failed to generate access token: ' . $response->body());
}
if (!function_exists('sendFirebaseNotification')) {
    function sendFirebaseNotification($notificationBody, $type, $token = null)
    {
        $serviceAccountFilePath = storage_path('app/private/mission-firebase.json');
        $accessToken = generateAccessToken($serviceAccountFilePath);
        $data = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => 'Mission',
                    "body" => $notificationBody,
                    // "sound" => "default"
                ],
                'data' => [
                    "action" => $type
                ],
            ]
        ];

        Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ])->post('https://fcm.googleapis.com/v1/projects/mission-78733/messages:send', $data);
    }
}

if (!function_exists('storeAndPushNotificationAdmin')) {

    function storeAndPushNotificationAdmin($titleAr, $titleEn, $descriptionAr, $descriptionEn, $icon, $color, $url, $ability = null)
    {
        /** add notification to first Admin **/
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $notification = new NotificationDashboard($titleAr, $titleEn, $descriptionAr, $descriptionEn, $date, $icon, $color, $url);
        if ($ability) {
            $admins       = Admin::whereHas('roles.abilities', function ($query) use ($ability) {
                $query->where('category', $ability);
            })->get();
        } else {
            $admins       = Admin::whereHas('roles', function ($query) {
                $query->where('id', 1);
            })->get();
        }
        Notification::send($admins, $notification);
        // foreach ($admins as $admin) {
        //     $admin->notify($notification);
        // }

        /** push notifications to all admins **/
        $firebaseToken  = Admin::whereNotNull('device_token')->pluck('device_token')->all();
        $SERVER_API_KEY = "AAAAbJigBxA:APA91bGEFXY9LQAOXAxOQGWaujUO_Wbm6zFf0794ROrbzf3ebjKr-l4uS6MnQIrRe4q4hTTJJx6DjR8kSkXTHHp86iXfiwI_FezdpznCLBlhJMUaMtCqE8rIC3nPv_UHarHqpVw7OpnQ";

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "alert_title" => app()->isLocale('ar') ? $titleAr : $titleEn,
                "title_ar" => $titleAr,
                "title_en" => $titleEn,
                "description_ar" => $descriptionAr,
                "description_en" => $descriptionEn,
                "date" => $date,
                "alert_icon" => $icon,
                "icon" => asset(getImagePathFromDirectory(setting('fav_icon'), 'Settings')),
                "icon_color" => $color,
                "url" => $url,
                // "id" => $admin->notifications->last()->id,
            ],
            "webpush" => [
                "fcm_options" => [
                    "link" => $url
                ]
            ]
        ];

        $response = Http::withHeaders([
            "Authorization" => "key=$SERVER_API_KEY",
        ])->post('https://fcm.googleapis.com/fcm/send', $data);

        return $response;
    }
}



if (!function_exists('storeAndPushNotificationUser')) {

    function storeAndPushNotificationUser(
        $titleAr,
        $titleEn,
        $descriptionAr,
        $descriptionEn,
        $icon,
        $color,
        $url,
        $userId = null,
        $role = null
    ) {
        $date = \Carbon\Carbon::now()->diffForHumans();

        $notification = new \App\Notifications\NotificationDashboard(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $date,
            $icon,
            $color,
            $url
        );


        if (!is_null($userId)) {

            // لو رقم واحد نخليه array
            if (is_int($userId)) {
                $userId = [$userId];
            }

            // لو array نجيبهم كلهم
            $admins = \App\Models\Admin::whereIn('id', $userId)->get();

        } elseif (!is_null($role)) {

            $admins = \App\Models\Admin::where('type', $role)->get();

        } else {

            // default
            $admins = \App\Models\Admin::where('id', 1)->get();
        }

        // إرسال إشعار Laravel
        \Illuminate\Support\Facades\Notification::send($admins, $notification);

        // -------------------------
        // FCM
        // -------------------------

        $firebaseTokens = $admins->pluck('device_token')->filter()->all();

        if (!empty($firebaseTokens)) {

            $SERVER_API_KEY = "AAAAbJigBxA:APA91bGEFXY9LQAOXAxOQGWaujUO_Wbm6zFf0794ROrbzf3ebjKr-l4uS6MnQIrRe4q4hTTJJx6DjR8kSkXTHHp86iXfiwI_FezdpznCLBlhJMUaMtCqE8rIC3nPv_UHarHqpVw7OpnQ";

            $data = [
                "registration_ids" => $firebaseTokens,
                "notification" => [
                    "alert_title"     => app()->isLocale('ar') ? $titleAr : $titleEn,
                    "title_ar"        => $titleAr,
                    "title_en"        => $titleEn,
                    "description_ar"  => $descriptionAr,
                    "description_en"  => $descriptionEn,
                    "date"            => $date,
                    "alert_icon"      => $icon,
                    "icon"            => asset(getImagePathFromDirectory(setting('fav_icon'), 'Settings')),
                    "icon_color"      => $color,
                    "url"             => $url,
                ],
                "webpush" => [
                    "fcm_options" => [
                        "link" => $url
                    ]
                ]
            ];

            return \Illuminate\Support\Facades\Http::withHeaders([
                "Authorization" => "key=$SERVER_API_KEY",
            ])->post('https://fcm.googleapis.com/fcm/send', $data);
        }

        return null;
    }
}



if (!function_exists('generateRandomCode')) {
    function generateRandomCode($length)
    {
        $allCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
        }

        return $code;
    }
}

if (!function_exists('currentCurrency')) {
    function currentCurrency()
    {
        return session('currency_code') ?? config('app.currency_code');
    }
}

if (!function_exists('getCurrencyTransferAmount')) {
    function getCurrencyTransferAmount()
    {
        $currency_name   = Cache::get('currency_name');
        $currency_amount = Cache::get('currency_amount');

        if ($currency_name != currentCurrency()) {
            cache()->forget('currency_name');
            cache()->forget('currency_amount');

            // Fetching JSON
            $req_url       = 'https://api.exchangerate-api.com/v4/latest/USD';
            $response_json = file_get_contents($req_url);

            if (false !== $response_json) {

                // Try/catch for json_decode operation
                try {

                    // Decoding
                    $response_object = json_decode($response_json);

                    // YOUR APPLICATION CODE HERE, e.g.
                    $currency               = currentCurrency();
                    $currencyTransferAmount = round(($response_object->rates->$currency), 2);
                    Cache::put('currency_name', $currency);
                    Cache::put('currency_amount', $currencyTransferAmount);

                    return $currencyTransferAmount;
                } catch (Exception $e) {
                    // Handle JSON parse error...
                }
            }
        }

        return $currency_amount;
    }
}

if (!function_exists('priceAfterTransfer')) {
    function priceAfterTransfer($price)
    {
        $base_price  = $price;
        $final_price = round(($base_price * getCurrencyTransferAmount()), 2);

        return $final_price;
    }
}

if (!function_exists('checkIfProviderAllowQuantity')) {
    function checkIfProviderAllowQuantity($provider, $type)
    {
        if ($provider == 'unipin' && $type == 'voucher') {
            return true;
        }

        return false;
    }
}

if (!function_exists('getCommissionTypeLabel')) {
    function getCommissionTypeLabel(int|string|null $value): string
    {
        return match ((int) $value) {
            1 => 'percentage',
            2 => 'fixed',
        };
    }
}


if (!function_exists('getAvatarInitial')) {
    function getAvatarInitial($name)
    {
        // Extract the first character
        $initial = mb_substr($name, 0, 1, 'UTF-8');
        // Detect if it's Arabic
        $isArabic = preg_match('/\p{Arabic}/u', $initial);

        // Convert to uppercase only if not Arabic
        if (!$isArabic) {
            $initial = mb_strtoupper($initial, 'UTF-8');
        }
        return ($initial);
    }
}

if (!function_exists('allowedGrades')) {
    // الصفوف المسموح للمستخدم رؤيتها: الطالب صفه فقط، ولي الأمر صفوف أبنائه، والباقي الكل (null)
    function allowedGrades($user = null): ?array
    {
        $user = $user ?: auth('admin')->user();
        if (! $user) {
            return [];
        }
        if ($user->type === 'student') {
            return $user->grade ? [$user->grade] : [];
        }
        if ($user->type === 'parent') {
            return $user->students()->pluck('admins.grade')->filter()->unique()->values()->all();
        }

        return null;
    }
}

if (!function_exists('youtubeId')) {
    // يستخرج ID اليوتيوب (11 حرف) من أي صيغة رابط — أو null لو الرابط غير صالح
    function youtubeId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        $patterns = [
            '/youtube\.com\/watch\?.*v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/youtube(?:-nocookie)?\.com\/embed\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/live\/([A-Za-z0-9_-]{11})/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }
}

if (!function_exists('youtubeEmbed')) {
    function youtubeEmbed(?string $url): ?string
    {
        $id = youtubeId($url);

        return $id ? 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0' : null;
    }
}
