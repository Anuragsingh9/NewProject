<?php

namespace Tests\Feature;

use App\Task;
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
//        $user = User::find(7); // find specific user
//        $user->createToken('Personal Access Token')->accessToken;
//        return $this->actingAs($user, 'api');
    }

    public function test_login(){
        $data = [
            'email'=>'anurags@gmail.com',
            'password'=>'Anurag1@'
        ];
        $this->json('POST', 'api/login', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "token_type",
                "access_token",
                "expires_at",
            ]);
    }

    public function test_create(){
        $user = User::find(7); // find specific user
        $token = $user->createToken('Personal Access Token')->accessToken;
        $this->actingAs($user, 'api');
        $data =  [
            'title'              => 'News Creating',
            'status'             => 'incomplete',
        ];
        $this->json('POST', 'api/create/task', $data, ['Accept' => 'application/json','Authorization' => 'Bearer '. $token])
            ->assertStatus(201);
    }

//    public function test_update(){
//        $user = User::find(7); // find specific user
//        $token = $user->createToken('Personal Access Token')->accessToken;
//        $this->actingAs($user, 'api');
//        $task = Task::find(13);
//        $data =  [
//            'title'              => 'News Creating',
//            'status'             => 'complete',
//        ];
//        dd($data);
//        $this->json('POST', 'api/update/task', $data, ['Accept' => 'application/json','Authorization' => 'Bearer '. $token])
//            ->assertStatus(201);
//    }

    public function test_getTask(){
        $user = User::find(7); // find specific user
        $token = $user->createToken('Personal Access Token')->accessToken;
        $this->actingAs($user, 'api');
        $data =  [];

        $this->json('GET', 'api/get/task',$data, ['Accept' => 'application/json','Authorization' => 'Bearer '. $token])
            ->assertStatus(200);
    }

    public function test_getAllTask(){
        $user = User::find(7); // find specific user
        $token = $user->createToken('Personal Access Token')->accessToken;
        $this->actingAs($user, 'api');
        $data =  [];
        $this->json('GET', 'api/all/task',$data, ['Accept' => 'application/json','Authorization' => 'Bearer '. $token])
            ->assertStatus(200);
    }

    public function test_delete(){
        $user = User::find(7); // find specific user
        $token = $user->createToken('Personal Access Token')->accessToken;
        $this->actingAs($user, 'api');
        $data =  ['task_id'=>13];
        $this->json('POST', 'api/delete',$data, ['Accept' => 'application/json','Authorization' => 'Bearer '. $token])
            ->assertStatus(200);
    }

    public function test_search(){
        $user = User::find(7); // find specific user
        $token = $user->createToken('Personal Access Token')->accessToken;
        $this->actingAs($user, 'api');
        $data =  ['title'=>'News'];
        $this->json('GET', 'api/search',$data, ['Accept' => 'application/json','Authorization' => 'Bearer '. $token])
            ->assertStatus(200);
    }


}
