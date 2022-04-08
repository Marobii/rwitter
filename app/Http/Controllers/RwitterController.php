<?php

namespace App\Http\Controllers;

use App\Models\UserActivity as ModelsUserActivity;
use Illuminate\Http\Request;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Mod;
use Symfony\Component\Console\Logger\ConsoleLogger;

class RwitterController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {

            $qas = ModelsUserActivity::orderBy('position', 'desc')->get();
            return view('/rwitterhome',compact('qas'));

    }

    //Store
    public function store(Request $request)
    {
        $paper = new ModelsUserActivity();

        $paper->message = request('message');

        $lastinsertedid = ModelsUserActivity::max('id');

        /* Ternário para saber se o último id é nulo, se for atribui 0 senão atribui o valor de $lastinsertedid */
        !empty($lastinsertedid) ? $lastposition = $lastinsertedid : $lastposition = 0;

        $newposition = $lastposition + 1;

        $paper->position = $newposition;

        $msg = $_POST['message'];

        /* Verifica se o rwitt é null */
        if ($msg == "") {
            /* Informa que não é possível escrever rwitt em branco */
            return redirect('/rwitterhome')->with('info', 'Escreve um Rwitt');
        } else {
            /* Informa que o rwitt foi publicado */
            $paper->save();

            return redirect('/rwitterhome')->with('info', 'Rwitter enviado com sucesso');
        }

    }

    /* Apaga da db de acordo com o id */
    public function destroy($id){
        $bb = ModelsUserActivity::findOrFail($id);
        $bb->delete();

        return redirect('/rwitterhome');
    }

    /* Move Up */
    public function funfat($id){
        $grabinfo = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();

        /* Descobrir id e posição do feed que queremos subir */
        $grabid =  $grabinfo[0]['id'];
        $grabposition =  $grabinfo[0]['position'];

        $maxposition =  ModelsUserActivity::max('position');
        $maxid = ModelsUserActivity::select('id')
            ->where('position', $maxposition)
            ->get();

        $comp = $maxid[0]['position'];

        /* if para saber se o feed que queremos subir já está no topo */
        if ($grabposition < $maxposition){
            /* Não se encontra no topo então */

            /* Nova posição */
                $temp = $grabposition + 1;
            /* Posição anterior = $grabposition */

            /* Diz-me o teu id */
            $giveid = ModelsUserActivity::select('id')
            ->where('position', $temp)
            ->get();

            $ido = $giveid[0]['id'];

            $giveposition = ModelsUserActivity::select('position')
            ->where('id', $ido)
            ->get();

            $feedaboveposition = $giveposition[0]['position'];

            ModelsUserActivity::where("id", $grabid)->update(["position" => $feedaboveposition]);
            ModelsUserActivity::where("id", $ido)->update(["position" => $grabposition]);
            /*
            Já sei id e posição do que queremos mexer
            Já sei o id do que queremos mexer
            Falta o id
             */
            error_log("entrei");

        } else {
            /* Encontra-se no topo */
            error_log("não entrei");
        }

        /*  id position
                    9    1
                    10   2
                    11   3
                    12   4
        */


/*         error_log($max);
        error_log($maxposition);
        error_log($comp); */




         /* Agarra current id
            Descobre posição do current id

            descobrir id da posição acima
            armazenar posição acima
          */

        return redirect('/rwitterhome')->with('info');
    }


    /* Move Down */
    public function funfadown($id){
        $rw = null;
        /* Indica posição do id selecionado*/
        $lowestposition = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();

        /* Recebe todas posições */
        $checkallpositions = ModelsUserActivity::select('position')
            ->get();

        /* Entra no array e diz o id */
        $grabid =  $lowestposition[0]['id'];

        /* Entra no array e diz a posição */
        $grabposition =  $lowestposition[0]['position'];



        /*
        Posição do id selecionado = lowestposition [{"id":5,"position":4}]
        Id que queremos descer = grab [{"position":4}]
        grabid = 5
        grabposition = 4
         */
        return redirect('/rwitterhome')->with('info', $checkallpositions);
    }
}
/* update */
/* ModelsUserActivity::where("id", $id)->update(["position" => $receive]); */

/* Select */
/* $downgrade = ModelsUserActivity::select('id')
                ->where('position', $receive)
                ->get(); */
