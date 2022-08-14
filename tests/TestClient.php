<?php
    use PHPUnit\Framework\TestCase;

    use Nemm\MRest\Client;
    use Nemm\MRest\AuthenticationType;
    use Nemm\MRest\AuthenticationFactory;
    
    final class UserClient extends TestCase{
        
        public function testNoAuthGET(){
            # simple GET without authentication
            $client = new Client( 'https://cat-fact.herokuapp.com/' );
            $responseData = $client->get('facts/random',['animal_type'=>'cat','amount'=>1]);
            
            $this->assertSame($responseData->getCode(), 200);
        }
        
        public function testNoAuthPOST(){
            # simple POST without authentication
            
            $client = new Client( 'http://api.shoutcloud.io/', AuthenticationType::NONE );
            $lowerCaseWord = 'einekleineświnkapopolugelaufenist';
            $responseData = $client->post('V1/SHOUT',null,['INPUT'=>$lowerCaseWord]);
            
            $this->assertSame($responseData->getCode(), 200);
            $this->assertArrayHasKey('OUTPUT', $responseData->getData() );
            $this->assertSame($responseData->getData()['OUTPUT'], mb_convert_case( $lowerCaseWord, MB_CASE_UPPER ));
            
        }
                
        public function testBasicAuthGET(){
            # simple GET with Basic Auth 
            # username and password are matched in those in path
    
            $username = "einekleine";
            $password = "swinka";
            $client = 
                (new Client( 'http://httpbin.org/' ))
                    ->setAuthentication( AuthenticationFactory::createBasicAuth( $username, $password ));
    
            $responseData = $client->get("basic-auth/{$username}/{$password}");
            $this->assertSame($responseData->getCode(), 200);
        }
        
        public function testJWTAuthGET(){
            # simple POST with JWT Auth with pregenerated token
            
            $token = "13121f7fa398b0333eb7223212b33040ce160d5c5f806235d1cd3d3e47966d1a";
            $client = 
                    (new Client( 'https://gorest.co.in/' ))
                        ->setAuthentication( AuthenticationFactory::createJWTAuth( $token ));

            $responseData = $client->post("/public/v2/users",null,["name" => "EineKleine Świnka", "gender" => "female", "email" => "swinka@einekleine.pl", "status" => "active"]);
            
            # Unprocessable Entity, entry already added earlier
            $this->assertSame($responseData->getCode(), 422);
        }
        
        
        public function testJWTAuthPUT(){
            # simple PUT with JWT Auth with pregenerated token
            # change świnka's gender
            
            $token = "13121f7fa398b0333eb7223212b33040ce160d5c5f806235d1cd3d3e47966d1a";
            $client = 
                    (new Client( 'https://gorest.co.in/' ))
                        ->setAuthentication( AuthenticationFactory::createJWTAuth( $token ));

            $responseData = $client->put("/public/v2/users/4245",null,["name" => "EineKleine Świnka", "gender" => "male", "email" => "swinka@einekleine.pl", "status" => "active"]);
            
            # Unprocessable Entity, entry already added earlier
            $this->assertSame($responseData->getCode(), 200);
            $this->assertArrayHasKey('gender', $responseData->getData() );
            $this->assertSame($responseData->getData()['gender'], 'male');
        }
        
        
        public function testJWTAuthPATCH(){
            # simple PATCH with JWT Auth with pregenerated token
            # change świnka's gender again
            
            $token = "13121f7fa398b0333eb7223212b33040ce160d5c5f806235d1cd3d3e47966d1a";
            $client = 
                    (new Client( 'https://gorest.co.in/' ))
                        ->setAuthentication( AuthenticationFactory::createJWTAuth( $token ));

            $responseData = $client->patch("/public/v2/users/4245",null,["gender" => "female"]);
            
            # Unprocessable Entity, entry already added earlier
            $this->assertSame($responseData->getCode(), 200);
            $this->assertArrayHasKey('gender', $responseData->getData() );
            $this->assertSame($responseData->getData()['gender'], 'female');
        }
    }
