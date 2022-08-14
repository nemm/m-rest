<?php

    namespace Nemm\MRest;
    
    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;
    use Nyholm\Psr7\Request;
    use Nyholm\Psr7\Response;
    use Nyholm\Psr7\Stream;
    use Nyholm\Psr7\Factory\Psr17Factory;

    /**
     * Main class used to make API calls
     */
    
    class Client {
        private string $url;
        private Authentication $auth;
        
        public function __construct( $url ) {
            $this->setUrl($url);
            $this->setAuthentication( AuthenticationFactory::createNoAuth() );
        }
        
        public function setAuthentication( Authentication $auth ){
            $this->auth = $auth;
            return $this;
        }
        
        public function get( string $path, array $query = null ):?ResponseData{
            return $this->call('GET', $path, $query);
        }
        
        public function post( string $path, array $query = null,$data= [] ):?ResponseData{
            return $this->call('POST', $path, $query,$data);
        }
        
        public function put( string $path, array $query = null,$data= [] ):?ResponseData{
            return $this->call('PUT', $path, $query,$data);
        }
        
        public function patch( string $path, array $query = null,$data= [] ):?ResponseData{
            return $this->call('PATCH', $path, $query,$data);
        }

        private function call( $method = 'GET', string $path, array $query = null, $data = [] ):?ResponseData{
            # construct factory
            $psr17Factory = new Psr17Factory();
            
            # prepare URI
            $uri = $psr17Factory->createUri( $this->url )
                ->withPath($path);
            
            # propably for GET  only
            if( !is_null( $query ) ){
                $uri = $uri->withQuery( http_build_query($query) );
            }
            
            # create request
            $request = 
                $psr17Factory->createRequest($method, $uri );
            
            # add body to other than GET
            if( $method !== 'GET' ){
                $request = $request
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody( Stream::create( json_encode($data) ) );
            }
            
            # make the call
            $response = $this->doTheRequest($request);
            
            # retrieve data
            $response->getBody()->rewind();
            $contents = json_decode( trim($response->getBody()->getContents() ), true);
            
            # construct ResponseData object and return
            $responseData = new ResponseData( $response->getStatusCode(), $contents);
            return $responseData;
        }

        /**
         * remove last slash if it's there
         */
        public function setUrl(string $url): void {
            if(substr($url, -1) == '/') {
                $url = substr($url, 0, -1);
            }
            $this->url = $url;
        }

        /**
         * prepare nicely formatted options array
         */
        private function constructOptions( RequestInterface $request ){
            $options = [];
            
            # some standard options
            $options[CURLOPT_URL] = (string)$request->getUri();
            $options[CURLOPT_RETURNTRANSFER] = true;
            
            # headers
            $headers = [];
            foreach( $request->getHeaders() as $headerKey => $headerValues ){
                $headers[] = "{$headerKey}: {$headerValues[0]}";
            }
            
            # authentication
            switch( $this->auth->getType() ){
                case AuthenticationType::NONE:{
                    break;
                }
                
                case AuthenticationType::BASIC:{
                    $options[CURLOPT_USERPWD] = $this->auth->getUsername() . ":" . $this->auth->getPassword();
                    break;
                }
                
                case AuthenticationType::JWT:{
                    $headers[] = "Authorization: Bearer " . $this->auth->getToken();
                    break;
                }
            }
            
            $options[CURLOPT_HTTPHEADER] = $headers;
            
            # method specific
            switch( $request->getMethod() ){
                case 'POST':{
                    $request->getBody()->rewind();
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = (string)$request->getBody()->getContents();
                    break;
                }
                
                case 'PUT':
                case 'PATCH':{
                    $request->getBody()->rewind();
                    $options[CURLOPT_CUSTOMREQUEST] = $request->getMethod();
                    $options[CURLOPT_POSTFIELDS] = (string)$request->getBody()->getContents();
                    break;
                }
            }
            
            return $options;
        }
        
        /**
         * this is where curl is making a shot
         */
        private function doTheRequest( RequestInterface $request ): ResponseInterface{
            # construct factory
            $psr17Factory = new Psr17Factory();
            
            # create response
            $response = $psr17Factory->createResponse();
            
            # init curl instance
            $curl = curl_init();
                        
            # prepare options
            $options = $this->constructOptions( $request );
            
            # retrieve headers           
            $options[CURLOPT_HEADERFUNCTION] = 
                function ($curl, $header) use (&$response) {
                    $len = strlen($header);
                    $header = explode(':', $header, 2);
                    
                    # ignore invalid headers
                    if (count($header) < 2){
                        return $len; 
                    }
                        
                    $name = strtolower(trim($header[0]));
                    $value = trim($header[1]);
                    $response = $response->withHeader($name, $value);
                    return $len;
                };
            
            #set options
            curl_setopt_array($curl, $options);
            
            # retrieve body
            $body = curl_exec($curl);
            
            # retrieve extra info
            $info = curl_getinfo($curl);
                 
            curl_close($curl);
            
            # fill ResponseMessage
            $response = $response
                ->withStatus($info['http_code'])
                ->withBody( Stream::create( $body ) );
            
            return $response;
        }
    }
