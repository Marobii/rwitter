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
        $maxid = ModelsUserActivity::select('id')
            ->where('position', $maxposition)
            ->get();

        /* Sei que o max position tem id = dbgrabid */
        $dbgrabid = $maxid[0]['id'];

        $confirmnext = $grabposition + 1;

        $askdb = ModelsUserActivity::select('id')
            ->where('position', $confirmnext)
            ->get();

        $compare = $grabposition;
        if ($maxposition == $compare){
            error_log("Já estás no topo");
        }else {
            if(empty($askdb[0])){
                $checkall=ModelsUserActivity::select('id' , 'position')
                ->orderBy('position', 'asc')
                ->get();
                foreach($checkall as $index=>$opt){
                    $test = $checkall[$index]['position'];
                    if ($test === $grabposition){
                        error_log("faz nada");
                    }else {
                        error_log($checkall);
                        if ($test > $grabposition ) {

                            /* Id acima */
                            $redirect = ModelsUserActivity::select('id')
                            ->where('position', $test)
                            ->get();

                            $unveil = $redirect[0]['id'];

                            ModelsUserActivity::where("id", $unveil)->update(["position" => $grabposition]);
                            ModelsUserActivity::where("id", $grabid)->update(["position" => $test]);

                            break;
                        }
                    }
                }
                /* [{"id":17,"position":3},{"id":18,"position":2},{"id":21,"position":19},{"id":22,"position":22}] */
                /* ModelsUserActivity::where("id", $tradeid)->update(["position" => $trade2position]);
                ModelsUserActivity::where("id", $trade2id)->update(["position" => $tradeposition]); */

            } else {
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
            }
        }

        return redirect('/rwitterhome')->with('info');
    }

    /* Move Down */
    public function funfadown($id){
        /* Agarrar na informação do feed que queremos mover */
        $grabinfo = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();

        /* Retirar do array os valores do id que queremos mover e guardar nas respetivas variaveis */
        $grabid =  $grabinfo[0]['id'];
        $grabposition =  $grabinfo[0]['position'];

        /* Pedir a db a maior posição na db */
        $minposition =  ModelsUserActivity::min('position');
        $minid = ModelsUserActivity::select('id')
            ->where('position', $minposition)
            ->get();

        /* Sei que o max position tem id = dbgrabid */
        $dbgrabid = $minid[0]['id'];

        $confirmnext = $grabposition - 1;

        $askdb = ModelsUserActivity::select('id')
            ->where('position', $confirmnext)
            ->get();

        $compare = $grabposition;
        if ($minposition == $compare){
            error_log("Já estás no fundo");
        }else {
            if(empty($askdb[0])){
                $checkall=ModelsUserActivity::select('id' , 'position')
                ->orderBy('position', 'asc')
                ->get();
                foreach($checkall as $index=>$opt){
                    $test = $checkall[$index]['position'];
                    if ($test === $grabposition){
                        error_log("faz nada");
                    }else {
                        error_log($checkall);
                        if ($test < $grabposition && $minposition != $test) {

                            /* Id acima */
                            $redirect = ModelsUserActivity::select('id')
                            ->where('position', $test)
                            ->get();

                            $unveil = $redirect[0]['id'];

                            ModelsUserActivity::where("id", $unveil)->update(["position" => $grabposition]);
                            ModelsUserActivity::where("id", $grabid)->update(["position" => $test]);

                            break;
                        }
                    }
                }
                /* [{"id":17,"position":3},{"id":18,"position":2},{"id":21,"position":19},{"id":22,"position":22}] */
                /* ModelsUserActivity::where("id", $tradeid)->update(["position" => $trade2position]);
                ModelsUserActivity::where("id", $trade2id)->update(["position" => $tradeposition]); */

            } else {
                $temp = $grabposition - 1;

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
            }
        }
        return redirect('/rwitterhome')->with('info', $minposition);
    }
}

/* 02:06 */
