<?php 
use Illuminate\Support\Facades\Route;

if(!function_exists('urlsafe_b64encode')){
    function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}
if(!function_exists('urlsafe_b64decode')){
    function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}

if(!function_exists('init_rapid_paginator_cache')){
    /* This function is supposed to cache 'Form' values...
    *
    * @param  Array     $fields
    * @return Array     $result
    *
    */
    function init_rapid_paginator_cache($fields = null, $tab=1){
        //init cache
        $cache = isset($fields) ? count($fields) > 0 ? [] : null : null;
        
        if($cache == [] || $cache == null){
            $cache['sort'] = '>';
            $cache['perPage'] = '10';
        }

        // If form is submitted...
        // Cache Form values
        if (request()->isMethod('POST')) {
            foreach ($fields as $fieldName) {
                if(request($fieldName)){
                    $cache[$fieldName] = request($fieldName);
                }
            }
        }
        else // else we have to retrieve old cache from the state 
        {
            $state_array = null;

            // Decode The State
            if(request('state') && request('tab') == $tab){
                $state_base64 = request('state');
                $state_decoded = urlsafe_b64decode($state_base64);
                $state_array = json_decode($state_decoded,true);
            }

            // Append the state cache key/value pairs to the new cache...
            if(isset($state_array['cache'])){
                foreach ($state_array['cache'] as $key => $value) {
                    $cache[$key] = $value;
                }
            }
        }

        return $cache;
    }
}
if(!function_exists('rapid_paginator')){
    /* Custom pagination System Based on RapidPagination package
    * @param  Query     $query
    * @param  Array     $field
    * @param  Char      $sort 
    * @param  Integer   $sort
    * @param  Boolean   $seekable
    * @return Array     $result
    */
    function rapid_paginator($query, $field = 'id', $cache = null, $sort = '>', $perPage = 10, $tab=1, $seekable = true)
    {
        if($cache == null)
            init_rapid_paginator_cache(null);
        /*
        ** Setup Default values
        */
        if($sort == null)
            $sort = '>';
        else
            $cache['sort'] = $sort;

        if($field == null)
            $field = 'id';
        
        if($perPage == null)
            $perPage = 10;
        else
            $cache['perPage'] = $perPage;
    
        /*
        ** Extract Cursor from the State route parameter
        ** Cursor is used as a reference to navigate to the next or previous 'pages'...
        */

        $state_array = null;

        // Decode the State
        if(request('state') && request('tab') == $tab){
            $state_base64 = request('state');
            $state_decoded = urlsafe_b64decode($state_base64);
            $state_array = json_decode($state_decoded,true);
        }

        $cursor = null;
        
        // Add cursor from state to the newCursor array
        if($state_array){
            if(isset($state_array['cursor'][$field]))
                $cursor[$field] = $state_array['cursor'][$field];
        }
        

        // Create a new paginator
        $paginator = $query->rapid_pagination()
                    ->limit($perPage); // Set Number of elements Per Page (default=10)
        
        // Sort by 'field'..
        if($sort == '>' || $sort == null)
            $paginator = $paginator->orderBy($field);
        else
            $paginator = $paginator->orderByDesc($field);
        
        // Get 'Previous Cursor' to be able to navigate backwards
        if($seekable)
            $paginator = $paginator->seekable(); 

        // If 'Next' Button is Clicked
        if(request()->direction == "next" || request()->direction == null){
            $paginator = $paginator->forward(); // Use forward method to change the direction of the navigation
        } 
        // If 'Previous' Button is Clicked
        else{
            $paginator = $paginator->backward(); // Use backward method to change the direction of the navigation
        }

        // Navigation rules
        if($cursor != null){
            $paginator = $paginator
                ->paginate($cursor);
        }
        else{
            $paginator = $paginator
                ->paginate();
        }

        /*
        ** Prepare a new State
        */
        
        // Extract cursors from paginator
        $paginatorArray = (array)$paginator;
        unset($paginatorArray['records']); // We don't need to encode records in the state


        // Next and Previous buttons have different cursors that's why we need state for every button

        // Next Btn State...
        $state_next = [
            'cursor' => $paginatorArray['nextCursor'],
            'cache' => $cache
        ];

        // Previous Btn State...
        $state_prev = [
            'cursor' => $paginatorArray['previousCursor'],
            'cache' => $cache
        ];

        // Encode States
        $base64_next_state = urlsafe_b64encode(json_encode($state_next));
        $base64_prev_state = urlsafe_b64encode(json_encode($state_prev));
        
        //Set tab id
        $paginator->setTabID($tab);
        
        // Set paginator previous and next Urls
        $paginator->makePreviousUrl($base64_prev_state);
        $paginator->makeNextUrl($base64_next_state);

        if($appendQuery){
            $paginator->appends(request()->query());
        }

        $result = [
            'items' => $paginator,
            'cache' => $cache
        ];

        return $result;
    }
}
