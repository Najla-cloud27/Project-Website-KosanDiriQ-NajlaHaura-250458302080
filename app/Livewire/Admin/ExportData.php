<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Exports\BillsExport;
use App\Exports\RoomsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportData extends Component
{
    // properti
    public $dateFrom;
    public $dateTo;
    public $billStatus;

    // export bilss
    public function exportBills()
    {
        $fileName = 'laporan-pembayaran-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(
            new BillsExport($this->dateFrom, $this->dateTo, $this->billStatus),
            $fileName
        );
    }

    // export rooms
    public function exportRooms()
    {
        $fileName = 'data-kamar-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new RoomsExport(), $fileName);
    }

    public function render()
    {
        return view('livewire.admin.export-data');
    }
}