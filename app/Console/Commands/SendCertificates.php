<?php

namespace App\Console\Commands;

use App\Mail\Conference\CertificateMail;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-certificates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();

        $endedConferences = Conference::where('end_date', '<', $now)->get();
        // dd($endedConferences);
        foreach ($endedConferences as $conference) {
            $registrants = ConferenceRegistration::where('conference_id', $conference->id)
                // ->where('certificate_sent', false)
                ->get();

            foreach ($registrants as $registrant) {
                $pdf = Pdf::loadView('backend.certificates.template', [
                    'registrant' => $registrant,
                    'conference' => $conference,
                ]);

                $directory = storage_path('app/certificates');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $filePath = $directory . "/certificate_{$conference->id}_{$registrant->id}.pdf";
                $pdf->save($filePath);


                Mail::to($registrant->user->email)->send(new CertificateMail($registrant, $filePath, $conference, $conference->conference_name));

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                // $registrant->certificate_sent = true;
                // $registrant->save();
            }
        }
    }
}
