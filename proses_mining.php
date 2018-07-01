<?php
function format_decimal($value){
    return round($value, 3);
}

//fungsi utama
function proses_DT($db_object, $parent, $kasus_cabang1, $kasus_cabang2) {
    echo "cabang 1<br>";
    pembentukan_tree($db_object, $parent, $kasus_cabang1);
    echo "cabang 2<br>";
    pembentukan_tree($db_object, $parent, $kasus_cabang2);
}

//fungsi proses dalam suatu kasus data
function pembentukan_tree($db_object, $N_parent, $kasus) {
    //mengisi kondisi
    if ($N_parent != '') {
        $kondisi = $N_parent . " AND " . $kasus;
    } else {
        $kondisi = $kasus;
    }
    echo $kondisi . "<br>";
    //cek data heterogen / homogen???
    $cek = cek_heterohomogen($db_object, 'kelas_asli', $kondisi);
    if ($cek == 'homogen') {
        echo "<br>LEAF ||";
        $sql_keputusan = $db_object->db_query("SELECT DISTINCT(kelas_asli) FROM "
                . "data_latih WHERE $kondisi");
        $row_keputusan = $db_object->db_fetch_array($sql_keputusan);
        $keputusan = $row_keputusan['0'];
        //insert atau lakukan pemangkasan cabang
        pangkas($db_object, $N_parent, $kasus, $keputusan);
    }//jika data masih heterogen
    else if ($cek == 'heterogen') {
        //cek jumlah data
        // $jumlah = jumlah_data($kondisi);
        // if($jumlah<=3){
        //     echo "<br>LEAF ";
        //     $Nlancar = $kondisi." AND kelas_asli='baik'";
        //     $Nmacet = $kondisi." AND kelas_asli='kurang'";
        //     $jumlahlancar = jumlah_data("$Nlancar");
        //     $jumlahmacet = jumlah_data("$Nmacet");
        //     if($jumlahlancar <= $jumlahmacet){
        //         $keputusan = 'kurang';
        //     }else{
        //         $keputusan = 'baik';
        //     }
        //     //insert atau lakukan pemangkasan cabang
        //     pangkas($N_parent , $kasus , $keputusan);
        // }
        // //lakukan perhitungan
        // else{
        //jika kondisi tidak kosong kondisi_kelas_asli=tambah and
        $kondisi_kelas_asli = '';
        if ($kondisi != '') {
            $kondisi_kelas_asli = $kondisi . " AND ";
        }
        $jml_lancar = jumlah_data($db_object, "$kondisi_kelas_asli kelas_asli='Lancar'");
        $jml_macet = jumlah_data($db_object, "$kondisi_kelas_asli kelas_asli='Macet'");
        
        $jml_total = $jml_lancar + $jml_macet ;
        echo "Jumlah data = " . $jml_total . "<br>";
        echo "Jumlah Lancar = " . $jml_lancar . "<br>";
        echo "Jumlah Macet = " . $jml_macet . "<br>";

        //hitung entropy semua
        $entropy_all = hitung_entropy($jml_lancar, $jml_macet);
        echo "Entropy All = " . $entropy_all . "<br>";

        $nilai_status_pernikahan = array();
        $nilai_status_pernikahan = cek_nilaiAtribut($db_object, 'status_pernikahan',$kondisi);
        $jmlStatusPernikahan = count($nilai_status_pernikahan);

        echo "<div class='table-responsive'>
                <table class='table table-striped table-bordered table-hover' id='sample-table-1'>
                    <thead>";
        echo "<tr>"
                . "<th>Nilai Atribut</th> "
                . "<th>Jumlah data</th> "
                . "<th>Jumlah Lancar</th> "
                . "<th>Jumlah Macet</th> "
                . "<th>Entropy</th> "
                . "<th>Gain</th>"
                . "<tr>";
        echo "</thead>"
        . " <tbody>";

        $db_object->db_query("TRUNCATE gain");
        //hitung gain atribut KATEGORIKAL
        hitung_gain($db_object, $kondisi, "status_rumah", $entropy_all, "status_rumah='rumah sendiri'", "status_rumah='kontrak'", "", "", "");
        
        //hitung gain atribut KATEGORIKAL
        if($jmlStatusPernikahan!=1){
            $NA1StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[0]'";
            $NA2StatusPernikahan="";
            $NA3StatusPernikahan="";
            if($jmlStatusPernikahan==2){
                    $NA2StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[1]'";
            }else if ($jmlStatusPernikahan==3){
                    $NA2StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[1]'";
                    $NA3StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[2]'";
            }				
            hitung_gain($db_object, $kondisi , "status_pernikahan", $entropy_all , $NA1StatusPernikahan, $NA2StatusPernikahan, $NA3StatusPernikahan, "" , "");	
        }

        //hitung gain atribut Numerik
        //Penghasilan
        hitung_gain($db_object, $kondisi, "Penghasilan=1000000", $entropy_all, "penghasilan<=1000000", "penghasilan>1000000", "", "", "");
        hitung_gain($db_object, $kondisi, "Penghasilan=2000000", $entropy_all, "penghasilan<=2000000", "penghasilan>2000000", "", "", "");
        hitung_gain($db_object, $kondisi, "Penghasilan=3000000", $entropy_all, "penghasilan<=3000000", "penghasilan>3000000", "", "", "");
        
        //Umur
        hitung_gain($db_object, $kondisi, "Umur=35", $entropy_all, "umur<=35", "umur>35", "", "", "");
        hitung_gain($db_object, $kondisi, "Umur=40", $entropy_all, "umur<=40", "umur>40", "", "", "");
        hitung_gain($db_object, $kondisi, "Umur=45", $entropy_all, "umur<=45", "umur>45", "", "", "");
        
        echo "</tbody>";
        echo "</table>";
        //ambil nilai gain terBesar
        $sql_max = $db_object->db_query("SELECT MAX(gain) FROM gain");
        $row_max = $db_object->db_fetch_array($sql_max);
        $max_gain = $row_max[0];
        $sql = $db_object->db_query("SELECT * FROM gain WHERE gain=$max_gain");
        $row = $db_object->db_fetch_array($sql);
        $atribut = $row[2];
        echo "Atribut terpilih = " . $atribut . ", dengan nilai gain = " . $max_gain . "<br>";
        echo "<br>================================<br>";

        //jika max gain = 0 perhitungan dihentikan dan mengambil keputusan
        if ($max_gain == 0) {
            echo "<br>LEAF ";
            $Nlancar = $kondisi . " AND kelas_asli='Lancar'";
            $Nmacet = $kondisi . " AND kelas_asli='Macet'";
            $jumlahlancar = jumlah_data($db_object, "$Nlancar");
            $jumlahmacet = jumlah_data($db_object, "$Nmacet");
            if($jumlahlancar >= $jumlahmacet ) {
                $keputusan = 'Lancar';
            }
            else {
                $keputusan = 'Macet';
            }
            //insert atau lakukan pemangkasan cabang
            pangkas($db_object, $N_parent, $kasus, $keputusan);
        }
        //jika max_gain >0 lanjut..
        else {
            //status rumah terpilih
            if ($atribut == "status_rumah") {
                proses_DT($db_object, $kondisi, "($atribut='rumah sendiri')", "($atribut='kontrak')");
            }
            
            //status pernikahan terpilih
            if ($atribut == "status_pernikahan") {
                //jika nilai atribut 3
                if($jmlStatusPernikahan==3){
                    //hitung rasio
                    $cabang = array();
                    $cabang = hitung_rasio($db_object, $kondisi , 'status_pernikahan',$max_gain,$nilai_status_pernikahan[0],$nilai_status_pernikahan[1],$nilai_status_pernikahan[2],'','');
                    $exp_cabang = explode(" , ",$cabang[1]);						
                    proses_DT($db_object, $kondisi , "($atribut='$cabang[0]')","($atribut='$exp_cabang[0]' OR $atribut='$exp_cabang[1]')");						
                }
                //jika nilai atribut 2
                else if($jmlStatusPernikahan==2){
                    proses_DT($db_object, $kondisi , "($atribut='$nilai_status_pernikahan[0]')" , "($atribut='$nilai_status_pernikahan[1]')");
                }
            }

            //Jawaban A Terpilih
            if ($atribut == "Penghasilan=1000000") {
                proses_DT($db_object, $kondisi, "(penghasilan<=1000000)", "(penghasilan>1000000)");
            } else if ($atribut == "Penghasilan=2000000") {
                proses_DT($db_object, $kondisi, "(penghasilan<=2000000)", "(penghasilan>2000000)");
            } else if ($atribut == "Penghasilan=3000000") {
                proses_DT($db_object, $kondisi, "(penghasilan<=3000000)", "(penghasilan>3000000)");
            }
            
            //Jawaban B Terpilih
            if ($atribut == "Umur=35") {
                proses_DT($db_object, $kondisi, "(umur<=35)", "(umur>35)");
            } else if ($atribut == "Umur=40") {
                proses_DT($db_object, $kondisi, "(umur<=40)", "(umur>40)");
            } else if ($atribut == "Umur=45") {
                proses_DT($db_object, $kondisi, "(umur<=45)", "(umur>45)");
            }
            
        }//end 
        //else jika max_gain>0
        // }// end jumlah<3
    }//end else if($cek=='heterogen'){
}

