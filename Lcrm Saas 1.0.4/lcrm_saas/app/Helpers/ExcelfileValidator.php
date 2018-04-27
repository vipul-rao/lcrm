<?php

namespace App\Helpers;

use Validator;

class ExcelfileValidator {

    public static function validate($request)
    {

        $validator = Validator::make(
			[
				'file'      =>  $request->file('file'),
				'extension' => strtolower($request->file('file')?$request->file('file')->getClientOriginalExtension():''),
			],
			[
				'file'          => 'required',
				'extension'      => 'in:csv,xlsx',
			]
		);

		 if ($validator->fails()) {
		 	// TODO : pass validator and let controller catch and display errors
            return false;
        }

        return true;
    }



}

?>
