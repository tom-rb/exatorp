<?php

namespace App\SelectionProcess\Controllers;

use App\SelectionProcess\Exporters\MemberApplicationsExporter;
use App\SelectionProcess\SelectionProcess;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Infrastructure\Http\Controller;

class ExportCsvController extends Controller
{
    /**
     * Generates a CSV file download response with Member Applications
     */
    public function export(SelectionProcess $process, Request $request)
    {
        $applicationsQuery = $process->applications();
        $time = (new Carbon())->format('d \d\e M \d\e Y H\h i\m\i\n');

        if ($area = $request->query('area')) {
            $applicationsQuery = $applicationsQuery->forArea($area);
            $fileName = "Candidatos para $area $time.csv";
        }
        else
            $fileName = "Todos os candidatos $time.csv";

        $csv = MemberApplicationsExporter::csv($applicationsQuery->get());

        return response()->downloadText($csv, $fileName);
    }
}
