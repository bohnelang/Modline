<?

$hfrontmin       = 0.50;
$hsyncmin        = 1.20;
$hbackmin        = 1.25;
$hblankmin       = 4.00;
$hsfmax          = 60.0;

$vfrontmin       = 0.0;
$vsyncmin        = 45.0;
$vbackmin        = 500.0;
$vblankmin       = 600.0;
$vsfmax          = 90.0;

$hfl = 1;
$vfl = 1;

$step = 10.0;
$ende=0;


if(isset( $_GET["RE_VALUE"] )) $re_value = $_GET["RE_VALUE"];
if( empty($re_value) ) die("Exit");

list($hr,$vr)=sscanf($re_value,"%s %s");

if( empty ($hr) || ($hr>10000) || ($hr<=10) ) die("Exit");
if( empty ($vr) || ($vr>10000) || ($vr<=10) ) die("Exit");

if(isset($_GET["FREQ"])) $s_rr = $_GET["FREQ"]; else $s_rr=60;
if( empty ($s_rr) || ($s_rr>1000) || ($s_rr<=25) ) die("Exit");

if(isset($_GET["MODE"])){ $mode = $_GET["MODE"]; $dcf = $s_rr;}  else {$mode=1; $dcf = 10.0;}


#$mode=2;
#$dcf=58;

if($s_rr<20.0) $s_rr =20.0;

do{
        $rr = 1000000.0 * $dcf / ($hfl * $vfl);
        $hsf = 1000.0 * $dcf / $hfl;

        $hfront = $hfrontmin * $dcf + $hr;
        if( (integer)($hfront) % 8 ) $hfront = 8 * (1 + (float)((integer)($hfront/8)));

        $hsync = $hsyncmin * $dcf + $hfront;
        if( (integer)($hsync)%8) $hsync = 8 * (1+ (float)((integer)($hsync/8)));

        $hblank = $hblankmin * $dcf;
        $hfl = $hr + $hblank;
        if((integer)($hfl)%8) $hfl = 8 * (1+(float)((integer)($hfl/8)));

        $vtick = $hfl / $dcf;
        $vfront = $vr + $vfrontmin / $vtick;

        $vsync = $vfront + $vsyncmin /$vtick;
        $vback = $vbackmin /$vtick;
        $vblank = $vblankmin / $vtick;

        $vfl = $vsync + $vback;
        if( $vfl < $vr+ $vblank) $vfl = $vr + $vblank;

        if($mode==1){
                $v1 = (integer)($rr*1000.0);
                $v2 = (integer)($s_rr*1000.0);

                if( $v1 == $v2 ) $ende =1;
                else if( $v1 < $v2 ) $dcf += $step;
                else if( $v1 > $v2 ) { $dcf -= $step; $step /= 10.0 ;}
        } else {
                $rr = 1000000.0 * $dcf / ($hfl * $vfl);
                $hsf = 1000.0 * $dcf / $hfl;
                $ende=1;
        }
} while( $ende==0);
		printf("<html><body><pre>\n");

        printf("  Horizontal Resolution:   %4.0f \n",(float)$hr);
        printf("  Vertical Resolution:     %4.0f \n",(float)$vr);
        printf("  Vertical Refresh Rate:   %4.2f Hz \n",(float)$rr);
        printf("  Horizontal Refresh Rate: %4.2f KHz \n",(float)$hsf);
        printf("  Dot Clock Frequence:     %4.2f MHz \n",(float)$dcf);
        printf("\n");
        printf(" # V-freq: %4.2f Hz  // h-freq: %4.2f KHz\n",(float)$rr,(float)$hsf);
        printf(" # HorizSync %d.0 - %d.0 \n", (integer) ($hsf-5), (integer) ($hsf+5));
        printf(" # VertRefresh %d.0 - %d.0 \n", (integer) ($rr-5), (integer) ($rr+5));
        printf(" Modeline \"%dx%d\" %4.2f  %4d %4d %4d %4d  %4d %4d %4d %4d \n",(integer)($hr),(integer)($vr),(float)$dcf,(integer)($hr),(integer)($hfront),(integer)($hsync),(integer)($hfl),(integer)($vr),(integer)($vfront),(integer)($vsync),(integer)($vfl));
		printf("</pre></bpdy></html>");
?>
