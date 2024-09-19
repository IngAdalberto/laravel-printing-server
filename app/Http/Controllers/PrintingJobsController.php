<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\PrintingJobService;
use Illuminate\Support\Facades\Input;

class PrintingJobsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('cors');
    }

    public function print()
    {
        return (new PrintingJobService())->print_job( Input::get() );
    }

    public function jobs()
    {
        $jobs = (new PrintingJobService())->get_jobs( Input::get() );

        return view( 'jobs', compact('jobs') );
    }

    public function reprint_job( $id )
    {
        (new PrintingJobService())->reprint_job( $id );

        $jobs = (new PrintingJobService())->get_jobs([]);

        return view( 'jobs', compact('jobs') );
    }

    public function cancel_job( $id )
    {
        (new PrintingJobService())->cancel_job( $id );

        $jobs = (new PrintingJobService())->get_jobs([]);

        return view( 'jobs', compact('jobs') );
    }
}
