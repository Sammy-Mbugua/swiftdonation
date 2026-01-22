<?php

namespace App\Http\Controllers\Setup\Manage;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Vrm\HierarchyMeta;
use App\Models\Vrm\Hierarchy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\Vrm\Setting;
use App\Models\Vrm\Notify;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{

    // PRIVATE VARIABLES
    private $Table = ''; // Table name will be pluralized

    private $ThemePath = ""; //Main Theme Path starting from resources/views/
    private $MainFolder = "manage"; //Main Folder Name (in prural) inside the resources/views/$ThemePath/pages
    private $SubFolder = "/subscription"; //Sub Folder Name inside the resources/views/$ThemePath/pages/$MainFolder/
    private $Upload = ""; //Upload Folder Name inside the public/admin/media

    private $ParentRoute = "vrm/setup/manage/subscription"; // Parent Route Name Eg. vrm/settings
    private $AllowedFile = null; //Set Default allowed file extension, remember you can pass this upon upload to override default allowed file type. jpg|jpeg|png|doc|docx|

    private $New = ''; // New
    private $Save = 'vrm/setup/subscription/save'; // Add New
    private $Edit = 'vrm/setup/subscription/edit'; // Edit
    private $Update = 'vrm/setup/subscription/update'; // Update
    private $Delete = 'vrm/setup/subscription/delete'; // Delete
    private $Action = 'vrm/setup/manage-subscription/status'; // Multiple Entry Action

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
        $settings = Setting::adminLoad($view_name, $passed);

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
        $setting['h4_pagetitle'] = 'Dashboard';
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
        return view("admin/pages/" . $data['page_name'], $data);
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
        $view = 'list';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        $suerss = \App\Models\Referral::get();
        // dd($suerss);

        $data['entry_list'] = \App\Models\Referral::get();
        //Notification
        // $notify = Notify::notify();
        // $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data, 'list');
    }

    /**
     * Page {open}
     * Method is public and accessible via the web
     * Todo: This method is used to open a specific view/page (you can pass the view name/full_path and open will call show() method to render the view/page)
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
     * Page {edit}
     * Method is private and can;t via the web
     * Todo: This method is used to preview/open for edit a record
     *
     * @param  \Illuminate\Http\Request  $request - (the request object)
     * @param  string $page - page to be opened by default is edit
     * @param  optional $message - notification message (By default, no message is displayed)
     * @param  optional $layout - (By default the layout is main)
     */
    public function edit(Request $request, $page = 'edit', $message = '', $layout = 'main')
    {
    // dd($request->all());
        // Load View Page Path
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$page";
        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $page;

        // Get the id
        $id = $request->get('id');

        // Fetch Hierarchy
        $package =  \App\Models\Cash\Package::where('id', $id)->first();

        if (is_null($package)) {
            // Notification
            session()->flash('notification', 'error');
            // Open Page
            return $this->index('<strong>Error:</strong> Invalid request, please try again.');
        }

        // Data Found
        $data['resultFound'] = $package;

        //Notification
        $notify = Notify::notify();
        $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data, $layout);
    }

    /**
     * Validation {valid}
     * Method is public and accessible via the web
     * Todo: This method is used to validate the form data.
     *
     * @param  \Illuminate\Http\Request  $request - (the request object)
     * @param  required $action - (what option to validate)
     *
     * @return \Illuminate\Http\Response
     */
    public function valid(Request $request, $action = '')
    {
        // dd($request->all('id'));
        // Load View Page Path
        $view = 'view';
        $page = Str::plural($this->MainFolder) . $this->SubFolder .  "/$view";

        // Load Settings
        $data = $this->loadSettings($page);
        $data['other']->view = $view;

        $user = $request->all('id');
        $data['userName']  = \App\Models\User::where('id', $user)->first();
                // dd($user);

        $data['name'] = null;
        $data['totalreferral'] = \App\Models\Referral::with('this_refer')->where('referred_by', $user)->count();

        // ----------new way-------

        $bills = \App\Models\Referral::whereHas('this_billing', function($query){$query->where('status',1)->where('flag',1);})->where('referred_by', $user)->get();
        $total_earned = 0;
        foreach($bills as $bill){
            $total_earned += intval($bill->this_billing->total);
        } 

        $successWithdraw = \App\Models\Cash\Txn::whereHas('billing_info', function($query) use($user){$query->where('status',1)->where('flag',1)->where('type', 'withdraw')->where('user', $user);})->sum('dr');
        $total = $total_earned - $successWithdraw; 
        $data['total'] = $total;


        


// -----------------------------------------------------------------------------------------------------------------------------------------------------------------
        // $bills = \App\Models\Referral::whereHas('this_billing', function($query){$query->where('status',1)->where('flag',1);})->where('referred_by', $user)->get();

        // $data['total'] = \App\Models\Vrm\UserMeta::where('user', $user)->where('key', 'total_amount')->pluck('value', 'key')->first();
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------

        $data['entry_list'] = \App\Models\Referral::with('this_refer','this_billing')->where('referred_by', $user)->get(); 
        // $data['referrer'] =  \App\Models\Referral::with('this_user')->where('user', $user)->first();

        $referrer =  \App\Models\Referral::where('user', $user)->first();
        // dd($referrer);

        if(!$referrer->referred_by==null){
            $ref = \App\Models\User::where('id', $referrer->referred_by)->first();
            $data['name'] = $ref->name;
        }

        $referral_code = \App\Models\Vrm\UserMeta::where('user', $user)->pluck('value', 'key');

        $data['getReferralLink'] = url('/register?ref=' . $referral_code['referral_code']);

        //Notification
        // $notify = Notify::notify();
        // $data['notify'] = Notify::$notify($message);

        //Open Page
        return $this->show($data);

    }

    public function delete(Request $request, $action = '')
    {

        log::info('delete', [$request->all()]);
        // Check if the request is ajax
        if ($request->ajax()) {
            // Get the id
            $id = $request->get('id');

            // Check if the id is valid
            if (is_null($id)) {
                // Return error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid request, please try again.'
                ]);
            }

            // Delete the record
            if (Hierarchy::where('id', $id)->delete()) {
                // Return success
                return response()->json([
                    'status' => 'success',
                    'message' => 'Record deleted successfully.'
                ]);
            }

            // Return error
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete, please try again.'
            ]);
        }

        // Check if is ID is in GET or POST
        $id = $request->get('id');

        // delete the record
        if ( \App\Models\Cash\Package::where('id', $id)->delete()) {
            // Notification
            session()->flash('notification', 'success');

            // Return success
            return $this->index('<strong>Success:</strong> Record deleted successfully.');
        }

        // Notification
        session()->flash('notification', 'error');

        // Return error
        return $this->index('<strong>Error:</strong> Failed to delete, please try again.');
    }
}
