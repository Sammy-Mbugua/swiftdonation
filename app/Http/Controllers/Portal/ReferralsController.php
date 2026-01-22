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

        
        $total = auth()->user()->calculateBalance(); 
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

        $data['total'] = auth()->user()->calculateBalance();

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);
    }

    public function subscribe(Request $request){
        // log::info('subscribe data', [$request->all()]);        

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'between:8,17', 'regex:/^(?:\+254|254|0)\d{9}$/'],
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
            // log::info('subscription data validation fails ',[$validator->errors()]);
            return response()->json([
                'status' => false,
                'response' => [
                    // 'errors' => $validator->errors(),
                    'message' => $message,
                ]
            ]);
          
        }

         $validated = $validator->validated();
        // log::info('subscription data is validated', $validated);

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

        // Log::info('validated ', [$validated['phone']]);

        $decoded['package'] = $validated['package'];
        $decoded['user'] = $validated['user']; // need package and user

        $_results = \App\Models\Api\Manage\BillingControl::issue_new_billing($decoded);

        $billing = \App\Models\Cash\Billing::where('uid', $_results['uid'])?->first();

        

        $user = \App\Models\User::where('id', $validated['user'])->first();

        // log::info('the billing ', [$_results['uid']]);
        // log::info('the user ',[ $validated['user']]);
        // log::info('the user ', [$user->name]);

        $received['name']  = $user->name;        
        $received['email'] = strtolower($user->email);
        $received['phone'] = $validated['phone'];
        $received['location'] = 'nairobi';
        $received['billing'] = $_results['uid'];
        $received['mode'] = 'chpter';

        // Decode Data
        $decoded = $received;

        // Issue Payment
        $results = \App\Models\Api\Manage\PaymentControl::prompt_payment($decoded);

        if ($results['status'] == true) {
            log::info('payment is successful0', $results);
            $this_chpter = \App\Models\Cash\Chpter::updateOrCreate(['billing_id' => $billing->id], [
                'reference' => $results['ref'],
                'type' => 'cashin',
                'billing_amount' => $billing->total,
                'payment_status' => $results['status'],
                'paid' => 0,
                'flag' => 1,
            ]);
            
        }

        $message = 'Donate request was sent successfully!';
        return response()->json([
            'status' => true,
            'response' => [
                'message' => $message,
                'billing' => $billing->uid,

            ]
        ]);

    }

    public function pay_load($OrderCode)
    {
        // get order
        $billing = \App\Models\Cash\Billing::where('uid', $OrderCode)->first();

        // check if order exists
        if (!$billing) {
            return abort(404);
        }

        $chpter_txn_id = $billing->uid;
        $layout = 'main';
        $view = '/payment-validate';
        // Load View Page Path
        $page = Str::plural($this->MainFolder) . "/" . $this->SubFolder . $view;

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;
        $data['chpter_checkout_id'] = $chpter_txn_id;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify('');

        //Open Page
        return $this->show($data, $layout);
    }


    public function pay_status($status="success")
    {

        $layout = '/main';
        $view = $status;
        // Load View Page Path
        $page = Str::plural($this->MainFolder) . "/" . $this->SubFolder . $view;

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify('');

        //Open Page
        return $this->show($data, $layout);
    }

    /**
     * Todo :: logTest
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function check_status(Request $request)
    {

        // ? get order key
        $chpter_txn_id = $request->get('oid');
        // ? LOGISTICS ITEM
        $chpterTxn = \App\Models\Cash\Billing::where('uid', $chpter_txn_id)->first();

        $view = '/failed';
        if ($chpterTxn) {
            if ($chpterTxn->status == 1) {
                $view = '/success';
            }
        }
        // Load View Page Path;

        // $user = \App\Models\User::where('id',$chpterTxn->donationinfo->user)->first();

        // Load Settings
        $page = Str::plural($this->MainFolder) . "/" . $this->SubFolder . $view;

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::error('Payment request confirmation failed.');
        $data['status'] = false;

        if ($chpterTxn) {
            if ($chpterTxn->status == 1) {
                $data['notify'] = Notify::success('Payment request confirmation successful.');
                $data['status'] = true;
                $view = 'success';
            }
        }

        // save
        $data['chpter_checkout_id'] = $chpter_txn_id;
        // $data['username'] = $user->username;

        //Open Page
        return $this->show($data,'main');
        // return $this->paymentStatus($chpterTxn->paid,);

    }

}