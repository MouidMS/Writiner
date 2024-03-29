<?php

namespace App\Http\Controllers;
use App\Models\Collector;
use App\Models\Favorite;
use App\Models\Friend;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Rate;
use App\Models\Ready_text_edit;
use App\Models\Receive;
use App\Models\SherProject;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Finder\Finder;
use Termwind\Components\Li;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function getProject($id)
    {
        $id_user = Auth::id();
        $project = DB::table('projects')->where('id', $id)->get();
        $filename = Storage::disk('jsonUser')->get($id_user . '/projects/' . $id . '.json');
        // $filename = json_encode(Storage::disk('jsonUser')->get($id_user . '/projects/' .$id.'.json'), true);

        $receive = null;
        $commuinty = null;
        $collector = null;
        $type = $project[0]->type;
        $readOnly = false;
        return view('landpage.page', compact('project'))->with(['id' => $id])->with(['readOnly' => $readOnly])->with(['filename' => $filename])->with(['receive' => $receive])->with(['commuinty' => $commuinty])->with(['collector' => $collector])->with(['type' => $type]);

    }

    public static function getProjectInfo($id)
    {
        $ck = Project::Find($id);

        $project = DB::table('projects')->where('id', $id)->get()[0];
        $project_id = $project->id;
        if ($ck) {
            $project = DB::table('projects')->where('id', $id)->get()[0];
            $project_id = $project->id;
            foreach ($project as $value) {

                $id_user_array = DB::table('projects')->where('id', $project->id)->select('user_id')->get();
                $id_user = $id_user_array[0]->user_id;
                if ($id_user == Auth::id()) {
                    $nameUser = DB::table('users')->where('id', $id_user)->get('name')[0]->name;
                    if ($project->user_id == $project->right_to) {
                        $value->permission = true;
                    } else {
                        $value->permission = false;
                    }

                }


//                return view('landpage.page', compact('id'))->with(['recave' => $recave])->with(['commuinty' => $commuinty])->with(['collector' => $collector]);;

//                return View::make('landpage.page', ["id"=>$id,"recave"=>$id]);

//                return view("landpage.page", ["id"=>$id , "id2"=>$id] );
                return view('landpage.page', compact('id'));
            }
        } else {
            return redirect(url('login'));

        }
    }


    public function getFileProject($id)
    {
        $id_user = Auth::id();
        $filename = Storage::disk('jsonUser')->get($id_user . '/projects/' . $id . '.json');
        return response()->json($filename);
    }




    public function getFav($id_project, $state, Request $request)
    {
        $id_user = Auth::id();
        if ($state == 'true') {
            if (DB::table('favorites')->where('user_id', $id_user)->where('id_file', $id_project)->exists()) {
                return response()->json(true);
            } else {
                $student = new Favorite();
                $student->id_file = $id_project;
                $student->user_id = $id_user;
                $student->save();
                return response()->json(DB::table('favorites')->where('user_id', $id_user)->where('id_file', $id_project)->exists());
            }

        } else if ($state == 'false') {
            DB::table('favorites')->where('id_file', $id_project)->delete();
            return response()->json(DB::table('favorites')->where('user_id', $id_user)->where('id_file', $id_project)->exists());
        }
    }



//
//        if( $project == false){
//            $student = new Favorite();
//            $student->id_file = $id;
//            $student->user_id = Auth::id();
//            $student->save();
//        }
//        return response()->json($project);
//    }

//    public function InsertFav($id){
//        $id_user = Auth::id();
//        $project = DB::table('favorites')->where('user_id',$id_user)->where('id_file',$id)->exists();
//        return response()->json($project);
//    }

    public function AddSherProject(Request $request)
    {
        $project = Project::Find($request->input('id_project'));
        if ($project) {
            if (DB::table('sher_projects')->where('owner_id', Auth::id())->where('project_id', $request->input('id_project'))->exists()) {
                return response()->json('All ready saved');
            } else {
                $student = new SherProject();
                $student->project_id = $request->input('id_project');
                $student->isCopy = $request->input('isCopy');
                $student->owner_id = Auth::id();
                $student->save();
                return response()->json('Done');
            }
        } else {
            return response()->json('Not found');
        }
    }


//    public function DeleteSherProject($id)
//    {
//
//        $getIdSherProject = DB::table('sher_projects')
//            ->where('project_id', $id)
//            ->get('id');
//
//        if (array_key_exists(0, json_decode($getIdSherProject))) {
//            $getIdSherProject = $getIdSherProject[0]->id;
//            $project = SherProject::find($getIdSherProject);
//            if ($project) {
//                $project->delete();
//                return response()->json('Done');
//            }
//        } else {
//            return response()->json('Not found');
//        }
//    }

    public function DeleteSherProject($id)
    {
        $getIdSherProject = DB::table('sher_projects')
            ->where('project_id', $id)
            ->value('id');

        if (optional(SherProject::find($getIdSherProject))->delete()) {
            return response()->json('Done');
        } else {
            return response()->json('Not found');
        }
    }


    public function getFriend($id)
    {
        $sher_reiend = SherProject::find($id);
    }


    public function AddSherFriend(Request $request)
    {
        $student = new Receive();
        $getIdFromEmailUser = DB::table('users')->where('email', $request->input('email'))->get('id');

        if (array_key_exists(0, json_decode($getIdFromEmailUser))) {
            $getIdFromEmailUser = $getIdFromEmailUser[0]->id;

            if(Auth::id() == $getIdFromEmailUser){
                return response()->json("Project not found");
            }else{
                $project = Project::Find($request->input('project_id'));
                if ($project) {
                    if (DB::table('receives')->where('user_id', $getIdFromEmailUser)->where('project_id', $request->input('project_id'))->exists()) {
                        return response()->json('All ready saved');
                    } else {
                        $student->user_id = $getIdFromEmailUser;
                        $student->project_id = $request->input('project_id');
                        $student->isCopy = $request->input('isCopy');
                        $student->save();

                        $nofiaction = new Notification();
                        $nofiaction->user_id_receive = $getIdFromEmailUser;
                        $nofiaction->type_id = Auth::id();
//                    $name_user = User::where('id', $getIdFromEmailUser)->get('name');
                        $nofiaction->text = Auth::user()->name. ' sher project to you';
                        $nofiaction->save();
                        return response()->json('Done');
                    }
                } else {
                    return response()->json("Project not found");
                }
            }

        } else {
            return response()->json("Error");
        }
    }

    public function DeleteSherFriend($id)
    {
        $student = Receive::find($id);
        if ($student) {
            $student->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Deleted Successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Found.'
            ]);
        }

    }


    ////////////////////////-------------likes---------////////////////////////
    public function addLike($user_id, $project_id)
    {

        if($user_id){
            if (DB::table('likes')->where('user_id', $user_id)->where('project_id_sher_project', $project_id)->exists()) {
                return redirect(url('community'));
            } else {
                $student = new Like();
                $student->user_id = $user_id;
                $student->project_id_sher_project = $project_id;
                $student->save();
                return redirect(url('community'));
            }
        }else{
            return redirect(url('login'));

        }



//        }else{
//            return response()->json([
//                'status'=>404,
//                'message'=>'Done'
//            ]);

    }


