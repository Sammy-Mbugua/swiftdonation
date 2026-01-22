<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use App\Models\Vrm\Notify;
use App\Models\Vrm\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreatorProfileController extends Controller
{

    // PRIVATE VARIABLES
    private $Table = ''; // Table name will be pluralized

    private $ThemePath = ""; //Main Theme Path starting from resources/views/
    private $MainFolder = "projects"; //Main Folder Name (in prural) inside the resources/views/$ThemePath/pages
    private $SubFolder = "/profile"; //Sub Folder Name inside the resources/views/$ThemePath/pages/$MainFolder/
    private $Upload = ""; //Upload Folder Name inside the public/admin/media

    private $ParentRoute = ""; // Parent Route Name Eg. vrm/settings
    private $AllowedFile = null; //Set Default allowed file extension, remember you can pass this upon upload to override default allowed file type. jpg|jpeg|png|doc|docx|

    private $New = ''; // New
    private $Save = ''; // Add New
    private $Edit = ''; // Edit
    private $Update = '/account/profile/update'; // Update
    private $Delete = ''; // Delete
    private $Action = ''; // Multiple Entry Action


    private $HeaderName = ""; // (Optional) Name

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //

    }

    /**
     * Global Settings {loadSettings}
     * Method is private and not accessible via the web
     * Todo: This method Load all settings from database via the PreLoad Model:: getSettings()
     *
     * @param optional $view_name (string) Page Name (make sure to add $ThemePath/$MainFolder/$SubFolder/$page_name)
     *
     * @return \Illuminate\Http\Response
     */
    private function loadSettings($view_name = '')
    {
        // Load in Controller Settings from passedSettings method
        $passed = $this->passedSettings();

        //openLoad settings
        $settings = Setting::preLoad($view_name, $passed);

        // Return all settings
        return $settings;
    }

    /**
     * Custom Settings {passedSettings}
     * Method is private and not accessible via the web
     * Todo: This method Load all settings for this Controller only
     *
     * @param optional $addtionalData (array) any additional data to be passed on demand
     *
     * @return \Illuminate\Http\Response
     */
    private function passedSettings($addtional_data = [])
    {
        date_default_timezone_set('Africa/Nairobi'); //Time Zone
        $setting['dateTime'] = strtotime(date('Y-m-d, H:i:s')); //Current DateTime

        // Links
        $setting['links'] = (object)[
            'new' => $this->New,
            'save' => $this->Save,
            'edit' => $this->Edit,
            'update' => $this->Update,
            'delete' => $this->Delete,
            'manage' => $this->Action,
            'route' => $this->ParentRoute,
           
        ];

        // Other
        $setting['other'] = (object)[
            'headerName' => (!array_key_exists('headerName', $addtional_data)) ? $this->HeaderName : $addtional_data['headerName'],
        ];

        // Header
        $setting['h4_pagetitle'] = '';
        $setting['breadcrumb'] = [];

        // Merge all settings into one array
        $setting = array_merge($setting, $addtional_data);

        // Return all settings
        return $setting;
    }

    /**
     * Page View {show}
     * Method is private and not accessible via the web
     * Todo: This method is the only method that is accessible render the view/page visible via browser.
     *
     * @param  requred $data - (has all the values needed to render the page)
     * @param  optional $layout - (By default the layout is main)
     *
     * @return \Illuminate\Http\Response
     */
    private function show($data, $layout = 'main')
    {
        // Add Layout
        $data['layoutName'] = $layout;
        //Load Page View
        return view("{$data['theme_dir']}/pages/" . $data['page_name'], $data);
    }

    /**
     * Main {Index}
     * Method is public and accessible via the web
     * Todo: This method is the main settings page.
     *
     * @param  optional  $message - notification message (By default, no message is displayed)
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $message = '')
    {
        // Load View Page Path
        $view = 'profile';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        $data['user'] = \app\Models\User::with('usermetas')->where('id', auth()->user()->id)->first();

        // dd($data);

        $_user_extr = \App\Models\Vrm\UserMeta::where('user', auth()->user()->id)->pluck('value', 'key')->toArray();
        $data['moredata'] = $_user_extr;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);
    }

    /**
     * Page {open}
     * Method is public and accessible via the web
     * @Todo:
     * This method is used to open a specific view/page (you can pass the view name/full_path and open will call show() method to render the view/page)
     *
     * @param required $view - (the view name/full_path to be rendered)
     * @param  optional $message - notification message (By default, no message is displayed)
     * @param  optional $layout - (By default the layout is main)
     *
     * @return \Illuminate\Http\Response
     */
    public function open($view, $message = '', $layout = 'main')
    {
        // Load View Page Path
        $page = Str::plural($this->MainFolder) . "/" . $this->SubFolder . $view;

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data, $layout);
    }

    public function profileEdit(Request $request, $message = ''){
        // Load View Page Path
        $view = 'profileupdate';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        // ? Check the authenticated user
        if (!Auth::user()) {
            // ? Redirect to login page
            return redirect()->route('account-signin');
        }
        // set user id
        $user_id = Auth::user()->id;
        $data['user'] = \app\Models\User::with('usermetas')->where('id', $user_id)->first();

        $_user_extr = \App\Models\Vrm\UserMeta::where('user', $user_id)->pluck('value', 'key')->toArray();
        $data['moredata'] = $_user_extr;
        // dd($data);
        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);
    }

     /**
     * For Updating {update}
     * Method is private and not accessible via the web
     * Todo: This method is used to update data to the database.
     *
     * @param  \Illuminate\Http\Request  $request - (the request object)
     * @param  required $action - (what option to validate)
     *
     * @return \Illuminate\Http\Response
     */
    public function profileUpdate(Request $request, $action = '')
    {
        // dd($request->all());
        // ? Check the authenticated user
        if (!Auth::user()) {
            // ? Redirect to login page
            return redirect()->route('account-signin');
        }

        $userId = Auth::user()->id;
        $allowed_files = (is_null($this->AllowedFile)) ? 'jpg,jpeg,png' : $this->AllowedFile; //Set Allowed Files
        $upoadDirectory = $this->Upload . "/"; //Upload Location

        $_user_extr = \App\Models\Vrm\UserMeta::where('user', $userId)->pluck('value', 'key')->toArray();
        $data['moredata'] = $_user_extr;

        // Validate Form Data
        $validator = Validator::make($request->all(), [
            'name' => "required|max:100",
            'nikename'=>"max:50",
            'email' => "required|max:200|email|unique:users,email,$userId",
            'address'=> "max:70",
            'phone' => [
                'nullable',
                'between:8,17',
                'regex:/^\d{1,3}\d{9,}$/',
                Rule::unique('users', 'phone')->ignore($userId)->whereNotNull('phone'),
            ],
            fn($attribute, $value, $fail) => strlen($value) > 11 ? $fail('The ' . $attribute . ' field must not be greater than 11.') : null,
            'profession' => "nullable|max:300",
            'country' => "nullable|max:15",
        ], [
            'phone.regex' => 'The phone number must be in international format with country code, e.g. "254xxxxxxxxxx".',
        ]);

        // On Validation Failphp
        if ($validator->fails()) {
            session()->flash('notification', 'error');
            Notify::error('Please check the form for errors.');

            // Return Error Message
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        // Validate Form Data
        $validated = $validator->validated();
        // dd($validated);
        // Remove + from phone number
        $validated['phone'] = Str::replaceFirst('+', '', $validated['phone']);

        // Add User Role
        $updateFrom['name'] = $validated['name'];
        $updateFrom['email'] = $validated['email'];
        $updateFrom['nikename'] =$validated['nikename'];

        $updateFrom['address'] = $validated['address'];
        $updateFrom['phone'] = (!empty($validated['phone']) && !is_null($validated['phone'])) ? $validated['phone'] : null;

        // usermeta
        $updateFrommeta = [
            'address' => $validated['address'],
            'nikename' => $validated['nikename'],
        ];
        // dd($updateFrom);

        // Store Product
        DB::beginTransaction();

        try {
            $user = User::with('usermetas')->where('id', $userId)->first(); // Assuming you have the product ID Update the product data
            $user->update($updateFrom);

            foreach ($updateFrommeta as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    // loop through the array $product->productmetas() and update the meta_value
                    $user->usermetas()->updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;

            // Notification
            session()->flash('notification', 'error');

            // Open Page
            return redirect()->back()->with('message', 'DB-Error! Profile info could not be updated.');
        }
        // Notification
        session()->flash('notification', 'success');

        // Open Page
        return redirect()->back()->with('message', 'Customer Info was Updated successfully.');
    }
}


