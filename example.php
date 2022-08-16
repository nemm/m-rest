<?php
    require_once __DIR__ . '/vendor/autoload.php';
    
    use Nemm\MRest\Client;
    use Nemm\MRest\AuthenticationType;
    use Nemm\MRest\AuthenticationFactory;

    
    // JWT Auth
    $token = "13121f7fa398b0333eb7223212b33040ce160d5c5f806235d1cd3d3e47966d1a";
    $client = 
            (new Client( 'https://gorest.co.in/' ))
                ->setAuthentication( AuthenticationFactory::createJWTAuth( $token ));
    
    try{
        $randomFirstName = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 5);
        $randomLastName = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 7);
        $email = "{$randomFirstName}@{$randomLastName}.com";
        
        $newUser = [
            "name" => mb_convert_case("{$randomFirstName} {$randomLastName}", MB_CASE_TITLE_SIMPLE),
            "gender" => "female",
            "email" => $email,
            "status" => 'active'
        ];
        
        # Create new user
            
        echo "Creating user {$newUser['name']}\n";
        $rData = $client->post("/public/v2/users",null,$newUser);
                
        if( $rData->getCode() === 201 && isset($rData->getData()['id']) ){
            $newUserID = $rData->getData()['id'];
            echo "{$newUser['name']} ID is {$newUserID}\n";
            
            echo "Changing gender ...\n";
            # use PATCH to change single field
            $result = $client->patch("/public/v2/users/{$newUserID}",null,["gender" => "male"]);
            
            $newUserData = $result->getData();
            
            echo "{$newUserData['name']} is {$newUserData['gender']} now\n";
        }else{
            echo "Could not create new user\n";
        }
        
    } catch (Exception $ex) {
        echo "Could not connect to API, code ";
        echo $ex->getCode() . ": ";
        echo $ex->getMessage() . "\n";
    }
    
    
    
    
    