//==============================================================================
//fungsi cek nilai atribut
function cek_nilaiAtribut($db_object, $field , $kondisi){
    //sql disticnt		
    $hasil = array();
    if($kondisi==''){
            $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih");					
    }else{
            $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih WHERE $kondisi");					
    }
    $a=0;
    while($row = $db_object->db_fetch_array($sql)){
            $hasil[$a] = $row['0'];
            $a++;
    }	
    return $hasil;
}

//fungsi cek heterogen data
function cek_heterohomogen($db_object, $field, $kondisi) {
    //sql disticnt
    if ($kondisi == '') {
        $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih");
    } else {
        $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih WHERE $kondisi");
    }
    //jika jumlah data 1 maka homogen
    if ($db_object->db_num_rows($sql) == 1) {
        $nilai = "homogen";
    } else {
        $nilai = "heterogen";
    }
    return $nilai;
}

//fungsi menghitung jumlah data
function jumlah_data($db_object, $kondisi) {
    //sql
    if ($kondisi == '') {
        $sql = "SELECT COUNT(*) FROM data_latih $kondisi";
    } else {
        $sql = "SELECT COUNT(*) FROM data_latih WHERE $kondisi";
    }

    $query = $db_object->db_query($sql);
    $row = $db_object->db_fetch_array($query);
    $jml = $row['0'];
    return $jml;
}

