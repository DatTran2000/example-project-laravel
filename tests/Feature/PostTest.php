<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_post()
    {
        $payload = [
            "title"=> "mqz7etF2Hb",
            "content"=> "tOpnpwfAL6@gmail.com",
            "author" => "UswEmiNVKk",    
        ];

        $this->json('post', 'api/post', $payload)
        ->assertStatus(201) // test status code

        ->assertJsonStructure([
            "title",
            "content",
            "author",
            "updated_at",
            "created_at",
            "id",
        ]); // test stucre of json

        $this->assertDatabaseHas('posts', $payload); // test data inserted
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_post()
    {
        $payload = [
            "title"=> "mqz7etF2Hb",
            "content"=> "tOpnpwfAL6@gmail.com",
            "author" => "UswEmiNVKk",    
        ];

        $post = Post::create($payload);    

        $this->json('get', 'api/post/'.$post->id, $payload)
        ->assertStatus(200) // test status code

        ->assertJsonStructure([
            "title",
            "content",
            "author",
            "updated_at",
            "created_at",
            "id",
        ]); // test stucre of json

        $this->assertDatabaseHas('posts', $payload); // test data showed
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_post()
    {
        $payload = [
            "title"=> "mqz7etF2Hb",
            "content"=> "tOpnpwfAL6@gmail.com",
            "author" => "UswEmiNVKk",    
        ];

        $post = Post::create($payload);

        $this->json('put', 'api/post/'.$post->id ,$payload)
        ->assertStatus(200); // test status code

        $this->assertDatabaseHas('posts', $payload); // test data showed
    }



    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_destroy_post()
    {
        $payload = [
            "title"=> "mqz7etF2H1b",
            "content"=> "tOpnpwfAL6@gmail.com1",
            "author" => "UswEmiNVKk1",    
        ];

        $post = Post::create($payload);


        $this->json('delete', 'api/post/'.$post->id)
        ->assertNoContent(); // test status code

        $this->assertDatabaseMissing('posts', $payload); 
    }



}
