<?php

namespace App\Http\Controllers\Portal;

use App\Models\Vrm\Notify;
use App\Models\Vrm\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Colors\Rgb\Channels\Red;

class WithdrawalController extends Controller
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

    private $Withdraw = '/withdrawal-fund'; // Withdrawibe

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
            'withdraw' =>$this->Withdraw,
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
        $view = 'withdraw';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        $user = auth()->user()->id;


        $data['name'] = null;
        $data['totalreferral'] = \App\Models\Referral::with('this_refer')->where('referred_by', $user)->count();

        $total = auth()->user()->calculateBalance();         
        $data['total'] = auth()->user()->calculateBalance();

        // chech if the user amount is greater than 1000 or equal to 1000
        $data['amount_to'] = 0;
        if($total >= config('services.retes.min_amount')){ // change this to 1000
            $data['amount_to'] = number_format((float)$total * config('services.retes.rate'), 2, '.', ''); // 90% of the total amount
        }        
        
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

    // Withdrawal starts here

    public function withdrawal(Request $request, $message = '')
    { 
        // dd($request->all()); 
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'between:8,17', 'regex:/^(?:\+254|254|0)\d{9}$/'],
            // 'amount' => 'required|numeric|min:1|max:100000',
        ],[
            'phone.regex' => 'The phone number must be a valid Kenyan number, e.g. 0722xxxxxx, 254722xxxxxx, or +254722xxxxxx.',
        ]);

        if ($validator->fails()) {
            // $message = "Please check the form for errors.";
            $message = $validator->errors();
            session()->flash('notification', 'error');

            //Notification
            $notify = Notify::notify();
            $data['notify'] = Notify::$notify($message);
          
        }
         $validated = $validator->validated();

        $phone = preg_replace('/\s+/', '', $validated['phone']); // Remove spaces if any

        if (preg_match('/^0\d{9}$/', $phone)) {
            // Starts with 0: convert to 254 format
            $validated['phone'] = '254' . substr($phone, 1);
        } elseif (preg_match('/^\+254\d{9}$/', $phone)) {
            // Starts with +254: remove the "+"
            $validated['phone'] = substr($phone, 1);
        } elseif (preg_match('/^254\d{9}$/', $phone)) {
            // Already in correct format: do nothing
            $validated['phone'] = $phone;
        }    

        // user balance
        $user = auth()->user()->id;

        $actual_bal = auth()->user()->calculateBalance(); // 10% is the fee charged by the system

        if($actual_bal < config('services.retes.min_amount')){ // change this to 1000
            $message = "You need to have at least KES 1000 to withdraw.";
            session()->flash('notification', 'warning');
            return redirect()->back()->with('message', $message);
        }

         $donated = \App\Models\Referral::where('user', $user)->first();
        if($donated->status == 0){
            $message = "You must have at least one donation before you can make a withdrawal.";
            session()->flash('notification', 'warning');
            return redirect()->back()->with('message', $message);
        }

        // dd($actual_bal);

        $amount = $actual_bal * config('services.retes.rate'); // 90% of the total amount
        $transaction = $actual_bal - $amount; // 90% of the total amount
     
         // user
        $user_data = \App\Models\User::where('id', $user)->first();      

        $unique_id = uniqid();

        // New Billing
        $_billing = new \App\Models\Cash\Billing;
        $_billing->uid = $unique_id;
        $_billing->user = (int) $user;
        $_billing->type = 'withdraw';
        $_billing->total = $actual_bal;
        $_billing->save();


        $uid = $_billing->uid;

        $billing = \App\Models\Cash\Billing::where('uid', $uid)?->first();   
        
        // dd($billing);      

        // TODO: SEND REQUEST TO CHAPTER
        $client_details = [
            'client_details' => [
                "full_name" => "$user_data->name",
                'phone_number' => "$user_data->phone",
                'email' => "$user_data->email",
            ],
            'destination_details' => [
                'country_code' => 'KE',
                'mobile_number' => "$validated[phone]",
                'wallet_type' => 'mpesa'
            ],
            'transfer_details' => [
                'currency_code' => 'KES',
                // 'amount' => $amount,
                'amount' => 1,
            ],
            'callback_details' => [
                'notify_customer' => true,
                'payout_reference' => "$billing->id",
                'callback_url' => 'https://example.com/callback'
            ]
        ];

        // TODO: SEND REQUEST
        $responnse = \App\Models\Cash\Chpter::chpterPayoutSend($client_details, 'mpesa');

        if ($responnse['success']) {
            // ? Accounting - Billing
            $billing->status = 1;
            $billing->flag = 1;
            $billing->update();

            // ? Withdraw
            \App\Models\Cash\Txn::create([
                'billing' => $billing->id,
                'geteway' => 'chpter',
                'cr' => 0.00,
                'dr' => $actual_bal,
                'currency' => 'KES',
                'type' => 'creator-withdraw',
                'reverse' => 0,
                'ref' => $user_data->id,
                'flag' => 1,
            ]);

            // ? Withdraw
            \App\Models\Cash\Txn::create([
                'billing' => $billing->id,
                'geteway' => 'chpter',
                'dr' => 0.00,
                'cr' => $amount,
                'type' => 'withdrawal',
                'reverse' => 0,
                'ref' => $user_data->id,
                'flag' => 1,
            ]);

             // ? Withdraw transaction record
             \App\Models\Cash\Txn::create([
                'billing' => $billing->id,
                'geteway' => 'chpter',
                'dr' => 0.00,
                'cr' => $transaction,
                'type' => 'transaction',
                'reverse' => 0,
                'ref' => $user_data->id,
                'flag' => 1,
            ]);


            $message = 'Withdrawal request has been sent successfully. Please wait for the payment to be processed.';
            session()->flash('notification', 'success');
            // ? Return Balance
            return redirect()->back()->with('message', $message);
        }        
    }
}