<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Post;

class PostControllerTest extends TestCase
{

    use RefreshDatabase;

    public function test_store()
    {
        $this->withoutExceptionHandling();

        $response = $this->json('POST', '/api/posts', [
            'title' => 'El post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
        ->assertJson(['title' => 'El post de prueba'])
        ->assertStatus(201); //OK, creado un recurso

        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    public function test_validation(){
        $response = $this->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show(){

        $post = factory(Post::class)->create();

        $response = $this->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title' => $post->title])
        ->assertStatus(200);

    }

    public function test_update()
    {
        $this->withoutExceptionHandling();

        $post = factory(Post::class)->create();

        $response = $this->json('PUT', "/api/posts/$post->id", [
            'title' => 'El post de edit'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
        ->assertJson(['title' => 'El post de edit'])
        ->assertStatus(200); //OK, edit recurso

        $this->assertDatabaseHas('posts', ['title' => 'El post de edit']);
    }

    public function test_delete(){
        $this->withExceptionHandling();

        $post = factory(Post::class)->create();

        $response = $this->json('DELETE',"/api/posts/$post->id");

        $response->assertSee(null)
        ->assertStatus(204); //sin contenido

        $this->assertDatabaseMissing('posts',['id'=>$post->id]); // se revisa si el id existe en la tabla post
    }

}
