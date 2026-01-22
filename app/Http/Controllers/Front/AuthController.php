<?php

namespace App\Http\Controllers\Front;

use App\Models\Vrm\Notify;
use App\Models\Vrm\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // PRIVATE VARIABLES
    private $Table = ''; // Table name will be pluralized

    private $ThemePath = ""; //Main Theme Path starting from resources/views/
    private $MainFolder = "logs"; //Main Folder Name (in prural) inside the resources/views/$ThemePath/pages
    private $SubFolder = ""; //Sub Folder Name inside the resources/views/$ThemePath/pages/$MainFolder/
    private $Upload = ""; //Upload Folder Name inside the public/admin/media

    private $ParentRoute = ""; // Parent Route Name Eg. vrm-settings
    private $AllowedFile = null; //Set Default allowed file extension, remember you can pass this upon upload to override default allowed file type. jpg|jpeg|png|doc|docx|

    private $New = ''; // New
    private $LoginView = 'login';
    private $registerView = 'register';
    private $Login = 'account-signin/access'; // Add New
    private $Register = 'account-signup/access'; // Add New
    private $Reverify = 'account-verification/reverify'; // Add New
    private $ResetPassword = 'account/resetpassword'; // Add New
    private $updatePassword = '/account/resetpassword/update'; // Add New
    private $Edit = ''; // Edit
    private $Update = ''; // Update
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
            'login' => $this->Login,
            'register' => $this->Register,
            'verify' => $this->Reverify,
            'edit' => $this->Edit,
            'update' => $this->Update,
            'delete' => $this->Delete,
            'manage' => $this->Action,
            'route' => $this->ParentRoute,
            'loginview' => $this->LoginView,
            'registerview' => $this->registerView,
            'resetpassword' => $this->ResetPassword,
            'updatepassword' => $this->updatePassword,
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
    public function index($message = '')
    {
        // Load View Page Path
        $view = 'login';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);
    
        // dd( $setting['links']);
        //Open Page
        return $this->show($data);
    }

    public function registerview(Request $request, $message = '')
    {
        // dd($request->ref);
        // Load View Page Path
        $view = 'register';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;
        $data['ref'] = $request->ref;
        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);
    
        // dd( $setting['links']);
        //Open Page
        return $this->show($data);
    }


    public function twoStepAuth(Request $request, $message=''){
         // Load View Page Path
         $view = 'two_step_auth';
         $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

         // Load Settings
         $data = $this->loadSettings($page);
         $data['other']->view = $view;

         //Notification
         $notify = Notify::notify();
         $data['notify'] = Notify::$notify($message);

         //Open Page
         return $this->show($data);
    }

    public function resetPassword(Request $request, $message=''){
        // Load View Page Path
        $view = 'reset_password';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);
   }

    //  hundle reset password request
   public function validatEmail(Request $request, $message = ''){
        // Validate Form Data
        $validator = Validator::make($request->all(), [
            'email' => "required|max:200|email",
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
        // ? Check Email
        $found = \App\Models\User::where('email', $validated['email'])?->first();
        if (!$found) {
            // ? Message
            $message = "The email entered does not belong to any account. Please register";
            // ? Account Doesnot Exist
            session()->flash('notification', 'error');

            // Return Error Message
            return redirect()->back()->with('message', $message);
        }
        

        // ? Verification Code
        $verification = new \App\Services\Verification();
        $verify = $verification->generateVerificationCode($found->id);
        // ? URL
        $verify_url = url('account/updatepassword') . '?v=' . $verify;

        // Send Password To Email
        $mail_data = [
            'name' => $found->name,
            'email' => $found->email,
            'username' => $found->username,
            'verification' => $verify_url,
        ];

         // ? Mail
         Mail::to($found->email)->send(new \App\Mail\ResetPasswordMail($mail_data));

         // Later we nedd to send company verification email

         $message = "A password reset email has been sent successfully. Please check your inbox for further instructions.";
         
         // $message = "Account created successfully.";
         session()->flash('notification', 'success');

         // Return Error Message
         return redirect()->back()->with('message', $message);
       
   }


   /**
    * Todo: Update Password
    * Method is public and accessible via the web
    */
    public function updatepasswordview(Request $request, $message=''){
        // Load View Page Path
        $view = 'newpassword';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;
        $data['code'] = $request->v;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);
   }  
   
   //  hundle reset password request
   public function updatePassword(Request $request, $message=''){
        // Validate Form Data
        $validator = Validator::make($request->all(), [
            'password' => "required|min:5|max:20|confirmed",
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

         $code = $request->input('code');

         // ? Check if code is valid
         if ($code) {
 
             // ? Verification Code
             $verification = new \App\Services\Verification();
             $userId = $verification->verifyVerificationCode($code);

             // ? Check if code is valid
             $user = \App\Models\User::where('id', $userId)->first();
             if ($user) {
                 // ? Update User
                $user->password = Hash::make($validated['password']);   
                $user->save();

                // ? Notification
                $message = "Password successfully updated. Please login to continue.";
                session()->flash('notification', 'success');

                // ? Redirect
                return redirect()->route('account-signin')->with('message', $message);
             }
         }
 
         // ? Notification
         $message = "Password Reset failed, might be code or token expired. Please try again.";
         session()->flash('notification', 'error');
 
         // ? Redirect
         return $this->resetPassword($request, $message);;

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

    /**
     * Todo: Login User
     * Method is public and accessible via the web
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // dd($request->all());
        // ? Validate Form Data
        $validator = Validator::make($request->all(), [
            'email' => "required|email|max:200",
            'password' => "required|max:20",
            'remember' => "nullable|boolean",
        ]);

        // ? On Validation Failphp
        if ($validator->fails()) {
            session()->flash('notification', 'error');
            $message = 'Please check the form for errors.';

            // ? Return Error Message
            return redirect()->back()->withErrors($validator)->withInput($request->input())->with('message', $message);
        }

        // ? Validate Form Credentials
        $credentials = $request->only('email', 'password');
        $credentials['flag'] = 1;
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // dd($credentials);
            // Generate Token
            // \App\Services\TokenService::createToken(Auth::user(), 'apptoken', ['porject-bid'], now()->addDay());


            // ? URL
            $redirect_url = '/account/home/dashboard';
           
            // Check if is in cart
            $_fromcart = $request->get('r');
            if (!is_null($_fromcart) && !empty($_fromcart)) {
                $redirect_url = '/cart';
            }

            // Authentication passed...
            return redirect()->intended("$redirect_url");
        }

        // ? Message
        $message = 'Invalid credentials. Check your email and password.';

        // ? Check User
        $found = \App\Models\User::where('email', $request->email)?->first(['flag']);
        if ($found) {
            if ($found->flag == 0) {
                // ? Return Error Message       
                session()->flash('notification', 'warning');
                $message = '
                    Please activate your account first. 
                    <form method="POST" action="'.url('/account/resend-verification').'" style="display:inline;">
                        '.csrf_field().'
                        <input type="hidden" name="email" value="'.e($request->email).'">
                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block fa-lg gradient-custom-2 my-1" type="submit">Resend Verification</button>
                    </form>
                ';
                return redirect('/login')->withInput($request->input())->with('message', $message);    
            }
        }

        // ? Return Error Message
        session()->flash('notification', 'error');

        // ? Return Error Message
        return redirect()->back()->withErrors($validator)->withInput($request->input())->with('message', $message);
    }

    /**
     * Todo: Logout User
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Redirect the user to the login page
        return redirect('/');
    }


    /**
     * Todo: This method is used to register customer.
     *
     * @param  \Illuminate\Http\Request  $request - (the request object)
     * @param  required $action - (what option to validate)
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, $action = '')
    {
        // Validate Form Data
        $validator = Validator::make($request->all(), [
            'fullname' => "required|max:60",
            'phone' => "nullable|between:8,17|unique:users,phone|regex:/^\d{1,3}\d{9,}$/",
            'email' => "required|max:200|email|unique:users,email",
            'password' => "required|min:5|max:20|confirmed",

            // terms
            'term' => "nullable|max:10",
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
        // Add User Role
        $insertFrom['name'] = $validated['fullname'];
        $insertFrom['email'] = $validated['email'];
        $insertFrom['phone'] = (!is_null($validated['phone']) && !empty($validated['phone'])) ? $validated['phone'] : null;
        $insertFrom['password'] = Hash::make($validated['password']);
        // usermeta
        $insertFrom['usermeta'] = [
            'term' => array_key_exists('term',$validated)?$validated['term']:null,
            'profile' => null,
        ];

        // ? Removed from @
        $username = $validated['email'];
        $username = explode('@', $username);
        $username = $username[0];
        // Username
        $insertFrom['username'] = \App\Models\Vrm\Term::username($username);

        // Save Form Data
        $saved = new \App\Models\User();
        $saved->username = $insertFrom['username'];
        $saved->name = $insertFrom['name'];
        $saved->phone = $insertFrom['phone'];
        $saved->email = $insertFrom['email'];
        $saved->password = $insertFrom['password'];
        $saved->flag = 0;
        $saved->save();
        // $referrer = null;
        // if ($request->has('ref')) {
        //     $referrer = \App\Models\User::with('usermetas')->where('referral_code', $request->ref)->first();
        // }

        if ($request->has('ref')) {
            $referrer = \App\Models\Vrm\UserMeta::where('key','referral_code')->where('value', $request->ref)->first();
        }
        $ref = null;
        if($referrer){
            $ref = $referrer->user; 
        }

        // dd($referrer->user);

        $referral = new \App\Models\Referral();
        $referral->user =$saved->id;
        $referral->referred_by = $ref;
        $referral->status = 0;
        $referral->save();

       
        $code = strtoupper(Str::random(6) . time()); // Appends a timestamp to ensure uniqueness
        
        // dd($code);
        \App\Models\Vrm\UserMeta::Create([
            'user' => $saved->id ,
            'key' => "referral_code",
            'value' => $code,
        ]);
            



        if ($saved) {

            // ? User Meta
            foreach ($insertFrom['usermeta'] as $key => $value) {
                $usermeta = new \App\Models\Vrm\UserMeta();
                $usermeta->user = $saved->id;
                $usermeta->key = $key;
                $usermeta->value = $value;
                $usermeta->save();
            }

            // ? Assign roles with ID 3 to the user - CUSTOMER
            $user = \App\Models\User::find($saved->id);
            $user->roles()->attach(3);

            // ? Verification Code
            $verification = new \App\Services\Verification();
            $verify = $verification->generateVerificationCode($user->id);
            // ? URL
            $verify_url = url('account-verification') . '?v=' . $verify;

            // Send Password To Email
            $mail_data = [
                'name' => $saved->name,
                'email' => $saved->email,
                'username' => $saved->username,
                'verification' => $verify_url,
            ];

            // ? Mail
            Mail::to($insertFrom['email'])->send(new \App\Mail\RegistrationMail($mail_data));

            // Later we nedd to send company verification email

            $message = "Account created successfully. Please check your email for verification.";
            
            // $message = "Account created successfully.";
            session()->flash('notification', 'success');

            // Return Error Message
            return redirect()->back()->with('message', $message);
        }

        // Notification
        session()->flash('notification', 'error');

        // Open Page
        return $this->index('<strong>Error:</strong>Account was not created, kindly try again.');
    }

    /**
     * Todo: Verification
     * Method is public and accessible via the web
     *
     * @param  \Illuminate\Http\Request  $request - (the request object)
     */
    public function verification(Request $request)
    {
        // dd($request->all());
        // ? Get Verification Code
        $code = $request->input('v');

        // ? Check if code is valid
        if ($code) {

            // ? Verification Code
            $verification = new \App\Services\Verification();
            $userId = $verification->verifyVerificationCode($code);

            // ? Check if code is valid
            $user = \App\Models\User::where('id', $userId)->first();
            if ($user) {
                // ? Update User
                $user->flag = 1;
                $user->save();

                // ? Notification
                $message = "Account verified successfully. Please login to continue.";
                session()->flash('notification', 'success');

                // ? Redirect
                return redirect()->route('account-signin')->with('message', $message);
            }
        }
        // ? Notification
        $message = "Account verification failed, might be verification code or token expired. Please try again.";
        session()->flash('notification', 'error');

        // ? Redirect
        return $this->open('register', $message, 'main');
    }

    /**
     * Todo: Re-verification
     * Method is public and accessible via the web
     *
     * @param  \Illuminate\Http\Request  $request - (the request object)
     */
    public function reverify(Request $request)
    {
        // Validate Form Data
        $validator = Validator::make($request->all(), [
            'email' => "required|max:200|email",
        ]);

        // On Validation Failphp
        if ($validator->fails()) {
            session()->flash('notification', 'error');
            Notify::error('Please check the form for errors.');

            // Return Error Message
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        // ? Check Email
        $found = \App\Models\User::where('email', $request->email)?->first();
        if ($found) {
            // ? Check Flag
            if ($found->flag == 1) {
                session()->flash('notification', 'warning');

                // ? Message
                $message = 'This account is already active. please login or reset password if you forgot';

                // Return Error Message
                return redirect()->route('account-signin')->withInput($request->input())->with('message', $message);
            }

            // ? Verification Code
            $verification = new \App\Services\Verification();
            $verify = $verification->generateVerificationCode($found->id);
            // ? URL
            $verify_url = url('account-verification') . '?v=' . $verify;

            // Send Password To Email
            $mail_data = [
                'name' => $found->name,
                'email' => $found->email,
                'username' => $found->username,
                'verification' => $verify_url,
            ];

            // ? Maile User
            if ($found->level == 'customer') {
                // ? Mail
                // Mail::to($found->email)->send(new \App\Mail\RegistrationMail($mail_data));
            } else {
                // Mail::to($found->email)->send(new \App\Mail\RegistrationResearcherMail($mail_data));
            }

            session()->flash('notification', 'success');
            $message = "Account verification link sent. Please check your email for verification.";

            // Return Error Message
            return redirect()->route('account-signin')->with('message', $message);
        }

        // ? Message
        $message = "The email entered does not belong to any account. Please register";
        // ? Account Doesnot Exist
        session()->flash('notification', 'error');

        // Return Error Message
        return redirect()->route('account-signup')->with('message', $message);
    }

    /**
     * Todo: This method is used to send password reset link to the user.
     *  
     * @param  \Illuminate\Http\Request  $request - (the request object)
     * @param  required $action - (what option to validate)
     *
     * @return \Illuminate\Http\Response
     */

    public function resendVerification(Request $request){
     
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user) {
        return redirect('/login')->with('message', 'Email not found.');
    }

    $verification = new \App\Services\Verification();
    $verify = $verification->generateVerificationCode($user->id);
    // ? URL
    $verify_url = url('account-verification') . '?v=' . $verify;

    // Send Password To Email
    $mail_data = [
        'name' => $user->name,
        'email' => $user->email,
        'username' => $user->username,
        'verification' => $verify_url,
    ];

    // ? Mail
    Mail::to($user->email)->send(new \App\Mail\ResendEmailverificationMail($mail_data));

    // Later we nedd to send company verification email

    $message = "Verification email resent successfully. Please check your inbox for further instructions.";
    
    // $message = "Account created successfully.";
    session()->flash('notification', 'success');

    return $this->open('login', $message, 'main');

    }

}
