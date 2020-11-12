<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class todoTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function getUser() {
        $user = User::find(7); // find specific user
        $this->actingAs($user, 'api');
    }

    public function test_login(){
        $data = [
            'email'=>'anurags@gmail.com',
            'password'=>'Anurag1@'
        ];

        $this->post(route('task.login'),$data)
            ->assertJson(['status' => TRUE]);

//        $this->assertAuthenticated();
//    }
//        $this->post(route('task.login'),$data)
//            ->assertJson(['status' => TRUE,]);
//        dd(Auth::user()->accessToken());

    }

//    public function test_create(){
//
////        $this->getUser();
////        dd($this->getUser());
//        $data =  [
//            'title'              => 'News Creating',
//            'status'             => 'Incomplete',
//        ];
//        $this->json('POST', 'api/create/task', $data, ['Accept' => 'application/json'])
//            ->assertStatus(401)
//            ->assertJson([
//                'access_token' => true,
//                'token_type' => true,
//                'expires_at' => true,
//            ]);
//        $this->post(route('task.create'),$data)
//            ->assertJson(['status' => TRUE]);
//    }


}
