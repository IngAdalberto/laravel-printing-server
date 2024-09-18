<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingJob extends Model
{
    protected $fillable = [
        'name', 'detail', 'origin', 'printer_name', 'printer_ip', 'header', 'lines', 'file', 'sent_by', 'log', 'status'
    ];
}