//fungsi pemangkasan cabang
function pangkas($db_object, $PARENT, $KASUS, $LEAF) {
    //PEMANGKASAN CABANG
//    $sql_pangkas = $db_object->db_query("SELECT * FROM t_keputusan "
//            . "WHERE parent=\"$PARENT\" AND keputusan=\"$LEAF\"");
//    $row_pangkas = $db_object->db_fetch_array($sql_pangkas);
//    $jml_pangkas = $db_object->db_num_rows($sql_pangkas);
    //jika keputusan dan parent belum ada maka insert
//    if ($jml_pangkas == 0) {
        $sql_in = "INSERT INTO t_keputusan "
                . "(parent,akar,keputusan)"
                . " VALUES (\"$PARENT\" , \"$KASUS\" , \"$LEAF\")";
        $db_object->db_query($sql_in);
        // echo "1".$sql_in;
//    }
    //jika keputusan dan parent sudah ada maka delete
//    else {
//        $db_object->db_query("DELETE FROM t_keputusan WHERE id='$row_pangkas[0]'");
//        $exPangkas = explode(" AND ", $PARENT);
//        $jmlEXpangkas = count($exPangkas);
//        $temp = array();
//        for ($a = 0; $a < ($jmlEXpangkas - 1); $a++) {
//            $temp[$a] = $exPangkas[$a];
//        }
//        $imPangkas = implode(" AND ", $temp);
//        $akarPangkas = $exPangkas[$jmlEXpangkas - 1];
//        $que_pangkas = $db_object->db_query("SELECT * FROM t_keputusan "
//                . "WHERE parent=\"$imPangkas\" AND keputusan=\"$LEAF\"");
//        $baris_pangkas = $db_object->db_fetch_array($que_pangkas);
//        $jumlah_pangkas = $db_object->db_num_rows($que_pangkas);
//        if ($jumlah_pangkas == 0) {
//            $sql_in2 = "INSERT INTO t_keputusan "
//                    . "(parent,akar,keputusan)"
//                    . " VALUES (\"$imPangkas\" , \"$akarPangkas\" , \"$LEAF\")";
//            $db_object->db_query($sql_in2);
//            //echo "2".$sql_in2;
//        } else {
//            pangkas($db_object, $imPangkas, $akarPangkas, $LEAF);
//        }
//    }
    echo "Keputusan = " . $LEAF . "<br>================================<br>";
}

