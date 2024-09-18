<?php

namespace App\Services;

use App\Models\PrintingJob;
use Exception;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class PrintingJobService
{
    public function print_job($data)
    {
        if (!$this->validate_data($data)) {
            return json_encode(
                    [
                        'status' => 'error',
                        'message' => 'Error en el envio de parámetros.'
                    ]
                );
        }

        $job = $this->store_job( $data );
        
        $message = $this->sent_to_print( $data );

        $json_response = $this->update_job( $job->id, $message);
        
        return $data['callback'] . '(' . $json_response . ')';
    }

    public function validate_data($data)
    {
        if ( !isset( $data['callback'] ) ) {
            return false;
        }
        
        if ( !isset( $data['printer_ip'] ) ) {
            return false;
        }
        
        if ( !isset( $data['header'] ) ) {
            return false;
        }
        
        if ( !isset( $data['lines'] ) ) {
            return false;
        }

        return true;
    }

    public function store_job($data)
    {
        $data['header'] = json_encode($data['header']);
        $data['lines'] = json_encode($data['lines']);
        
        return PrintingJob::create($data);
    }

    public function update_job(int $id, $message)
    {
        $printing_job = PrintingJob::find($id);
        
        $status = 'Completado';

        if( $message != 'ok' )
        {
            $status = 'Pendiente';
        }

        $json_response = json_encode(
                                [ 
                                    'status' => $status,
                                    'message' => $message
                                ]
                            );
        
        if( !$json_response )
        {
            $json_response = json_encode(
                [ 
                    'status' => $status
                ]
            );
        }

        $printing_job->status = $status;
        $printing_job->log = $message;
        $printing_job->save();

        return $json_response;
    }

    public function sent_to_print($data)
    {        
        $printer_IP = $data['printer_ip'];

        $header = (object)$data['header'];
        $lines = (object)$data['lines'];

        try {
            
            $connector = new NetworkPrintConnector($printer_IP, 9100);
            $printer = new Printer($connector);

            // Initialize
            $printer->initialize();

            $printer->selectPrintMode(32);
            $printer->setJustification( Printer::JUSTIFY_CENTER );
            $printer->text( $header->transaction_label . "\n");
            $printer->selectPrintMode(56);
            $printer->text( $header->number_label . "\n");
            $printer->setJustification(); // Reset

            $printer->selectPrintMode(41);
            $printer->text( "Fecha: " . $header->date . "\n");
            $printer->text( "Cliente: " . $header->customer_name . "\n");
            $printer->text( "Atiende: " . $header->seller_label . "\n");
            $printer->text( "Detalle: " . $header->detail . "\n\n");

            $printer->text( "___________________________\n");
            $printer->text( " CANT.        ITEM \n");

            $printer->selectPrintMode(49);

            foreach ($lines as $line) {

                $item_name = $line['item'];

                $end = 20;
                
                $printer->text( " " . $line['quantity'] . "-" . substr( $item_name, 0, $end) . "\n" );

                $length_pendiente = strlen($item_name) - $end;
                $start = $end;
                    
                while ($length_pendiente > 3) {
                    $end += 1;   

                    $printer->text( "    " . substr( $item_name, $start, $end) . "\n" );

                    $length_pendiente = $length_pendiente - $start;

                    $start = $end;
                }
            }

            $printer->selectPrintMode(); // Reset

            $printer->feed(3);
            $printer->cut();

            // Always close the printer! On some PrintConnectors, no actual data is sent until the printer is closed.
            $printer->close();

        } catch (Exception $e) {

            // Log the message locally OR use a tool like Bugsnag/Flare to log the error
            Log::debug($e->getMessage());
         
            return $e->getMessage();
        }

        return 'ok';
    }

    public function get_jobs( $data )
    {
        $array_wheres = [
            ['id','>',0]
        ];

        if (isset($data['status'])) {
            $array_wheres = array_merge( $array_wheres, [
                ['status','=',$data['status']]
            ]);
        }

        return PrintingJob::where( $array_wheres )
                        ->orderBy('created_at','DESC')
                        ->get();
    }

    public function reprint_job( $id )
    {

    }

    public function cancel_job( $id )
    {
        return PrintingJob::find($id)->update(
            ['status'=>'Eliminado']
        );
    }
}
