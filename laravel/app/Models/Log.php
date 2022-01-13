<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';
    protected $table = "log";
    public $timestamps = false;

    public function pay($id)
    {
        $this->where('log_id', $id)
            ->update(
                [
                'paid' => 1
                ]
            );
    }
}
