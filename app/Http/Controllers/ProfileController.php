<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Deposit;
use App\Models\Region;
use App\Models\Systempage;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Withdrawaltype;
use App\Rules\AmountSumm;
use App\Rules\CurrentPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request as AdminRequest;
use function PHPSTORM_META\type;

class ProfileController extends Controller
{
    public function dyntree($parent_id)
    {
        $result = ['success' => false];

        $children = User::select(
            DB::raw("id, id as myid, parent_id, fname,family,fathername,region_id, (SELECT COUNT('id') FROM users WHERE parent_id = myid) as parent_count"))
            ->with('region')
            ->where('parent_id', '=', $parent_id == 0 ? null : $parent_id)
            ->orderby('family')
            ->get();


        if ($children->count()) {
            $result['success'] = true;
            $result['children'] = $children;
        }

        return $result;
    }

    public function income($id)
    {
        $user_id = Auth::id();
        if ($user_id == $id) {
            return 'Вы выбрали себя!';
        }
        if ($user_id) {
            $deposits = User::find($id)->deposits()->pluck('id')->toArray();
            $contributions = Contribution::where('user_id', $user_id)->whereIn('deposit_id', $deposits)->whereNotNull('level')->sum('amount');
            return number_format($contributions, 2, '.', ' ') . ' тенге';
        } else {
            return 0;
        }
    }

    public function my_referals()
    {
        $id = Auth::user()->id;
        $referals = User::select('id', 'fname', 'family', 'fathername', 'region_id')->with('region')->where('id', $id)->get();
        $referals = $referals->toArray();
        $referals[0]['children'] = [];
        $referals = json_encode($referals);
        $content = Systempage::where('page', 'my_referals')->first();
        return view('profile.my_referals', compact('content', 'referals'));
    }

}
