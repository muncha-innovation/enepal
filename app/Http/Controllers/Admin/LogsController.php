<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\LogsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Process;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function getAllLogs(LogsDataTable $dt)
    {

        $startDate = request()->get('start_date') ?? Carbon::now()->subDays(30);
        $endDate = request()->get('end_date') ?? Carbon::now();

        return $dt->with([
            'startDate' => $startDate, 'endDate' => $endDate,
        ])->render('modules.logs.index');
    }

    public function getLogsByUser(Request $request, $userId)
    {

        $startDate = request()->get('start_date') ? Carbon::parse(request()->get('start_date'))->format('m/d/Y') : Carbon::now()->subDays(30)->format('m/d/Y');
        $endDate = request()->get('end_date') ? Carbon::parse(request()->get('end_date'))->format('m/d/y') : Carbon::now()->format('m/d/Y');

        $logs = Activity::where('causer_id', $userId)
            ->when(request('start_date') && request('end_date'), function ($query) {
                return $query->whereBetween('created_at', [request('start_date'), request('end_date')]);
            })
            ->latest()->paginate(10);
        $modelId = $userId;
        return view('modules.logs.logs_by_model', compact('logs', 'startDate', 'endDate', 'modelId'));
    }
    public function getLogsBySubject(Request $request, $subjectId, $subjectType)
    {
        $type = $subjectType;
        $typeClass = $subjectType == 'process' ? get_class(new Process()) : get_class(new Product());
        $startDate = request()->get('start_date') ? Carbon::parse(request()->get('start_date'))->format('m/d/Y') : Carbon::now()->subDays(30)->format('m/d/Y');
        $endDate = request()->get('end_date') ? Carbon::parse(request()->get('end_date'))->format('m/d/y') : Carbon::now()->format('m/d/Y');
        $modelId = $subjectId;
        $logs = Activity::where('subject_id', $subjectId)
            ->when(request('start_date') && request('end_date'), function ($query) {
                return $query->whereBetween('created_at', [request('start_date'), request('end_date')]);
            })
            ->where('subject_type', $typeClass)->latest()->paginate(10);

        return view('modules.logs.logs_by_model', compact('logs', 'startDate', 'endDate', 'modelId', 'type'));
    }
}