<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $ShopRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( ShopRepository $ShopRepository)
    {
        $this->middleware('auth');
        $this->ShopRepository = $ShopRepository;
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }


      public function List(Request $request )
    {
        if($request->type == "prefered")
        {  $items = DB::table('shops')
                        ->leftJoin('preferedshops', 'shops.id', '=', 'preferedshops.shop_id')
                        ->select('shops.*','preferedshops.id as shop_id')
                        ->where('preferedshops.status','like','%like%')
                        ->get();
        }
        else if($request->type == "nearby")
        { 
            $items = DB::table('shops')
                       ->leftJoin('preferedshops', 'shops.id', '=', 'preferedshops.shop_id')
                       ->select('shops.*',DB::raw('TIMESTAMPDIFF(MINUTE,created_on,NOW()), NOW(),created_on'))
                       ->where('preferedshops.status','like','%dislike%')
                       ->whereRaw('TIMESTAMPDIFF(MINUTE,created_on,NOW()) <= 120')
                       ->get();

       // ==================supression de magasins dépassant 2h dans la liste Nearby===========================//
             $items_deleted = DB::table('preferedshops')
                       ->select('preferedshops.id')
                       ->where('preferedshops.status','like','%dislike%')
                       ->whereRaw('TIMESTAMPDIFF(MINUTE,created_on,NOW()) > 120')
                       ->get();
             foreach ($items as $item) {

                 DB::table('preferedshops')->where('id', '=', $item->id)->delete();
             }

        }
        else 
        {
            $items = DB::table('shops')
                    ->leftJoin('preferedshops', 'shops.id', '=', 'preferedshops.shop_id')
                    ->select('shops.*')
                    ->whereNull('preferedshops.status')
                   // ->orWhere('preferedshops.status','like','%dislike%')
                    ->get();
        }
        
        $array_json = [];
        if(sizeof($items) == 0){
            $response = [
                'data' => 'No items',
                'type' => $request->type
            ];
        }else{
            foreach ($items as $item) {
            $pointFirst  = $request->latitude.','.$request->longitude;
            $pointSecond = $item->latitude.','.$item->longitude;
            $distance = $this->getDistance($request->latitude,$request->longitude,$item->latitude,$item->longitude);
            $array_json[] =['distance'=>$distance , 'record'=>$item,'type'=>$request->type];
            }
            $array_json_sorted = $this->orderBy($array_json, 'distance', SORT_ASC);

             $response = [
                'data' => $array_json_sorted,
                'type' => $request->type
            ];
        }
        

        return response()->json($response);     

    }

 /*************************** add to preferred shops  and Nearby Shops********************************/

    public function Like(Request $request)
    {
        $preferedshops = DB::table('preferedshops')
                     ->where('shop_id', $request->id)
                     ->count();
    if($preferedshops == 0)
       { 
            DB::table('preferedshops')->insertGetId(
                     ['status' => $request->like,'shop_id' => $request->id, 'created_by' => auth::id(),
                      'created_on' => date("y-m-d H:i:s")]
                     );

            return response()->json(array('msg'=>($request->like == 'like'?'shop added to my prefered shops':'shop added to my nearby shops'))); 
        } 

        else{
           return response()->json(array('err'=>'ERR'));  
        } 

    }
    
 /*************************** calcule du distance entre deux point********************************/

        function getDistance($lat1, $lon1, $lat2, $lon2) 
    {
        //rayon de la terre
        $r = 6366;
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $lon1 = deg2rad($lon1);
        $lon2 = deg2rad($lon2);
        //calcul précis
        $dp= 2 * asin(sqrt(pow (sin(($lat1-$lat2)/2) , 2) + cos($lat1)*cos($lat2)* pow( sin(($lon1-$lon2)/2) , 2)));
 
        //sortie en km
        $d = $dp * $r;
        return $d;
    }


    function orderBy()
    {
            $args = func_get_args();
            $data = array_shift($args);
            foreach ($args as $n => $field) {
                if (is_string($field)) {
                    $tmp = array();
                    foreach ($data as $key => $row)
                        $tmp[$key] = $row[$field];
                    $args[$n] = $tmp;
                    }
            }
            $args[] = &$data;
            call_user_func_array('array_multisort', $args);
            return array_pop($args);
    }


     /*************************** remove a shop from my preferred shops list********************************/

    function delete(Request $request){

        $preferedshopsFind = DB::table('preferedshops')
                     ->where('id', $request->id)
                     ->count();
        if($preferedshopsFind>0){
            DB::table('preferedshops')->where('id', '=', $request->id)->delete();
            return response()->json(array('msg'=>'shop deleted'));  
        }

        else{
            return response()->json(array('err'=>'shop not fond'));  
        }

      


    }
}
