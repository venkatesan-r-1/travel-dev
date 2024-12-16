<?php


$attribute_id = ['USRATTR_29', 'USRATTR_30', 'USRATTR_31', 'USRATTR_32', 'USRATTR_33'];

$group_id = DB::table('trf_user_detail_mapping')->orderBy('group_id', 'desc')->value('group_id') ?? 1;

$passport_details = DB::table('trf_user_detail_mapping')
                        ->select('id', 'aceid', 'group_id')
                        ->whereIn('attribute', $attribute_id)
                        ->get()
                        ->groupBy('aceid')
                        ->map(function ($item) {
                            return $item->pluck('id');
                        })
                        ->each( function ($item) use(&$group_id) {  
                            DB::table('trf_user_detail_mapping')->whereIn('id', $item->toArray())->update(['group_id' => $group_id++]);
                        } );


