<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

if (($_SESSION['koperasi_c45_id'])==2) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "proses_mining.php";
?>

<div class="super_sub_content">
    <div class="container">
        <div class="row">
            <?php
            $query = $db_object->db_query("SELECT * FROM data_uji");
            $id_rule = array();
            $it = 0;
            while ($bar = $db_object->db_fetch_array($query)) {
                //ambil data uji
                $n_status_pernikahan = $bar['status_pernikahan'];
                $n_status_rumah = $bar['status_rumah'];
                $n_penghasilan = $bar['penghasilan'];
                $n_umur = $bar['umur'];
                $n_kelas_asli = $bar['kelas_asli'];

                $hasil = klasifikasi($db_object, $n_status_pernikahan, $n_status_rumah, $n_penghasilan, $n_umur);

                $keputusan = $hasil['keputusan'];
                $id_rule_keputusan = $hasil['id_rule'];
                $it++;
                $db_object->db_query("UPDATE data_uji SET kelas_hasil='$keputusan', id_rule='$id_rule_keputusan' WHERE id=$bar[0]");
            }//end loop data uji
//menampilkan data uji dengan hasil prediksi
            $sql = $db_object->db_query("SELECT * FROM data_uji");
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="sample-table-1">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Status Pernikahan</th>
                            <th>Status Rumah</th>
                            <th>Penghasilan</th>
                            <th>Umur</th>
                            <th>Kelas Asli</th>
                            <th>Hasil Prediksi</th>
                            <th>Id Rule</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        while ($row = $db_object->db_fetch_array($sql)) {
                            if ($row['kelas_asli'] == $row['kelas_hasil']) {
                                $ketepatan = "benar";
                            } else {
                                $ketepatan = "salah";
                            }
                            echo "<tr>";
                            echo "<td>" . $no . "</td>";
                            echo "<td>" . $row['nama'] . "</td>";
                            echo "<td>" . $row['status_pernikahan'] . "</td>";
                            echo "<td>" . $row['status_rumah'] . "</td>";
                            echo "<td>" . $row['penghasilan'] . "</td>";
                            echo "<td>" . $row['umur'] . "</td>";
                            echo "<td>" . $row['kelas_asli'] . "</td>";
                            echo "<td>" . $row['kelas_hasil'] . "</td>";
                            echo "<td>" . $row['id_rule'] . "</td>";
                            echo "<td>" . ($ketepatan == 'benar' ? "<b>" . $ketepatan . "</b>" : $ketepatan) . "</td>";
                            echo "</tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
//perhitungan akurasi
            $que = $db_object->db_query("SELECT * FROM data_uji");
            $jumlah_uji = $db_object->db_num_rows($que);
            $TP=0; $FN=0; $TN=0; $FP=0; $kosong=0;
            //$TA = $FB = $FC = $FD = $FE = $TF = $FG = $FH = $FI = $FJ = $TK = $FL = $FM = $FN = $FO = $TP = 0;
            $kosong =$tepat = $tidak_tepat =  0;
            while ($row = $db_object->db_fetch_array($que)) {
                $asli = $row['kelas_asli'];
                $prediksi = $row['kelas_hasil'];
                if($asli==$prediksi){
                    if($asli=='lancar'){
                        $TP++;
                    }
                    else{
                        $TN++;
                    }
                    $tepat++;
                }
                else{
                    if($asli=='lancar'){
                        $FN++;
                    }
                    else{
                        $FP++;
                    }
                    $tidak_tepat++;
                }
            }
//            $tepat = ($TA + $TF + $TK + $TP);
//            $tidak_tepat = ($FB + $FC + $FD + $FE + $FG + $FH + $FI + $FJ + $FL + $FM + $FN + $FO + $kosong);
            $akurasi = ($tepat / $jumlah_uji) * 100;
            $laju_error = ($tidak_tepat / $jumlah_uji) * 100;
                        $sensitivitas=round(($TP/($TP+$FN))*100, 2);
                        $spesifisitas=round(($TN/($FP+$TN))*100, 2);

            $akurasi = round($akurasi, 2);
            $laju_error = round($laju_error, 2);
            echo "<br><br>";
            echo "<center><h4>";
            echo "Jumlah prediksi: $jumlah_uji<br>";
            echo "Jumlah tepat: $tepat<br>";
            echo "Jumlah tidak tepat: $tidak_tepat<br>";
            if ($kosong != 0) {
                echo "Jumlah data yang prediksinya kosong: $kosong<br></h4>";
            }
            echo "<h2>AKURASI = $akurasi %<br>";
            echo "LAJU ERROR = $laju_error %<br><h2>";
            
            echo "<h3>";
            echo "TP=$TP  TN=$TN  FP=$FP  FN=$FN<br>";
            echo "Sensitivitas = $sensitivitas %<br>";
            echo "Spesifisitas = $spesifisitas %<br>";
                echo "</h3>";
            ?>
        </div>
    </div>
</div>