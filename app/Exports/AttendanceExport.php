<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $serviceId;
    protected $startDate;
    protected $endDate;

    // Konstruktor untuk menerima parameter filter
    public function __construct($serviceId = null, $startDate = null, $endDate = null)
    {
        $this->serviceId = $serviceId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Fungsi untuk mengambil data dari database
    public function collection()
    {
        $query = Attendance::with('attendanceTransaction', 'activityTransaction.student')
            ->when($this->serviceId, function ($query) {
                if ($this->serviceId) {
                    $query->whereHas('attendanceTransaction', function ($query) {
                        $query->where('service_id', $this->serviceId);
                    });
                }
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereHas('attendanceTransaction', function ($query) {
                    $query->whereBetween('date_attendance', [$this->startDate, $this->endDate]);
                });
            })
            ->get();

        $data = [];
        foreach ($query as $attendance) {
            $data[] = [
                'NO' => $attendance->activityTransaction->student->id,
                'Nama Siswa' => $attendance->activityTransaction->student->student_name,
                'Tanggal' => $attendance->attendanceTransaction->date_attendance,
                'Status Kehadiran' => $attendance->check_in_status,
                'Jam Datang' => $attendance->check_in_time,
                'Jam Pulang' => $attendance->check_out_time,
            ];
        }

        return collect($data);
    }

    // Fungsi untuk judul kolom di Excel
    public function headings(): array
    {
        return [
            'NO',
            'Nama Siswa',
            'Tanggal',
            'Status Kehadiran',
            'Jam Datang',
            'Jam Pulang',
        ];
    }
}
