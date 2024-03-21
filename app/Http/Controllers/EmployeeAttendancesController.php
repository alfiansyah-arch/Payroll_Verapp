<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;


class EmployeeAttendances extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nim = Auth::guard('magang')->user()->nim;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nim',$nim)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nim = Auth::guard('magang')->user()->nim;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nim',$nim)->count();
        if($cek > 0){
            $ket = "out";
        } else {
            $ket = "in";
        }
        $image = $request->image;
        $lokasi = $request->lokasi;
        $folderPath = "public/uploads/absensi";
        $formatName = $nim . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64",$image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath.$fileName;
        
        
        if($cek > 0){
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'lokasi_out' => $lokasi
            ];
            $update = DB::table('presensi')->where('tgl_presensi',$tgl_presensi)->where('nim',$nim)->update($data_pulang);
            if($update){
                echo "success|Terimakasih, hati-hati dijalan|out";
                Storage::put($file, $image_base64);
            }else{
                echo "error|Maaf gagal absen|out";
            }
        }else{
            $data = [
                'nim' => $nim,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'lokasi_in' => $lokasi
            ];
            $simpan = DB::table('presensi')->insert($data);
            if($simpan){
                echo "success|Terimakasih, selamat bekerja|in";
                Storage::put($file, $image_base64);
            }else{
                echo "error|Maaf gagal absen|in";
            }
        }
        
    }

    public function editprofile()
    {
        $nim = Auth::guard('magang')->user()->nim;
        $magang = DB::table('magang')->where('nim',$nim)->first();
        return view('presensi.editprofile', compact('magang'));
    }

    public function updateprofile(Request $request)
    {
        $nim = Auth::guard('magang')->user()->nim;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $magang = DB::table('magang')->where('nim',$nim)->first();

        if($request->hasFile('foto')) {
            $foto = $nim . "." . $request->file('foto')->getClientOriginalExtension();
        }else{
            $foto = $magang->foto;
        }

        if (empty($request->password)){
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password
            ];
        }

        $update = DB::table('magang')->where('nim', $nim)->update($data);
        if($update) {
            if($request->hasFile('foto')){
                $folderPath = "public/uploads/magang";
                $request->file('foto')->storeAs($folderPath,$foto);
            }
            return Redirect::back()->with([ 'success' => 'Data Berhasil Di Update']);
        }else{
            return Redirect::back()->with([ 'error' => 'Data Gagal Di Update']);
        }
    }

    public function histori(){
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nim = Auth::guard('magang')->user()->nim;

        $histori = DB::table('presensi')
        ->whereRaw('MONTH(tgl_presensi)="' .$bulan. '"')
        ->whereRaw('YEAR(tgl_presensi)="' .$tahun. '"')
        ->where('nim', $nim)
        ->orderBy('tgl_presensi')
        ->get('*');

        return view('presensi.gethistori',compact('histori'));
    }

    public function izin()
    {
        $nim = Auth::guard('magang')->user()->nim;
        $dataizin = DB::table('pengajuan_izin')->where('nim',$nim)->get();
        return view('presensi.izin',compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nim = Auth::guard('magang')->user()->nim;
        $tgl_izin = $request->input('tgl_izin');
        $status = $request->input('status');
        $keterangan = $request->input('keterangan');

        $data = [
            'nim' => $nim,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data berhasil disimpan']);
        }else {
            return redirect('/presensi/izin')->with(['error' => 'Data gagal disimpan']);
        }

    }

    public function laporan()
    {
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        $siswa = DB::table('magang')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan','siswa'));
    }

    public function cetaklaporan(Request $request)
    {
        $nim = $request->nim;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $siswa = DB::table('magang')->where('nim', $nim)->first();

        $presensi = DB::table('presensi')
        ->where('nim', $nim)
        ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
        ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
        ->orderBy('tgl_presensi')
        ->get();

        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'siswa', 'presensi'));
    }

    public function izinsakit()
    {
        $izinsakit = DB::table('pengajuan_izin')
        ->join('magang','pengajuan_izin.nim','magang.nim')
        ->orderBy('tgl_izin', 'desc')
        ->get();
        return view ('presensi.izinsakit', compact('izinsakit'));
    }
    
}
