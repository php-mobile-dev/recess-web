<?php

namespace App\Http\Helpers;
use Carbon\Carbon;

trait FileUpload
{
    function upload($file, $destinationPath='uploads'){
        $returnArr = array(
            'mime_type' => '',
            'file_name' => ''
        );
    
        // $mimeType = $file->getMimeType();
        $mimeType = 'SSSS';
        
        // dd($destinationPath);
        $uniqueId = Carbon::now()->format('Ymdhis');
        $originalName = $file->getClientOriginalName();
        $name = $uniqueId . '_' . str_replace(' ','_', $originalName).'.'.$file->getClientOriginalExtension();
        $file->move($destinationPath, $name);
        $returnArr['mime_type'] = $mimeType;
        $returnArr['file_name'] = $name;
        return $returnArr;
    }
}