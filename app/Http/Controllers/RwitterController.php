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
        /* Agarrar na informação do feed que queremos mover */
        $grabinfo = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();

        /* Retirar do array os valores do id que queremos mover e guardar nas respetivas variaveis */
        $grabid =  $grabinfo[0]['id'];
        $grabposition =  $grabinfo[0]['position'];

        /* Pedir a db a maior posição na db */
        $maxposition =  ModelsUserActivity::max('position');

        /* if para saber se o feed que queremos subir já está no topo */
        if ($grabposition < $maxposition){
            /* Não se encontra no topo então */
            /* Posição para a qual queremos ir ( Posição atual do tweet +1) */
                $temp = $grabposition + 1;

            /* Pedimos a db o id da posição que queremos ir $temp */
            $giveid = ModelsUserActivity::select('id')
            ->where('position', $temp)
            ->get();

            /* Retiramos do array o valor do id que tem a posição que queremos ir */
            $idabove = $giveid[0]['id'];

            /* Pedimos a posição do id acima ao tweet que queremos mover */
            $giveposition = ModelsUserActivity::select('position')
            ->where('id', $idabove)
            ->get();

            /* Retiramos do array a posição do id acima ao que queremos mover */
            $feedaboveposition = $giveposition[0]['position'];

            /* Vamos atualizar a posição do tweet que queremos mover para a nova posição */
            ModelsUserActivity::where("id", $grabid)->update(["position" => $feedaboveposition]);

            /* Vamos atualizar a posição do tweet acima para a posição antiga do tweet que temos que selecionar */
            ModelsUserActivity::where("id", $idabove)->update(["position" => $grabposition]);
            error_log("entrei");

        } else {
            /* Encontra-se no topo */
            error_log("não entrei");
        }

        return redirect('/rwitterhome')->with('info');
    }


    /* Move Down */
    public function funfadown($id){
        return redirect('/rwitterhome')->with('info');
    }
}
/* update */
/* ModelsUserActivity::where("id", $id)->update(["position" => $receive]); */

/* Select */
/* $downgrade = ModelsUserActivity::select('id')
                ->where('position', $receive)
                ->get(); */
