<?php

namespace Tests\Unit;

use App\SelectionProcess\Exporters\MemberApplicationsExporter;

use App\SelectionProcess\MemberApplication;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberApplicationsExporterTest extends TestCase
{
    use DatabaseTransactions;

    private $header = 'Nome;Curso;RA;Email;Telefone;"Opção Atual";"Outra Opção"'."\n";

    /** @test */
    public function it_produces_the_header_line()
    {
       $csv = MemberApplicationsExporter::csv([]);

       $this->assertEquals($this->header, $csv);
    }

    /** @test */
    public function it_outputs_a_member_application_info_in_one_line()
    {
        $application = createState(MemberApplication::class, 'with_second_job');
        $candidate = $application->candidate;

        $csv = MemberApplicationsExporter::csv($application);

        $this->assertEquals($this->header.$this->makeCsvLine($application, $candidate), $csv);
    }

    /** @test */
    public function it_outputs_several_members_applications_info()
    {
        $application_one = create(MemberApplication::class);
        $candidate_one = $application_one->candidate;

        $application_two = create(MemberApplication::class);
        $candidate_two = $application_two->candidate;

        $csv = MemberApplicationsExporter::csv([$application_one, $application_two]);

        $expected = $this->header .
            $this->makeCsvLine($application_one, $candidate_one) .
            $this->makeCsvLine($application_two, $candidate_two);

        $this->assertEquals($expected, $csv);

        // Works with collection too
        $csv = MemberApplicationsExporter::csv(collect([$application_one, $application_two]));
        $this->assertEquals($expected, $csv);
    }

    private function makeCsvLine($application, $candidate)
    {
        $s = '"'.$candidate->name.'";'.
            '"'.$candidate->course.'";'.
            $candidate->ra.';'.
            $candidate->email.';'.
            '"'.$candidate->phones[0].'";'.
            '"'.$application->currentOption.'";';

        if ($application->otherOption)
            $s = $s . '"'.$application->otherOption."\"";

        return $s."\n";
    }
}
