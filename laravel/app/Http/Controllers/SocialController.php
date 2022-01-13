<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Exception;
use Socialite;
use App\Models\Customer;

class SocialController extends Controller
{

    // --- GITHUB OAUTH ---
    public function gitRedirect()
    {
        return Socialite::driver('github')->redirect();
    }
       

    public function gitLogin()
    {
        try {
     
            $user = Socialite::driver('github')->user();
      
            $searchUser = Customer::where('github_id', $user->id)->first();
      
            if($searchUser){
      
                Auth::login($searchUser);
     
                return redirect('mobile/hire');
      
            }else{
                $gitUser = Customer::create([
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => 0,
                    'birth_year' => 1900,
                    'payment' => 'Invoice',
                    'saldo' => 100,
                    'github_id'=> $user->id,
                    'password' => encrypt('@S3CR£T@')
                ]);
     
                Auth::login($gitUser);
      
                return redirect('mobile/hire');
            }
     
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // --- FACEBOOK OAUTH ---
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookLogin()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            $isUser = Customer::where('fb_id', $user->id)->first();
     
            if($isUser){
                Auth::login($isUser);
                return redirect('mobile/hire');
            }else{
                $createUser = Customer::create([
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => 0,
                    'birth_year' => 1900,
                    'payment' => 'Invoice',
                    'saldo' => 100,
                    'fb_id' => $user->id,
                    'password' => encrypt('@S3CR£T@')
                ]);
    
                Auth::login($createUser);
                return redirect('mobile/hire');
            }
    
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }
}