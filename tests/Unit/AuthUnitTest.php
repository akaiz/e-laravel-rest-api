<?php


namespace Tests\Unit;


use Tests\TestCase;

class AuthUnitTest extends TestCase
{
  
  public function test_it_fails_to_register_if_field_missing()
  {
    $data = [
      'name' => $this->faker->name,
    ];
    
    $this->json('POST', 'api/auth/register', $data)
         ->assertStatus(400)
         ->assertJsonStructure(['error']);
  }
  
  public function test_it_fails_when_email_is_not_unique()
  {
    $preData = [
      'name'     => $this->faker->name,
      'email'    => 'test@me.com',
      'income'   => $this->faker->numberBetween(1000, 9000),
      'password' => $this->faker->password
    ];
    
    $this->json('POST', 'api/auth/register', $preData)
         ->assertStatus(201)
         ->assertJsonStructure(['token']);
    
    $postData = [
      'name'     => $this->faker->name,
      'email'    => 'test@me.com',
      'income'   => $this->faker->numberBetween(1000, 9000),
      'password' => $this->faker->password
    ];
    
    $this->json('POST', 'api/auth/register', $postData)
         ->assertStatus(400)
         ->assertJsonStructure(['error']);
    
  }
  
  public function test_it_can_register_a_user()
  {
    $data = [
      'name'     => 'Justine Barber',
      'email'    => 'justine@bieber.com',
      'income'   => $this->faker->numberBetween(1000, 9000),
      'password' => 'test1234'
    ];
    
    $this->json('POST', 'api/auth/register', $data)
         ->assertStatus(201)
         ->assertJsonStructure(['token']);
  }
  
  public function test_it_fails_login_when_user_does_not_exist()
  {
    $data = [
      'email'    => $this->faker->email,
      'password' => $this->faker->password
    ];
    
    $this->json('POST', 'api/auth/login', $data)
         ->assertStatus(401)
         ->assertJsonStructure(['error']);
  }
  
  public function test_it_logins_existing_user()
  {
    $data = [
      'name'     => 'Justine Barber',
      'email'    => 'justine@bieber.com',
      'income'   => $this->faker->numberBetween(1000, 9000),
      'password' => 'test1234'
    ];
    
    $this->json('POST', 'api/auth/register', $data);
    
    $this->json('POST', 'api/auth/login', $data)
         ->assertStatus(200)
         ->assertJsonStructure(['token']);
  }
  
  public function test_it_logout_user()
  {
    $data = [
      'name'     => 'Justine Barber',
      'email'    => 'justine@bieber.com',
      'income'   => $this->faker->numberBetween(1000, 9000),
      'password' => 'test1234'
    ];
    
    $this->json('POST', 'api/auth/register', $data);
    $request  = $this->json('POST', 'api/auth/login', $data);
    $response = $request->send();
    
    $data = (array) $response->getData();
    
    $this->json('POST', 'api/auth/logout', [], [
      'HTTP_Authorization' => 'Bearer ' . $data["token"]
    ])->assertStatus(200)
         ->assertExactJson([
           "error"   => false,
           "message" => "auth.logged_out"
         ]);
  }
  
}