//fungsi menghitung gain
function hitung_gain($db_object, $kasus, $atribut, $ent_all, $kondisi1, $kondisi2, $kondisi3, $kondisi4, $kondisi5) {
    $data_kasus = '';
    if ($kasus != '') {
        $data_kasus = $kasus . " AND ";
    }

    //untuk atribut 2 nilai atribut	
    if ($kondisi3 == '') {
        $j_lancar1 = jumlah_data($db_object, "$data_kasus kelas_asli='Lancar' AND $kondisi1");
        $j_macet1 = jumlah_data($db_object, "$data_kasus kelas_asli='Macet' AND $kondisi1");
        $jml1 = $j_lancar1 + $j_macet1;
        
        $j_lancar2 = jumlah_data($db_object, "$data_kasus kelas_asli='Lancar' AND $kondisi2");
        $j_macet2 = jumlah_data($db_object, "$data_kasus kelas_asli='Macet' AND $kondisi2");
        $jml2 = $j_lancar2 + $j_macet2 ;
        //hitung entropy masing-masing kondisi
        $jml_total = $jml1 + $jml2;
        $ent1 = hitung_entropy($j_lancar1, $j_macet1);
        $ent2 = hitung_entropy($j_lancar2, $j_macet2);

        $gain = $ent_all - ((($jml1 / $jml_total) * $ent1) + (($jml2 / $jml_total) * $ent2));
        //desimal 3 angka dibelakang koma
        $gain = format_decimal($gain);

        echo "<tr>";
        echo "<td>" . $kondisi1 . "</td>";
        echo "<td>" . $jml1 . "</td>";
        echo "<td>" . $j_lancar1 . "</td>";
        echo "<td>" . $j_macet1 . "</td>";
        echo "<td>" . $ent1 . "</td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>" . $kondisi2 . "</td>";
        echo "<td>" . $jml2 . "</td>";
        echo "<td>" . $j_lancar2 . "</td>";
        echo "<td>" . $j_macet2 . "</td>";
        echo "<td>" . $ent2 . "</td>";
        echo "<td>" . $gain . "</td>";
        echo "</tr>";

        echo "<tr><td colspan='8'></td></tr>";
    }
     //untuk atribut 3 nilai atribut
     else if($kondisi4==''){
     	$j_lancar1 = jumlah_data($db_object, "$data_kasus kelas_asli='Lancar' AND $kondisi1");
     	$j_macet1 = jumlah_data($db_object, "$data_kasus kelas_asli='Macet' AND $kondisi1");
     	$jml1 = $j_lancar1 + $j_macet1 ;
        
     	$j_lancar2 = jumlah_data($db_object, "$data_kasus kelas_asli='Lancar' AND $kondisi2");
     	$j_macet2 = jumlah_data($db_object, "$data_kasus kelas_asli='Macet' AND $kondisi2");
     	$jml2 = $j_lancar2 + $j_macet2;
        
     	$j_lancar3 = jumlah_data($db_object, "$data_kasus kelas_asli='Lancar' AND $kondisi3");
     	$j_macet3 = jumlah_data($db_object, "$data_kasus kelas_asli='Macet' AND $kondisi3");
     	$jml3 = $j_lancar3 + $j_macet3;
        
     	//hitung entropy masing-masing kondisi
     	$jml_total = $jml1 + $jml2 + $jml3;
     	$ent1 = hitung_entropy($j_lancar1 , $j_macet1);
     	$ent2 = hitung_entropy($j_lancar2 , $j_macet2);
     	$ent3 = hitung_entropy($j_lancar3 , $j_macet3);
     	$gain = $ent_all - ((($jml1/$jml_total)*$ent1) + (($jml2/$jml_total)*$ent2) 
     				+ (($jml3/$jml_total)*$ent3));							
     	//desimal 3 angka dibelakang koma
     	$gain = format_decimal($gain);				
     	echo "<tr>";
     	echo "<td>".$kondisi1."</td>";
     	echo "<td>".$jml1."</td>";
     	echo "<td>".$j_lancar1."</td>";
     	echo "<td>".$j_macet1."</td>";
     	echo "<td>".$ent1."</td>";
     	echo "<td>&nbsp;</td>";
     	echo "</tr>";
     	echo "<tr>";
     	echo "<td>".$kondisi2."</td>";
     	echo "<td>".$jml2."</td>";
     	echo "<td>".$j_lancar2."</td>";
     	echo "<td>".$j_macet2."</td>";
     	echo "<td>".$ent2."</td>";
     	echo "<td>&nbsp;</td>";
     	echo "</tr>";
     	echo "<tr>";
     	echo "<td>".$kondisi3."</td>";
     	echo "<td>".$jml3."</td>";
     	echo "<td>".$j_lancar3."</td>";
     	echo "<td>".$j_macet3."</td>";
     	echo "<td>".$ent3."</td>";
     	echo "<td>".$gain."</td>";
     	echo "</tr>";
     	echo "<tr><td colspan='8'></td></tr>";
     }
    // //untuk atribut 4 nilai atribut
    // //untuk atribut 5 nilai atribut	
    
    $db_object->db_query("INSERT INTO gain VALUES ('','1','$atribut','$gain')");
}