//    public function DeleteLike($user_id, $project_id){
//        if(Auth::id()){
//            if (DB::table('likes')->where('user_id', $user_id)->where('project_id_sher_project', $project_id)->exists()) {
//                DB::table('likes')->where('user_id', $user_id)->where('project_id_sher_project', $project_id)->delete();
//                return redirect(url('community'));
//            } else {
//                return redirect(url('community'));
//            }
//        }else{
//            return redirect(url('login'));
//
//        }
//    }


    public function DeleteLike($user_id, $project_id)
    {
        $like = Like::where('user_id', $user_id)
            ->where('project_id_sher_project', $project_id)
            ->first();

        if ($like) {
            $like->delete();
        }

        return redirect(url('community'));
    }



    public function getLikes($id_project)
    {
        $following = DB::table('likes')->where('project_id_sher_project', $id_project)->count();
        return $following;

    }


    public function index(): Application|Factory|View
    {
//        $id = Auth::id();
//        $project = DB::table('projects')->where('user_id', $id)->paginate(15);
        $lastId = DB::getPdo()->lastInsertId();

        $project = Project::all();
        return view('landpage.writinerNew', compact('project', 'lastId'));

    }

    public function index1()
    {
        return view('landpage.writiner');
    }


    public function indexDocument()
    {
        return view('landpage.Writiner_Documents');
    }

    public function indexTextEdit()
    {
        return view('landpage.EditText');
    }

    public function indexProfileFrined($id)
    {
        if(Auth::user()){

            $ck = User::Find($id);

            if($ck){
                $user = Auth::user()->Find($id);
                return view('landpage.ProfileFriend', compact('id','user'));
            }else{
                return redirect(url('writiner'));
            }
        }else{
            return redirect(url('writiner'));
        }

    }

    public function getProjectIndex()
    {
        return view('landpage.page');
    }


    /*********** Start Route *************/

