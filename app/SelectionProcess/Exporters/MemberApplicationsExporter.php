<?php

namespace App\SelectionProcess\Exporters;


use Illuminate\Support\Collection;

class MemberApplicationsExporter
{
    protected static $delimiter = ";";

    public static function csv($applications)
    {
        if (!is_array($applications) && !($applications instanceof Collection))
            $applications = [$applications];

        if (is_array($applications))
            $applications = collect($applications);

        $header = ['Nome', 'Curso', 'RA', 'Email', 'Telefone', 'Opção Atual', 'Outra Opção'];

        $csv = fopen('php://memory', 'w');
        fputcsv($csv, $header, static::$delimiter);

        $applications->map( function($application) use ($csv) {
            $candidate = $application->candidate;
            $telephone = $candidate->phones ? $candidate->phones[0] : '';

            $attributes = [
                $candidate->name,
                $candidate->course,
                $candidate->ra,
                $candidate->email,
                $telephone,
                $application->currentOption,
                $application->otherOption,
            ];

            fputcsv($csv, $attributes, static::$delimiter);
        });

        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return $output;
    }
}