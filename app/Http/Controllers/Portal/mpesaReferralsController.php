<?php

namespace App\Http\Controllers\Portal;

use App\Models\Vrm\Notify;
use App\Models\Vrm\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ReferralsController extends Controller
{

    // PRIVATE VARIABLES
    private $Table = ''; // Table name will be pluralized

    private $ThemePath = ""; //Main Theme Path starting from resources/views/
    private $MainFolder = "projects"; //Main Folder Name (in prural) inside the resources/views/$ThemePath/pages
    private $SubFolder = "/portal"; //Sub Folder Name inside the resources/views/$ThemePath/pages/$MainFolder/
    private $Upload = ""; //Upload Folder Name inside the public/admin/media

    private $ParentRoute = ""; // Parent Route Name Eg. vrm/settings
    private $AllowedFile = null; //Set Default allowed file extension, remember you can pass this upon upload to override default allowed file type. jpg|jpeg|png|doc|docx|

    private $New = ''; // New
    private $Save = ''; // Add New
    private $Edit = ''; // Edit
    private $Update = ''; // Update
    private $Delete = ''; // Delete
    private $Action = ''; // Multiple Entry Action

    private $LoginView = 'login';

    private $Subscribe = '/subscribe';

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
            'loginview' =>$this->LoginView,
            'subscribe' =>$this->Subscribe,
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
        $view = 'refarrals';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        $user = auth()->user()->id;

        $data['name'] = null;
        $data['totalreferral'] = \App\Models\Referral::with('this_refer')->where('referred_by', $user)->count();

        $bills = \App\Models\Referral::whereHas('this_billing', function($query){$query->where('status',1)->where('flag',1);})->where('referred_by', $user)->get();
        $total_earned = 0;
        foreach($bills as $bill){
            $total_earned += intval($bill->this_billing->total);
        } 

        $successWithdraw = \App\Models\Cash\Txn::whereHas('billing_info', function($query) use($user){$query->where('status',1)->where('flag',1)->where('type', 'withdraw')->where('user', $user);})->sum('dr');
        $total = $total_earned - $successWithdraw; 
        $data['total'] = $total;   

        $data['entry_list'] = \App\Models\Referral::with('this_refer','this_billing')->where('referred_by', $user)->get(); 
        // $data['referrer'] =  \App\Models\Referral::with('this_user')->where('user', $user)->first();

        $referrer =  \App\Models\Referral::where('user', $user)->first();
     
        if(!$referrer->referred_by==null){
            $ref = \App\Models\User::where('id', $referrer->referred_by)->first();
            $data['name'] = $ref->name;
        }

        $referral_code = \App\Models\Vrm\UserMeta::where('user', $user)->pluck('value', 'key');

        $data['getReferralLink'] = url('/register?ref=' . $referral_code['referral_code']);

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

    public function donate(Request $request, $message = '')
    {
        // Load View Page Path
        $view = 'packages';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        $user = auth()->user()->id;

        $data['totalreferral'] = \App\Models\Referral::with('this_user')->where('referred_by', $user)->count();

        $data['packages'] = \App\Models\Cash\Package::get();

        $data['total'] = \App\Models\Vrm\UserMeta::where('user', $user)->where('key', 'total_amount')->pluck('value', 'key')->first();

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);
    }

    public function subscribe(Request $request){
        log::info('subscribe data', [$request->all()]);        

        $validator = Validator::make($request->all(), [
            'phone' => "nullable|between:8,17|regex:/^\d{1,3}\d{9,}$/",
            'package' => 'required',
            'user' => 'required',
            'amt' => 'required|numeric|min:1',
        ],[
            'phone.regex' => 'The phone number must be in international format with country code, e.g. "254xxxxxxxxxx".',
        ]);

        if ($validator->fails()) {
            // $message = "Please check the form for errors.";
            $message = $validator->errors();
            session()->flash('notification', 'error');

            // Return Error Message         
            log::info('subscription data validation fails ',[$validator->errors()]);
            return response()->json([
                'status' => false,
                'response' => [
                    // 'errors' => $validator->errors(),
                    'message' => $message,
                ]
            ]);
          
        }

         $validated = $validator->validated();
        log::info('subscription data is validated', $validated);


        $decoded['package'] = $validated['package'];
        $decoded['user'] = $validated['user']; // need package and user

        $results = \App\Models\Api\Manage\BillingControl::issue_new_billing($decoded);

        $billing = \App\Models\Cash\Billing::where('uid', $results['uid'])?->first();

        // Chpter
        $mpesaTxnFound = \App\Models\MpesaModel::where('billing', $billing->id)->first();
        
        if (is_null($mpesaTxnFound)) {
            $mpesaTxnFound = new \App\Models\MpesaModel;
            $mpesaTxnFound->billing = $billing->id;
            $mpesaTxnFound->type = 'cashin';
            $mpesaTxnFound->payment_method ='mpesa';
            // save
            $mpesaTxnFound->save();
        }

        log::info('billing created', [$results]);

        $amount = (int)$validated['amt'];
        $phoneNumber = $validated['phone'];
        $mpesa = new \App\Models\MpesaModel();

        $mpesa_response = $mpesa->lipaNaMpesa($amount, $phoneNumber);



        // TODO: SEND TO CHPTER
        if ($mpesa_response['ResponseCode'] == 0) {
            // Mpesa
            $mpesaTxnFound->MerchantRequestID = $mpesa_response['MerchantRequestID'];
            $mpesaTxnFound->CheckoutRequestID = $mpesa_response['CheckoutRequestID'];
            // save
            $mpesaTxnFound->save();
        }
        

        log::info('amount from validated', [$mpesa_response]);

        $message = 'Donate request was sent successfully!';
        return response()->json([
            'status' => true,
            'response' => [
                'message' => $message,
            ]
        ]);
    }

}