//fungsi menghitung entropy
function hitung_entropy($nilai1, $nilai2) {
    $total = $nilai1 + $nilai2;
    //jika salah satu nilai 0, maka entropy 0
//    if ($nilai1 == 0 || $nilai2 == 0 || $nilai3 == 0 || $nilai4 == 0) {
//        $entropy = 0;
//    }
//    else {
    $atribut1 = (-($nilai1 / $total) * (log(($nilai1 / $total), 2)));
    $atribut2 = (-($nilai2 / $total) * (log(($nilai2 / $total), 2)));
    
    $atribut1 = is_nan($atribut1)?0:$atribut1;
    $atribut2 = is_nan($atribut2)?0:$atribut2;
    
        $entropy = $atribut1 + 
                    $atribut2 ;
//    }
    //desimal 3 angka dibelakang koma
    $entropy = format_decimal($entropy);
    return $entropy;
}

//fungsi hitung rasio
function hitung_rasio($db_object, $kasus , $atribut , $gain , $nilai1 , $nilai2 , $nilai3 , $nilai4 , $nilai5){				
    $data_kasus = '';
    if($kasus!=''){
        $data_kasus = $kasus." AND ";
    }
    //menentukan jumlah nilai
    $jmlNilai=5;
    //jika nilai 5 kosong maka nilai atribut-nya 4
    if($nilai5==''){
        $jmlNilai=4;
    }
    //jika nilai 4 kosong maka nilai atribut-nya 3
    if($nilai4==''){
        $jmlNilai=3;
    }
    $db_object->db_query("TRUNCATE rasio_gain");		
    if($jmlNilai==3){
        $opsi11 = jumlah_data($db_object, "$data_kasus ($atribut='$nilai2' OR $atribut='$nilai3')");
        $opsi12 = jumlah_data($db_object, "$data_kasus $atribut='$nilai1'");
        $tot_opsi1=$opsi11+$opsi12;
        $opsi21 = jumlah_data($db_object, "$data_kasus ($atribut='$nilai3' OR $atribut='$nilai1')");
        $opsi22 = jumlah_data($db_object, "$data_kasus $atribut='$nilai2'");
        $tot_opsi2=$opsi21+$opsi22;
        $opsi31 = jumlah_data($db_object, "$data_kasus ($atribut='$nilai1' OR $atribut='$nilai2')");
        $opsi32 = jumlah_data($db_object, "$data_kasus $atribut='$nilai3'");
        $tot_opsi3=$opsi31+$opsi32;			
        //hitung split info
        $opsi1 = (-($opsi11/$tot_opsi1)*(log(($opsi11/$tot_opsi1),2))) + (-($opsi12/$tot_opsi1)*(log(($opsi12/$tot_opsi1),2)));
        $opsi2 = (-($opsi21/$tot_opsi2)*(log(($opsi21/$tot_opsi2),2))) + (-($opsi22/$tot_opsi2)*(log(($opsi22/$tot_opsi2),2)));
        $opsi3 = (-($opsi31/$tot_opsi3)*(log(($opsi31/$tot_opsi3),2))) + (-($opsi32/$tot_opsi3)*(log(($opsi32/$tot_opsi3),2)));
        //desimal 3 angka dibelakang koma
        $opsi1 = format_decimal($opsi1);
        $opsi2 = format_decimal($opsi2);
        $opsi3 = format_decimal($opsi3);										
        //hitung rasio
        $rasio1 = $gain/$opsi1;
        $rasio2 = $gain/$opsi2;
        $rasio3 = $gain/$opsi3;
        //desimal 3 angka dibelakang koma
        $rasio1 = format_decimal($rasio1);
        $rasio2 = format_decimal($rasio2);
        $rasio3 = format_decimal($rasio3);
            //cetak
            echo "Opsi 1 : <br>jumlah ".$nilai2."/".$nilai3." = ".$opsi11.
                    "<br>jumlah ".$nilai1." = ".$opsi12.
                    "<br>Split = ".$opsi1.
                    "<br>Rasio = ".$rasio1."<br>";
            echo "Opsi 2 : <br>jumlah ".$nilai3."/".$nilai1." = ".$opsi21.
                    "<br>jumlah ".$nilai2." = ".$opsi22.
                    "<br>Split = ".$opsi2.
                    "<br>Rasio = ".$rasio2."<br>";
            echo "Opsi 3 : <br>jumlah ".$nilai1."/".$nilai2." = ".$opsi31.
                    "<br>jumlah ".$nilai3." = ".$opsi32.
                    "<br>Split = ".$opsi3.
                    "<br>Rasio = ".$rasio3."<br>";

            //insert 
            $db_object->db_query("INSERT INTO rasio_gain VALUES 
                                    ('' , 'opsi1' , '$nilai1' , '$nilai2 , $nilai3' , '$rasio1'),
                                    ('' , 'opsi2' , '$nilai2' , '$nilai3 , $nilai1' , '$rasio2'),
                                    ('' , 'opsi3' , '$nilai3' , '$nilai1 , $nilai2' , '$rasio3')");
    }
    
    $sql_max = $db_object->db_query("SELECT MAX(rasio_gain) FROM rasio_gain");
    $row_max = $db_object->db_fetch_array($sql_max);	
    $max_rasio = $row_max['0'];
    $sql = $db_object->db_query("SELECT * FROM rasio_gain WHERE rasio_gain=$max_rasio");
    $row = $db_object->db_fetch_array($sql);	
    $opsiMax = array();
    $opsiMax[0] = $row[2];
    $opsiMax[1] = $row[3];		
    echo "<br>=========================<br>";
    return $opsiMax;		
}


function klasifikasi($db_object, $n_status_rumah, $n_usia, $n_sekolah, $n_jawaban_a, $n_jawaban_b, $n_jawaban_c, $n_jawaban_d) {

    $sql = $db_object->db_query("SELECT * FROM t_keputusan");
    $keputusan = $id_rule_keputusan = "";
    while ($row = $db_object->db_fetch_array($sql)) {
        //menggabungkan parent dan akar dengan kata AND
        if ($row['parent'] != '') {
            $rule = $row['parent'] . " AND " . $row['akar'];
        } else {
            $rule = $row['akar'];
        }
        //mengubah parameter
        $rule = str_replace("<=", " k ", $rule);
        $rule = str_replace("=", " s ", $rule);
        $rule = str_replace(">", " l ", $rule);
        //mengganti nilai
        $rule = str_replace("status_rumah", "'$n_status_rumah'", $rule);
        $rule = str_replace("usia", "'$n_usia'", $rule);
        $rule = str_replace("sekolah", "'$n_sekolah'", $rule);
        $rule = str_replace("jawaban_a", "'$n_jawaban_a'", $rule);
        $rule = str_replace("jawaban_b", "$n_jawaban_b", $rule);
        $rule = str_replace("jawaban_c", "$n_jawaban_c", $rule);
        $rule = str_replace("jawaban_d", "$n_jawaban_d", $rule);
        //menghilangkan '
        $rule = str_replace("'", "", $rule);
        //explode and
        $explodeAND = explode(" AND ", $rule);
        $jmlAND = count($explodeAND);
        //menghilangkan ()
        $explodeAND = str_replace("(", "", $explodeAND);
        $explodeAND = str_replace(")", "", $explodeAND);
        //deklarasi bol
        $bolAND=array();
        $n=0;
        while($n<$jmlAND){
            //explode or
            $explodeOR = explode(" OR ",$explodeAND[$n]);
            $jmlOR = count($explodeOR);	
            //deklarasi bol
            $bol=array();
            $a=0;
            while($a<$jmlOR){				
                //pecah  dengan spasi
                $exrule2 = explode(" ",$explodeOR[$a]);
                $parameter = $exrule2[1];				
                if($parameter=='s'){
                    //pecah  dengan s
                    $explodeRule = explode(" s ",$explodeOR[$a]);
                    //nilai true false						
                    if($explodeRule[0]==$explodeRule[1]){
                            $bol[$a]="Benar";
                    }else if($explodeRule[0]!=$explodeRule[1]){
                            $bol[$a]="Salah";
                    }
                }else if($parameter=='k'){
                    //pecah  dengan k
                    $explodeRule = explode(" k ",$explodeOR[$a]);
                    //nilai true false
                    if($explodeRule[0]<=$explodeRule[1]){
                            $bol[$a]="Benar";
                    }else{
                            $bol[$a]="Salah";
                    }
                }else if($parameter=='l'){
                    //pecah dengan s
                    $explodeRule = explode(" l ",$explodeOR[$a]);
                    //nilai true false
                    if($explodeRule[0]>$explodeRule[1]){
                            $bol[$a]="Benar";
                    }else{
                            $bol[$a]="Salah";
                    }
                }				
                $a++;
            }
            //isi false
            $bolAND[$n]="Salah";
            $b=0;			
            while($b<$jmlOR){
                //jika $bol[$b] benar bolAND benar
                if($bol[$b]=="Benar"){
                        $bolAND[$n]="Benar";
                }
                $b++;
            }			
            $n++;
        }
        //isi boolrule
        $boolRule="Benar";
        $a=0;
        while($a<$jmlAND){			
                //jika ada yang salah boolrule diganti salah
                if($bolAND[$a]=="Salah"){
                        $boolRule="Salah";
                        break;
                }						
                $a++;
        }		
        if($boolRule=="Benar"){
            $keputusan=$row['keputusan'];
            $id_rule_keputusan=$row['id'];
            break;
        }
        //jika tidak ada rule yang memenuhi kondisi data uji 
        //maka ambil rule paling bawah(ambil konisi yg paling panjang)????....
        if ($keputusan == '') {
            $que = $db_object->db_query("SELECT parent FROM t_keputusan");
            $jml = array();
            $exParent = array();
            $i = 0;
            while ($row_baris = $db_object->db_fetch_array($que)) {
                $exParent = explode(" AND ", $row_baris['parent']);
                $jml[$i] = count($exParent);
                $i++;
            }
            $maxParent = max($jml);
            $sql_query = $db_object->db_query("SELECT * FROM t_keputusan");
            while ($row_bar = $db_object->db_fetch_array($sql_query)) {
                $explP = explode(" AND ", $row_bar['parent']);
                $jmlT = count($explP);
                if ($jmlT == $maxParent) {
                    $keputusan = $row_bar['keputusan'];
                    $id_rule[$it] = $row_bar['id'];
                    $id_rule_keputusan = $row_bar['id'];
                    break;
                }
            }
        }
    }//end loop t_keputusan

    return array('keputusan' => $keputusan, 'id_rule' => $id_rule_keputusan);
}
