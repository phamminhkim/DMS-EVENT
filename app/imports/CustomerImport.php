<?php

namespace App\Imports;

use App\Models\Customer;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class CustomerImport implements ToCollection
{

    public function collection(Collection $rows)
    {

        $header = $rows->shift()->toArray();

        $header = array_map(function ($item) {
            return mb_convert_case($item, MB_CASE_LOWER, "UTF-8");
        }, $header);
        $dataExcel = $rows->skip(0);
        $role = User::find(auth()->user()->id);
        try {
            foreach ($dataExcel as $key => $row) {
                $row = $row->map(function ($item) {
                    return trim($item);
                });
    
                $customer = new Customer();
                // $customer->user_id = $role->id;
                foreach ($header as $rowNumber => $column) {
                    if ($column == 'mã nhân viên bán hàng') {
                        if ($row[$rowNumber] == '') {
                            throw new \Exception("Dòng số '" . ($key + 1) . "': Mã nhân viên bán hàng không được để trống.");
                        } else {
                            $user = User::where('staff_code', $row[$rowNumber])->first();
                            if ($user) {
                                $customer->user_id = $user->id;
                            } else {
                                throw new \Exception("Dòng số '" . ($key + 1) . "': Mã nhân viên bán hàng '{$row[$rowNumber]}' không tồn tại trong hệ thống.");
                            }
                        }
    
                    }
                    if ($column == 'dms code') {
                        if (!empty($row[$rowNumber])) {
                            $unq = Customer::where('dms_code', $row[$rowNumber])->first();
                            if ($unq) {
                                throw new \Exception("Dòng số '" . ($key + 1) . "': Mã dms_code '{$row[$rowNumber]}' đã tồn tại trong hệ thống.");
                            }
                            $customer->dms_code = $row[$rowNumber];
                        } else {
                            // Xử lý khi cột dms code rỗng
                            $customer->dms_code = null;
                        }
                    }
                    if ($column == 'tên khách hàng') {
                        if ($row[$rowNumber] == '') {
                            throw new \Exception("Dòng số '" . ($key + 1) . "': Tên khách hàng không được để trống.");
                        } else {
                            $customer->name = $row[$rowNumber];
                        }
    
                    }
                    if ($column == 'địa chỉ') {
                        if ($row[$rowNumber] == '') {
                            $customer->address = null;
                        } else {
                            $customer->address = $row[$rowNumber];
                        }
    
                    }
                }
                $customer->save();
            }
        } catch (\Throwable $th) {
            Log::info( $th->getMessage());
            throw $th;
        }
       

    }
}
