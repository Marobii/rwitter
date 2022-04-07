<?php

namespace App\Http\Controllers;

use App\Models\UserActivity as ModelsUserActivity;
use Illuminate\Http\Request;
use DB;
use Database\Factories\UserFactory;
use PhpParser\Node\Expr\AssignOp\Mod;

class RwitterController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {

            $qas = ModelsUserActivity::orderBy('id', 'desc')->get();
            return view('/rwitterhome',compact('qas'));

    }

    //Store
    public function store(Request $request)
    {
        $paper = new ModelsUserActivity();


        $paper->message = request('message');

        $msg = $_POST['message'];

        /* Verifica se o rwitt é null */
        if ($msg == "") {
            /* Informa que não é possível escrever rwitt em branco */
            return redirect('/rwitterhome')->with('info', 'Escreve um Rwitt');
        } else {
            /* Informa que o rwitt foi publicado */
            $paper->save();
            error_log($msg);
            return redirect('/rwitterhome')->with('info', 'Rwitter enviado com sucesso');
        }

    }

    /* Apaga da db de acordo com o id */
    public function destroy($id){
        $bb = ModelsUserActivity::findOrFail($id);
        $bb->delete();

        return redirect('/rwitterhome');
    }

}