//    public function PageProject($id){
//        $prject = 1;
//        return view('landpage.page', compact('id'));
//}
//$id,$json

    public function SaveProject(Request $request){
        $id = $request->input('id');
        $ck = Project::Find($id);
        if($ck){

            $id_user_ouner = DB::table('projects')->where('id', $id)->select('user_id', 'name','type')->get();
            $name_user = DB::table('users')->where('id', $id_user_ouner[0]->user_id)->select('name')->get();
            Storage::disk('jsonUser')->put( $id_user_ouner[0]->user_id . '/projects/' .$id.'.json', $request->input('json'));

            $affected = DB::table('projects')
                ->where('id', $id)
                ->update(['updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);


            return response()->json($request->input('json'));

        }else{
            return response()->json('Not found');

        }

    }



    public function setIdPage($id){
//        $salt = 12345678;
//        $input_with_salt = $salt . $id;
        $id = base64_decode($id);

        $sher_project = Project::Find($id);
        if($sher_project){
            $id_user_ouner = DB::table('projects')->where('id', $sher_project->id)->select('user_id', 'name','type')->get();
            $name_user = DB::table('users')->where('id', $id_user_ouner[0]->user_id)->select('name')->get();
            $filename = Storage::disk('jsonUser')->get($id_user_ouner[0]->user_id . '/projects/' . $sher_project->project_id . '.json');
            $type = $id_user_ouner[0]->type;

            $receive = 0;
            $commuinty = 0;
            $collector = 0;
            $project=$id;
            $linkBack='recent';
            return view('landpage.page', compact('receive','type','commuinty','collector','project','linkBack'));
        }else{
            return redirect(url('login'));
        }
    }

    public function PageProject($id)
    {
        $project = Project::Find($id);
        if ($project) {
            $id_user_array = DB::table('projects')->where('id', $project->id)->select('user_id')->get();
            $id_user = $id_user_array[0]->user_id;

            if ($id_user == Auth::id()) {

                $filename = Storage::disk('jsonUser')->get($id_user . '/projects/' . $project->id . '.json');
                $nameUser = Auth::user()->name;
                if ($project->user_id == $project->right_to) {
                    $permission = true;
                } else {
                    $permission = false;
                }

                return response()->json([
                    'id' => $project->id,
                    'user_id' => $id_user,
                    'name_project' => $project->name,
                    'name_user' => $nameUser,
                    'file' => $filename,
                    'permission' => $permission,
                    'read_only'=> false,
                    'type'=> $project->type,

                ]);
            } else {
                return response()->json('Access is not authorized');
            }
        } else {
            return response()->json('Not found');
        }
    }


    public function setIdReceive($id){
        $id = base64_decode($id);
        $sher_project = Receive::Find($id);

        if($sher_project){
            $id_user_ouner = DB::table('projects')->where('id', $sher_project->project_id)->select('user_id', 'name','type')->get();
            $name_user = DB::table('users')->where('id', $id_user_ouner[0]->user_id)->select('name')->get();
            $filename = Storage::disk('jsonUser')->get($id_user_ouner[0]->user_id . '/projects/' . $sher_project->project_id . '.json');
            $type = $id_user_ouner[0]->type;

            $receive = $id;
            $commuinty = 0;
            $collector = 0;
            $project=0;
            $linkBack='receive';
            return view('landpage.ReadOnlyText', compact('receive','type','commuinty','collector','project','linkBack'));
        }else{
            return redirect(url('login'));

        }


    }

    public function PageReceive($id)
    {
        $receive = Receive::Find($id);
        if ($receive) {
            $id_user_array = DB::table('receives')->where('id', $id)->select('user_id')->get();
            $id_user = $id_user_array[0]->user_id;

            if ($id_user == Auth::id()) {

                $receive = DB::table('receives')->where('id', $id)->where('user_id', Auth::id())->select('project_id')->get();
                $project_id = $receive[0]->project_id;
                $id_user_ouner = DB::table('projects')->where('id', $project_id)->select('user_id', 'name','type')->get();
                $name_user = DB::table('users')->where('id', $id_user_ouner[0]->user_id)->select('name')->get();
                $filename = Storage::disk('jsonUser')->get($id_user_ouner[0]->user_id . '/projects/' . $project_id . '.json');
                return response()->json([
                    'name_project' => $id_user_ouner[0]->name,
                    'name_user' => $name_user[0]->name,
                    'file' => $filename,
                    "read_only"=> true,
                    "permission"=> false,
                    'type'=> $id_user_ouner[0]->type,
                ]);
            } else {
                return response()->json('Access is not authorized');

            }


        } else {
            return response()->json('Not Found');
        }
    }


    public function PageCommunity($id)
    {
        $sher_project = SherProject::Find($id);

        if ($sher_project) {

            $id_user_ouner = DB::table('projects')->where('id', $sher_project->project_id)->select('user_id', 'name','type')->get();
            $name_user = DB::table('users')->where('id', $id_user_ouner[0]->user_id)->select('name')->get();
            $filename = Storage::disk('jsonUser')->get($id_user_ouner[0]->user_id . '/projects/' . $sher_project->project_id . '.json');
            return response()->json([
                'name_project' => $id_user_ouner[0]->name,
                'name_user' => $name_user[0]->name,
                'file' => $filename,
                "read_only"=> true,
                "permission"=> false,
                'type'=> $id_user_ouner[0]->type,
            ]);

        } else {
            return response()->json('Not Found');
        }
    }


    public function setIdCommuinty($id){
        $id = base64_decode($id);
        $sher_project = SherProject::Find($id);

        if($sher_project){
            $id_user_ouner = DB::table('projects')->where('id', $sher_project->project_id)->select('user_id', 'name','type')->get();
            $name_user = DB::table('users')->where('id', $id_user_ouner[0]->user_id)->select('name')->get();
            $filename = Storage::disk('jsonUser')->get($id_user_ouner[0]->user_id . '/projects/' . $sher_project->project_id . '.json');
            $type = $id_user_ouner[0]->type;
            $receive = 0;
            $commuinty = $id;
            $collector = 0;
            $project=0;
            $linkBack='commuinty';
            return view('landpage.ReadOnlyText', compact('receive','type','commuinty','collector','project','linkBack'));
        }else{
            return redirect(url('login'));
        }
    }



    public function PageCollector($id)
    {

        $ck = Collector::Find($id);

        if ($ck) {
            $ck2 = DB::table('collectors')->where('id', $id)->where('user_id', Auth::id())->exists();
            if ($ck2) {

                $collector = DB::table('collectors')->where('id', $id)->where('user_id', Auth::id())->get();
                foreach ($collector as $value) {
//            $icon = DB::table('users')->where('id', $value->user_id)->get('name');
//            $name = $icon[0]->name;
//            $value->user_name= $name;
                    $items = array();
                    $data = json_decode($value->collector);
                    if ($data == NULL) {
                        $value->collector = null;
                    } else {
                        foreach (json_decode($value->collector) as $project) {
                            $items[] = ($this->IdCollectorProjectFile($project))->original;
//            $value->$collectorArrya[]= $this->IdCollectorProject($project);
                        }
                    }
////            echo print_r($items);
                    $value->collectorArrya = $items;
                }
                return response()->json($collector[0]->collectorArrya);
            } else {
                return response()->json('Not Allow');

            }
        } else {
            return response()->json('Not found');

        }
    }


    public function setIdCollector($id)
    {        $id = base64_decode($id);

        $sher_project = Collector::Find($id);
        if ($sher_project) {
            $type  = $sher_project->type;
            $receive = 0;
            $commuinty = 0;
            $collector = $id;
            $project=0;
            $linkBack='collector';
            return view('landpage.ReadOnlyText', compact('receive','type','commuinty','collector','project','linkBack'));
        } else
            return redirect(url('login'));

    }


    public function IdCollectorProjectFile($id)
    {
        $project = Project::Find($id);
        if ($project) {
            $projects = DB::table('projects')->where('id', $id)->select('id', 'user_id','type')->get();
            foreach ($projects as $value) {
                $filename = Storage::disk('jsonUser')->get($projects[0]->user_id . '/projects/' . $id . '.json');
                $value->id_project = $projects[0]->id;
                $value->file = $filename;
                $value->permission = false;
                $value->read_only = true;
                $value->type = $projects[0]->type;
                unset($value->id);
            }
            return response()->json($projects[0]);
        } else {
            return response()->json('Not found');
        }
    }


    public function getCommunityLike($id)
    {
        $likes = DB::table('likes')->where('project_id_sher_project', $id)->count();
        return response()->json($likes);
    }

    public function addCommunityLike($id)
    {
        $community = DB::table('sher_projects')->where('project_id', $id)->select('id')->get();

        if (array_key_exists(0, json_decode($community))) {
            $community = $community[0]->id;

            if (Auth::id()) {
                if (DB::table('likes')->where('project_id_sher_project', $community)->where('user_id', Auth::id())->exists()) {
                    return response()->json('All ready saved');
                } else {
                    $like = new Like();
                    $like->user_id = Auth::id();
                    $like->project_id_sher_project = $community;
                    $like->save();
                    return response()->json('Done');
                }
            } else {
                return response()->json('Access is not authorized');
            }
        } else {
            return response()->json('Not Found');
        }
    }

    public function removeCommunityLike($id)
    {

        $community = DB::table('likes')->where('project_id_sher_project', $id)->select('project_id_sher_project')->get();

        if (array_key_exists(0, json_decode($community))) {
            $community = $community[0]->project_id_sher_project;
            if (Auth::id()) {
                if (DB::table('likes')->where('project_id_sher_project', $community)->where('user_id', Auth::id())->exists()) {
                    DB::table('likes')->where('project_id_sher_project', $community)->where('user_id', Auth::id())->select('id')->delete();
                    return response()->json('Done');
                } else {
                    return response()->json('All ready saved');
                }
            } else {
                return response()->json('Access is not authorized');
            }
        } else {
            return response()->json('Not Found');
        }

    }

// not testin
    public function getReceiveProjectToListProject($id): \Illuminate\Http\JsonResponse
    {

        $project = Receive::find($id);

        if ($project) {
            $rec = DB::table('receives')->where('id', $id)->select('project_id', 'updated_at', 'isCopy')->get()[0];
            $projectNew = DB::table('projects')->where('id', $rec->project_id)->get()[0];

            $filename = Storage::disk('jsonUser')->get($projectNew->user_id . '/projects/' . $projectNew->id . '.json');
            $pr = DB::table('projects')->insertGetId([
                'user_id' => Auth::id(),
                'name' => $projectNew->name,
                'type' => $projectNew->type,
                'right_to' => $projectNew->right_to,
                'updated_at' => $projectNew->updated_at
            ]);

            $lastId = DB::getPdo()->lastInsertId();

            $affected = DB::table('projects')
                ->where('id', $lastId)
                ->update(['path' => Auth::id() . '/projects/' . $lastId . '.json',]);
            $filename_new = Storage::disk('jsonUser')->put(Auth::id() . '/projects/' . $lastId . '.json', $filename);

            return response()->json(DB::table('projects')->where('id',$lastId)->get()[0]);
        } else {
            return response()->json('Not Found');

        }
    }
    // ToRecacev
    // not testing //
    public static function AddToReceiveFromCommunity($id_community)
    {
        $project = SherProject::find($id_community);

        if ($project) {
            $rec = DB::table('sher_projects')->where('id', $id_community)->select('project_id', 'updated_at', 'isCopy')->get()[0];
            $projectNew = DB::table('projects')->where('id', $rec->project_id)->get()[0];

            $save = new Receive();
            $save->user_id = Auth::id();
            $save->isCopy = 1;
            $save->project_id = $projectNew->id;
            $save->updated_at = $projectNew->updated_at;
            $save->save();

            $url = url()->previous();
            return redirect($url)->with('update', 'added to your projects successfully!');
        } else {
            return response()->json('Not Found');

        }
    }

    public static function getCopyLink($id_community)
    {
        $project = SherProject::find($id_community);

        if ($project) {
            $rec = DB::table('sher_projects')->where('id', $id_community)->get('id')[0];
            $id_project = $rec->id;
            return response()->json('http://127.0.0.1:8000/page-community/' . $id_project);

        } else {
            return response()->json('Not Found');

        }
    }


    // not testing
    //toProject
    public static function AddToShareFromCommunity($id_community)
    {
        $project = SherProject::find($id_community);

        if ($project) {
            $rec = DB::table('sher_projects')->where('id', $id_community)->select('project_id', 'updated_at', 'isCopy')->get()[0];
            $projectNew = DB::table('projects')->where('id', $rec->project_id)->get()[0];

            $filename = Storage::disk('jsonUser')->get($projectNew->user_id . '/projects/' . $projectNew->id . '.json');
            $pr = DB::table('projects')->insertGetId([
                'user_id' => Auth::id(),
                'name' => $projectNew->name,
                'type' => $projectNew->type,
                'right_to' => $projectNew->right_to,
                'updated_at' => $projectNew->updated_at,
                'created_at'=> Carbon::now()

            ]);

            $lastId = DB::getPdo()->lastInsertId();

            $affected = DB::table('projects')
                ->where('id', $lastId)
                ->update(['path' => Auth::id() . '/projects/' . $lastId . '.json',]);
            $filename_new = Storage::disk('jsonUser')->put(Auth::id() . '/projects/' . $lastId . '.json', $filename);
            $url = url()->previous();
            return redirect($url)->with('update', 'copy project successfully!');
        } else {
            return response()->json('Not Found');
        }
    }


    /*********** End Route *************/


    /************************ Strat *****************/

//    // type project(){
//
//}





    /********* Start Collector *********/

    public function getCollector(){
        $collector= DB::table('collectors')->where('user_id',Auth::id())->get();
//       $collector = $collector[0];
//        $collectorArray=  json_decode($collector[1]->collector);
//
//        $items = array_values ( $collectorArray );
//
        foreach ($collector as $value) {
//            $hhh =$collector[0]->collector;
//            $value->hhh= $hhh;

            $icon = DB::table('users')->where('id', $value->user_id)->get('name');
            $name = $icon[0]->name;
            $value->user_name= $name;
            $items= array();

            $data =json_decode($value->collector);
            if($data == NULL){
                $value->collector = null;
            }else{
                foreach (json_decode($value->collector) as $project){
                    $items[]=($this->IdCollectorProject($project))->original;
//            $value->$collectorArrya[]= $this->IdCollectorProject($project);
                }
            }

//            echo print_r($items);
            $value->collectorArrya= $items;

            unset($value->user_id);
            unset($value->collector);


        }

        return response()->json($collector);

//
//            $itemsNew = array();
//            $i=0;
//            for ($i; $i < count(json_decode($collector)); $i++) {
//                $itemsNew[] = array_values(json_decode($collector[$i]->collector));
//                $value->collectorArray = $itemsNew;
//            }
//        }
////            $i = 0;
////            for ($i; $i < count($items); $i++) {
////                $val = $items[$i];
////                $a = $this->IdCollectorProject($val); //may you was intended to pass $val here?
////                $itemsNew[] = $a;
////            }
////            $value->collectorArray = $itemsNew;
//        return response()->json($collector);

    }


    public function IdCollectorProject($id){
        $project = Project::Find($id);
        if($project){
            return response()->json([
                'id_project'=>$project->id,
                'name_project'=>$project->name,
                'type'=>$project->type,
                'updated_at'=>$project->updated_at]);
        }else{
            return response()->json('Not found');
        }
    }

//    public function Rename

    public function RenameCollector($id,$rename){

        $collector = Collector::Find($id);
        if($collector){
            if (Auth::id() == $collector->user_id){
                $ck= DB::table('collectors')
                    ->where('id',$id)
                    ->update([
                        'name' =>$rename,
                    ]);
                return response()->json(DB::table('collectors')->where('id',$id)->select('name')->get()[0]->name);
            }else{
                return response()->json('Access is not authorized');
            }
        }else{
            return response()->json('Not found');

        }

    }


    public function DeleteCollector($id){

        $collector = Collector::Find($id);
        if($collector){
            if (Auth::id() == $collector->user_id){
                DB::table('collectors')->where('id',$id)->where('user_id',Auth::id())->delete();
                return response()->json('Done');
            }else{
                return response()->json('Access is not authorized');
            }
        }else{
            return response()->json('Not found');

        }
    }


    public function UpdateCollector($id,$json){
        $collector = Collector::Find($id);
        if($collector){
            if (Auth::id() == $collector->user_id){
                $ck= DB::table('collectors')
                    ->where('id',$id)
                    ->update([
                        'collector' =>$json,
                    ]);
                return response()->json('Done');
            }else{
                return response()->json('Access is not authorized');
            }
        }else{
            return response()->json('Not found');

        }
    }

    /*Request $request*/
    public function CreateNewCollector(Request $request){

        $student = new Collector();
        $student->user_id = Auth::id();
        $student->name =$request->input('name');
        $student->type =$request->input('type');
        $student->save();
//        return response()->json($student);



        //======================
        $collector= DB::table('collectors')->where('id',$student->id)->get();
//       $collector = $collector[0];
//        $collectorArray=  json_decode($collector[1]->collector);
//
//        $items = array_values ( $collectorArray );
//
        foreach ($collector as $value) {
//            $hhh =$collector[0]->collector;
//            $value->hhh= $hhh;

            $icon = DB::table('users')->where('id', $value->user_id)->get('name');
            $name = $icon[0]->name;
            $value->user_name= $name;
            $items= array();

            $data =json_decode($value->collector);
            if($data == NULL){
                $value->collector = null;
            }else{
                foreach (json_decode($value->collector) as $project){
                    $items[]=($this->IdCollectorProject($project))->original;
//            $value->$collectorArrya[]= $this->IdCollectorProject($project);
                }
            }

//            echo print_r($items);
            $value->collectorArrya= $items;

            unset($value->user_id);
            unset($value->collector);
            unset($value->created_by);
            unset($value->created_at);
        }
        return response()->json($collector);
    }

    public function getCollectorProject(Request $request){
        $id = Auth::id();
        $project = DB::table('projects')->where('user_id',$id)->where('type',$request->input('type'))->get();
        return response()->json($project);
    }

    public function getCollectorProjectUsingGet($type){
        $id = Auth::id();
        $project = DB::table('projects')->where('user_id',$id)->where('type',$type)->select('id as id_project','name','type','updated_at')->get();
        $receive = $this->getCollectorReceivesUsingGet($type);

        return response()->json(['projects'=>$project,'receives'=>$receive->original]);
    }

    public function getCollectorReceivesUsingGet($type){
        $id = Auth::id();

        $data = DB::table('projects')
            ->join('receives', 'projects.id', '=', 'receives.project_id')->where('receives.user_id', $id )->where('projects.type',$type)
            ->select('projects.type', 'projects.name','projects.id as id_project', 'projects.updated_at')
            ->get();

        return response()->json($data);
    }

    /********* End Collector *********/



    /******** Start Ready Text Edit ********/

    public function getReadyText(){
        $id = Auth::id();
        $ready_text = DB::table('ready_text_edits')->where('user_id',$id)->get();
        return response()->json($ready_text);

    }

    public function addReadyText($json){

        $id = Auth::id();
        $state =  DB::table('ready_text_edits')->where('user_id',$id)->exists();
        if($state) {
            return response()->json('All ready saved');
        }else{

            $data = [];

            $data = array (
                0 =>
                    array (
                        'name' => 'Untiteld',
                        'bold' => false,
                        'italic' => false,
                        'underline' => false,
                        'strikethrough' => false,
                        'backColor' => '#ffffff',
                        'fontSize' => 1,
                        'fontName' => 'Sans-serif',
                        'foreColor' => '#000000',
                        'justifyCenter' => false,
                        'justifyLeft' => false,
                        'justifyRight' => false,
                        'justifyFull' => false,
                        'subscript' => false,
                        'superscript' => false,
                        'link' => NULL,
                        'container' =>
                            array (
                            ),
                        'nameVeiw' =>
                            array (
                            ),
                    ),
                1 =>
                    array (
                        'name' => 'Untiteld',
                        'bold' => false,
                        'italic' => false,
                        'underline' => false,
                        'strikethrough' => false,
                        'backColor' => '#ffffff',
                        'fontSize' => 1,
                        'fontName' => 'Sans-serif',
                        'foreColor' => '#000000',
                        'justifyCenter' => false,
                        'justifyLeft' => false,
                        'justifyRight' => false,
                        'justifyFull' => false,
                        'subscript' => false,
                        'superscript' => false,
                        'link' => NULL,
                        'container' =>
                            array (
                            ),
                        'nameVeiw' =>
                            array (
                            ),
                    ),
            );


            $ready = new Ready_text_edit();
            $ready->user_id = $id;
            $ready->json = json_decode($data);
            $ready->save();
            return response()->json(DB::table('ready_text_edits')->where('user_id',$id)->get());
        }
    }

    public function updateReadyText($json){
        $id_user = Auth::id();
        $state =  DB::table('ready_text_edits')->where('user_id',$id_user)->exists();




        if($state) {
            $id =  DB::table('ready_text_edits')->where('user_id',$id_user)->get('id');


            DB::table('ready_text_edits')
                ->where('id',$id[0]->id)
                ->update([
                    'json' =>json_encode($json),
                ]);
            return response()->json(DB::table('ready_text_edits')->where('id',$id[0]->id)->get());
        }else{
            return response()->json('Not found');
        }
    }

    public function deleteReadyText(){
        $id_user = Auth::id();
        $state =  DB::table('ready_text_edits')->where('user_id',$id_user)->exists();
        if($state) {
            $id =  DB::table('ready_text_edits')->where('user_id',$id_user)->get('id');
            DB::table('ready_text_edits')->where('id',$id[0]->id)->delete();
            return response()->json('Done');
        }else{
            return response()->json('Not found');
        }

    }

    /******** End Ready Text Edit ********/



    /******** Search Email User ***********/

    public function SearchUsers($email){

        $users =User::where('email','LIKE',"{$email}%")->select('id as user_id','email')->get();
        return response()->json($users);
    }

    public function SearchCommunity($name_project){

//        $users =SherProject::where('name','LIKE',"{$name_project}%")->select('id as user_id','email')->get();

        $data = DB::table('projects')
            ->join('sher_projects', 'projects.id', '=', 'sher_projects.project_id')
            ->where('projects.name','LIKE',"{$name_project}%")->get();

        return response()->json($data);
    }



    /************ Community ***********/


    public static function getCommuitny(){
        // we need to know name project and id and type and created_at and dec and likes
        $commuinty = SherProject::all();
        foreach ($commuinty as $value){

            $type = DB::table('sher_projects')->where('project_id', $value->project_id)->where('owner_id',$value->owner_id)->select('isCopy','id')->get();
            $name_project = DB::table('projects')->where('id',$value->project_id)->get();
            if (array_key_exists(0, json_decode($name_project))) {
                $value->name = $name_project[0]->name;
            }
            if (array_key_exists(0, json_decode($type))) {
                if( $type[0]->isCopy  == 1){
                    $value->isCopy = true;
                }else{
                    $value->isCopy = false;
                }
            }
            $value->isLike = DB::table('likes')->where('project_id_sher_project',$type[0]->id)->where('user_id',Auth::id())->exists();

            //            $value->isLike = DB::table('likes')->where('id',7)->where('user_id',Auth::id())->exists();
            $value->type = $name_project[0]->type;
            $value->created_at = $name_project[0]->created_at;
            $value->dec = $name_project[0]->dec;
            $value->likes = (new static)->getLikes($type[0]->id);
            unset($value->updated_at);
        }
        return $commuinty;
    }

    public function AddToMyProjects($id){

    }
    public function CopySherProject($id){

    }

    public function getImage(){

    }
    public function UploadImage(){

    }
    public function getLinkAndSaveImage(){

    }




    public function fetchcurd()
    {
        $id = Auth::id();

        $project = DB::table('projects')->where('user_id', $id)->get();

        foreach ($project as $value) {
            $object = DB::table('favorites')->where('id_file', $value->id)->where('user_id', $id)->exists();
            unset($value->path);
            $value->Isfavorite = $object;
            $sher_project = DB::table('sher_projects')->where('project_id', $value->id)->where('owner_id', $id)->exists();
            $value->IsSher_project = $sher_project;
            $type = DB::table('sher_projects')->where('project_id', $value->id)->where('owner_id', $id)->get('isCopy');
            if (array_key_exists(0, json_decode($type))) {
                if( $type[0]->isCopy  == 1){
                    $value->isSherCopy = true;
                }else{
                    $value->isSherCopy = false;
                }
            }

            if($value->user_id == $value->right_to){
                $value->isProjectCopy = false;
                $value->icon = null;
            }else{
                $value->isProjectCopy = true;

                $icon = DB::table('users')->where('id', $value->right_to)->get('profile_photo_path')[0]->profile_photo_path;

                if($icon == null){
                    $icon = DB::table('users')->where('id', $value->right_to)->get('name');
                    $name = $icon[0]->name;
//                    $value->icon = "https://ui-avatars.com/api/?name=$name&color=7F9CF5&background=EBF4FF";
                    $value->icon = null;

                }else{
//                    $value->icon = "/storage/$icon";
                    $value->icon = null;

                }

            }


            $id_like = DB::table('sher_projects')->where('project_id', $value->id)->where('owner_id', $id)->get('id');

            if (array_key_exists(0, json_decode($id_like))) {
                $get_like = $this->getLikes($id_like[0]->id);
                $value->likes =$get_like;
            }else{
                $value->likes = null;
            }

        }
        $data = $this->ReseveProject();
        return response()->json(['projects'=>$project,'receives'=>$data]);
    }


    public function ReseveProject()
    {
        $id = Auth::id();
        $data = DB::table('projects')
            ->join('receives', 'projects.id', '=', 'receives.project_id')->where('receives.user_id', $id )
            ->select('projects.*', 'receives.id','receives.user_id', 'receives.project_id','receives.isCopy')
            ->get();

        foreach ($data as $value) {
            $icon = DB::table('users')->where('id', $value->user_id)->get('name');
            $name = $icon[0]->name;

            $isCopyFlaf = DB::table('receives')->where('id', $value->id)->get('isCopy');
            unset($value->path);
            if (array_key_exists(0, json_decode($isCopyFlaf))) {
                if($isCopyFlaf[0]->isCopy == 1){
                    $value->isCopy = true;

                }else{
                    $value->isCopy = false;

                }
            }

            $value->icon = "https://ui-avatars.com/api/?name=$name&color=7F9CF5&background=EBF4FF";

            $object = DB::table('favorites')->where('id_file', $value->project_id)->where('user_id', $id)->exists();
            $value->Isfavorite = $object;

            $sher_project = DB::table('sher_projects')->where('project_id', $value->project_id)->where('owner_id', $id)->exists();
            $value->IsSher_project = $sher_project;

            $type = DB::table('sher_projects')->where('project_id', $value->project_id)->where('owner_id', $id)->get('isCopy');
            if (array_key_exists(0, json_decode($type))) {
                if( $type[0]->isCopy  == 1){
                    $value->isSherCopy = true;
                }else{
                    $value->isSherCopy = false;
                }
            }

            $id_like = DB::table('sher_projects')->where('project_id', $value->project_id)->where('owner_id', $id)->get('id');

            if (array_key_exists(0, json_decode($id_like))) {
                $get_like = $this->getLikes($id_like[0]->id);
                $value->likes =$get_like;
            }else{
                $value->likes = null;
            }
            unset($value->project_id);

        }
        return $data;
    }

    public function DeleteProject($id,$value){
        if($value == 1){
            $receive = Receive::find($id);
            if($receive)
            {
//                $id_user = Auth::id();
                $receive->delete();
                if(DB::table('receives')->where('id',$id)->exists()){
                    return response()->json('wrong');
                }else{
                    return response()->json('done');
                }
            }
            else
            {
                return response()->json('notExist');
            }
        }else if ($value == 0){
            $receive = Project::find($id);
            if($receive)
            {
                $id_user = Auth::id();
                $path="users/".$id_user."/projects/".$receive->id.".json";
                Storage::delete($path);
                DB::table('projects')->where('id', $id)->where('user_id',Auth::id())->delete();
                if(DB::table('projects')->where('id',$id)->exists()){
                    return response()->json('wrong');
                }else{
                    return response()->json('done');
                }
            }
            else
            {
                return response()->json('notExist');
            }
        }
    }

    public function RenameProject($id,$rename){

        $student = Project::find($id);
        if($student)
        {
            $student->name = $rename;
            $student->update();
            return response()->json( $student->name );
        }
        else
        {
            return response()->json( false);
        }

    }

    public function EditDecProject($id,$dec){

        $student = Project::find($id);
        if($student)
        {
            $student->dec = $dec;
            $student->update();
            return response()->json($student->dec);
        }
        else
        {
            return response()->json("Not exist");
        }

    }



    function getDecProject(Request $request){
        $project = Project::find($request->input('id'));
        if($project) {
            if($project == null){
                return response()->json("Not exist");

            }else{
                return response()->json($project->dec);

            }

        }
        else
        {
            return response()->json(false);
        }
    }


    public static function getNameProject($id){
        $friends = DB::table('projects')->where('id', $id)->get('name');
        return $friends[0]->name;

    }


    function getFriends()
    {
        $friends = DB::table('friends')->where('user_id', Auth::id())->select('id','user_id_friend')->get();
        foreach ($friends as $value) {
            $friend_info = DB::table('users')->where('id', $value->user_id_friend)->select('email','name','profile_photo_path')->get();
            $value->info = $friend_info[0];

            $icon = DB::table('users')->where('id', $value->user_id_friend)->get('profile_photo_path')[0]->profile_photo_path;
            if($icon == null){
                $icon = DB::table('users')->where('id', $value->user_id_friend)->get('name');
                $name = $icon[0]->name;
                $value->icon = "https://ui-avatars.com/api/?name=$name&color=7F9CF5&background=EBF4FF";

            }else{
                $value->icon = "/storage/$icon";

            }

        }
        return response()->json($friends);
    }

    function AddFriends($id)
    {

        $fr = User::find($id);

        if ($fr) {

            if(DB::table('friends')->where('user_id',Auth::id())->where('user_id_friend',$id)->exists()){
//                return response()->json('added');
                return redirect(url('/profile-friend/'.$id));


            }else{
                $student = new Friend();
                $student->user_id_friend = $id;
                $student->user_id =Auth::id();
                $student->save();

                $nofiaction = new Notification();
                $nofiaction-> user_id_receive = $id;
                $nofiaction->type_id = Auth::id();
//                $name_user = User::where('id',$id)->get('name');
                $nofiaction->text = Auth::user()->name .' has followed you';
                $nofiaction->save();

                return redirect(url('/profile-friend/'.$id));

            }


        } else{
            return response()->json('not found');

        }
    }

    function DeleteFreind($id){
        $fr = User::find($id);

        if ($fr) {
            if(DB::table('friends')->where('user_id',Auth::id())->where('user_id_friend',$id)->exists()){
//                return response()->json('added');
                DB::table('friends')->where('user_id',Auth::id())->where('user_id_friend',$id)->delete();
                return redirect(url('/profile-friend/'.$id));
            }

        } else{
            return response()->json('not found');

        }
    }

    public static function ifFollowOrNot($id){
        $ck= DB::table('friends')->where('user_id',Auth::id())->where('user_id_friend',$id)->exists();

        if($ck){
            return true;
        }else{
            return false;
        }
    }

    function getCountFollower(Request $request){

        $following = DB::table('friends')->where('user_id',$request->input('id_user'))->count();
        $follower = DB::table('friends')->where('user_id_friend', $request->input('id_user'))->count();

        return response()->json([
            'following'=>$following,
            'follower'=>$follower
        ]);
    }

    public static  function getCountFollowerFromId($id){

        $following = DB::table('friends')->where('user_id',$id)->count();
        $follower = DB::table('friends')->where('user_id_friend', $id)->count();

        return [$following,$follower];
    }

    public static function getCountFollowerDiract()
    {

        $following = DB::table('friends')->where('user_id',Auth::id())->count();
        $follower = DB::table('friends')->where('user_id_friend', Auth::id())->count();

        return [$following,$follower];
    }

    public static function getIcon(){


        $icon = DB::table('users')->where('id', Auth::id())->get('profile_photo_path')[0]->profile_photo_path;

        if($icon == null){
            $icon = DB::table('users')->where('id', Auth::id())->get('name');
            $name = $icon[0]->name;
            $icon_user = "https://ui-avatars.com/api/?name=$name&color=7F9CF5&background=EBF4FF";

        }else{
            $icon_user = "/storage/$icon";

        }
        return $icon_user;

    }

    public static function getIconWithAuth($id){

        $icon = DB::table('users')->where('id', $id)->get('profile_photo_path')[0]->profile_photo_path;

        if($icon == null){
            $icon = DB::table('users')->where('id', $id)->get('name');
            $name = $icon[0]->name;
            $icon_user = "https://ui-avatars.com/api/?name=$name&color=7F9CF5&background=EBF4FF";

        }else{
            $icon_user = "/storage/$icon";

        }

        return $icon_user;

    }



    function getSimpleData(){
        $id = Auth::id();
        $project = DB::table('projects')->where('user_id', $id)->select('id','type','name','updated_at')->get();

        /************************************/


        $receive = DB::table('receives')->where('user_id', $id)->get('project_id');
        foreach ($receive as $value) {
            $receive_id = DB::table('projects')->where('id', $value->project_id)->select('type','name','updated_at')->get();
            $value->info = $receive_id[0];
        }

        return response()->json(['projects'=>$project,'receives'=>$receive]);
    }


    public static function getOneSherProject($id){

    }


//    public function getNotification(){
//        $id = Auth::id();
//        $project = DB::table('notifications')->where('user_id_receive', $id)->where('type','project')->select('id','text','user_id_receive')->get();
//        $friend = DB::table('notifications')->where('user_id_receive', $id)->where('type','friend')->select('id','text','user_id_receive')->get();
//        $system = DB::table('notifications')->where('user_id_receive', $id)->where('type','')->select('id','text','user_id_receive')->get();
//
//        return response()->json(['projects'=>$project,'friends'=>$friend,'others'=>$system]);
//
//    }


    public function getNotification(){
        $id = Auth::id();

//        $project = DB::table('notifications')->where('user_id_receive', $id)->where('type','project')->select('id','text','user_id_receive')->get();
//        $friend = DB::table('notifications')->where('user_id_receive', $id)->where('type','friend')->select('id','text','user_id_receive')->get();
//        $system = DB::table('notifications')->where('user_id_receive', $id)->where('type','')->select('id','text','user_id_receive')->get();
//
//        return response()->json(['projects'=>$project,'friends'=>$friend,'others'=>$system]);

        $nav = DB::table('notifications')->where('user_id_receive', $id)->get();
        return response()->json($nav);
    }


    public function DeleteNotification($id){
        DB::table('notifications')->where('id',$id)->delete();
        $url = url()->previous();
        return redirect($url);
    }



    public function BackPage(){
        return redirect(url('writiner'));
    }




    public function store1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:191',
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages()
            ]);
        }
        else
        {


            $student = new Project();
            $student->name = $request->input('name');
            $student->user_id = Auth::id();

            $student->save();
            return response()->json([
                'status'=>200,
                'message'=>'Added Successfully.'
            ]);
        }

    }

    public function edit1($id)
    {
        $student = Project::find($id);
        if($student)
        {
            return response()->json([
                'status'=>200,
                'student'=> $student,
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Found.'
            ]);
        }

    }

    public function update1(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:191',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages()
            ]);
        }
        else
        {
            $student = Project::find($id);
            if($student)
            {
                $student->name = $request->input('name');
                $student->update();
                return response()->json([
                    'status'=>200,
                    'message'=>'Updated Successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'No Found.'
                ]);
            }

        }
    }

    public function destroy1($id)
    {
        $student = Project::find($id);
        if($student)
        {
            $student->delete();
            return response()->json([
                'status'=>200,
                'message'=>'Deleted Successfully.'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Found.'
            ]);
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {


        /*
        $id = Auth::id();
        $project = DB::table('projects')->where('user_id', $id)->get();
        $urls = array();                          // <-- create empty array for the urls


        foreach ($project as $projects) {
            /*
            $data = [
                "info" => [
                    "id" => $projects->id,
                    "name_project" => $projects->name,
                    "path" => $projects->path,
                ],
                "project" => [
                    "nameProject" => $projects->name,
                    "date" => date("Y/m/d"),
                    "owner" => $projects->name
                ],
            ];
            Storage::disk('jsonUser')->put($id .'/projects/'.$projects->id .'.json', json_encode($data));
        }
*/

        /*
                    $data = [];
                    foreach ($project as $projects)
                        $data[] = [
                            "info" => [
                                "id" => $projects->id,
                                "name" => $projects->name,
                            ],
                            "project" => [
                                "nameProject" => "example",
                                "date" => date("Y/m/d"),
                                "owner" => $projects->name
                            ],
                        ];
                    Storage::disk('jsonUser')->put($id . '/projects/' . $projects->name . '.json', json_encode($data));
                    return $data;
        */

//        return view('projects.create'); // page html path(.../project/create)
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return RedirectResponse
     */
    public function store($type): RedirectResponse
    {
        DB::beginTransaction();
        User::where('id', Auth::id())->lockForUpdate()->first();

        $id = Auth::id();

        if (Auth::user()) {
            $pr =DB::table('projects')->insertGetId([
                'user_id' => $id,
                'name' => 'Untitled',
                'type' => $type,
                'right_to' => $id,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
            $lastId = DB::getPdo()->lastInsertId();
            $affected = DB::table('projects')
                ->where('id', $lastId)
                ->update(['path' => $id . '/projects/' .  $lastId . '.json',]);

            $data = [];
            $data =array (
                'page' =>
                    array (
                    ),
                'paragraphs' =>
                    array (
                    ),
                'topics' =>
                    array (
                    ),
                'image' =>
                    array (
                    ),
                'icons' =>
                    array (
                    ),
                'lists' =>
                    array (
                    ),
                'tables' =>
                    array (
                    ),
                'topicLists' =>
                    array (
                    ),
                'shapes' =>
                    array (
                    ),
                'pagenContener' =>
                    array (
                    ),
                'pageNumber' => 'none',
                'pageNumberColor' => '#000000',
                'pageDesign' => 'none',
                'backGrounColor' => '#ffffff',
                'borderColor' =>
                    array (
                        0 => '#000000',
                        1 => '#000000',
                        2 => '#000000',
                        3 => '#000000',
                        4 => '#000000',
                    ),
                'borderDesign' => 'none',
                'borderStyle' =>
                    array (
                        0 => 'solid',
                        1 => 'solid',
                        2 => 'solid',
                        3 => 'solid',
                        4 => 'solid',
                    ),
                'borderWidth' =>
                    array (
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    ),
                'borderRadius' =>
                    array (
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    ),
                'isDesignEditable' => true,
                'isContentEditable' => true,
                'pages' =>
                    array (
                        0 =>
                            array (
                                'page' =>
                                    array (
                                    ),
                                'paragraphs' =>
                                    array (
                                    ),
                                'topics' =>
                                    array (
                                    ),
                                'image' =>
                                    array (
                                    ),
                                'icons' =>
                                    array (
                                    ),
                                'lists' =>
                                    array (
                                    ),
                                'tables' =>
                                    array (
                                    ),
                                'topicLists' =>
                                    array (
                                    ),
                                'shapes' =>
                                    array (
                                    ),
                                'pagenContener' =>
                                    array (
                                    ),
                                'pageNumber' => 'none',
                                'pageNumberColor' => '#000000',
                                'pageDesign' => 'none',
                                'backGrounColor' => '#ffffff',
                                'borderColor' =>
                                    array (
                                        0 => '#000000',
                                        1 => '#000000',
                                        2 => '#000000',
                                        3 => '#000000',
                                        4 => '#000000',
                                    ),
                                'borderDesign' => 'none',
                                'borderStyle' =>
                                    array (
                                        0 => 'solid',
                                        1 => 'solid',
                                        2 => 'solid',
                                        3 => 'solid',
                                        4 => 'solid',
                                    ),
                                'borderWidth' =>
                                    array (
                                        0 => 0,
                                        1 => 0,
                                        2 => 0,
                                        3 => 0,
                                        4 => 0,
                                    ),
                                'borderRadius' =>
                                    array (
                                        0 => 0,
                                        1 => 0,
                                        2 => 0,
                                        3 => 0,
                                        4 => 0,
                                    ),
                                'isDesignEditable' => true,
                                'isContentEditable' => true,
                            ),
                    ),
            );

            Storage::disk('jsonUser')->put($id . '/projects/' .$lastId.'.json', json_encode($data));
            DB::commit();

            $receive =0;
            $commuinty = 0;
            $collector=0;
            $project=$lastId;


            return redirect()->to('page-project-id/'.base64_encode($lastId))->with( ['receive' => $receive,'commuinty'=>$commuinty , 'collector'=>$collector,'project'>$project,'type'>$type] );

        } else {
            return redirect('/login');
        }


//        $book = new Project();
//        $book->user_id = $request->user_id;
//        $book->name = $request->name;
//        $book->path = $request->path;
//        $book->save();
//        return response()->json($book);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Support\Collection
     */
    public function show($id)
    {
        $project = Project::find($id);
        return $project;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function edit($id): \Illuminate\Http\JsonResponse
    {

        $project = Project::all()->where('id',$id)->first();;

        return response()->json([
            'success'=>true,
            'project'=> $project
        ]);



    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, Project $project,$id)
    {


        /*
        $validator =  validator::make($request->all(),[
            'name' => 'required',
            ]);

        if ($validator->fails()){
            return $request->name;
          //  return response()->json($validator->errors(),400);
        }

        $project = Project::all()->where('id',$id)->first();
        $name = $request->name;
        $project->update($name);

        return response()->json([
            'success'=>true,
            'msg'=> 'Success Updated post'
        ]);
*/

        $userData = Project::find($id);
        $userData->name = request('name');
        $userData->save();

        /*    $book = new Project();
            $book->user_id = $request->user_id;
           $name = $request->name;
            $book->path = $request->path;

            DB::table('projects')->where('id', 87)->update('gggg');
    */

        return response()->json($userData);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return RedirectResponse
     */
    public function destroy(Project $project)
    {
        $id = Auth::id();
        $path="users/".$id."/projects/".$project->name.".json";
        Storage::delete($path);

        $project->delete();


        return redirect()->route('project.index')
            ->with('success','User deleted successfully');

    }

    public function updateData(UpdateProjectRequest $request, Project $project,$id): \Illuminate\Http\JsonResponse
    {

        $userData = Project::find($id);
        $userData->name = request('name');
        $userData->save();
        return response()->json($userData);

    }


    public function page($id){

    }

}
