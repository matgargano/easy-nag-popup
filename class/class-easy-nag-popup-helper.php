<?php

class easy_nag_popup_helper {
        public static function remote_file_exists($url) {
                if  (!in_array  ('curl', get_loaded_extensions())) {
                    $file_headers = @get_headers($url);
                    if(strpos($headers[0], '404 Not Found')) return false;
                    return true;
                }
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_NOBODY, true);
                $result = curl_exec($curl);
                $ret = false;
                if ($result !== false) {
                    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
                    if ($statusCode == 200) {
                        $ret = true;   
                    }
                }
                curl_close($curl);
                return $ret;
        
        }
}
    
    
    