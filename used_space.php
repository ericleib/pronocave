<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<HTML>
<HEAD>
<TITLE>Espace disque utilisé</TITLE>
</HEAD>
<BODY>
<H1>Espace disque utilisé</H1>
<TABLE>
    <TR>
        <TD><B>Nom</B></TD>
        <TD ALIGN="CENTER" COLSPAN="2"><B>Taille</B></TD>
    </TR>
    <TR>
        <TD COLSPAN="3"><HR></TD>
    </TR>
<?php

function format_size($value) {
    $string = "";

    // 1 Mo = 1000000 octet
    // 1 Ko = 1000 octet
    
    if (ereg("^[0-9]{1,}$", $value)) {
        if ($value>=1000000) {
            $string = sprintf('<TD ALIGN="RIGHT">%01.2f', $value/1000000);
            $string = ereg_replace("[\.]{1}[0]{1,}$", "", $string) . '</TD><TD ALIGN="LEFT"> Mo</TD>';
        } else if ($value>=1000) {
            $string = sprintf('<TD ALIGN="RIGHT">%01.2f', $value/1000);
            $string = ereg_replace("[\.]{1}[0]{1,}$", "", $string) . '</TD><TD ALIGN="LEFT"> Ko</TD>';
        } else if ($value>=0) {
            $string = '<TD ALIGN="RIGHT">' . $value . '</TD><TD ALIGN="LEFT"> octet'; 
            if ($value>0) 
                $string .= "s";
            $string .= '</TD>';
        } else {
            $string = '<TD ALIGN="RIGHT">' . $value . '</TD>';
        }
    } else {
        $string = $value;
    }

    return $string;
}

function DirSize($path , $recursive = TRUE) {
    $result = 0;
    
    if(!is_dir($path) || !is_readable($path))
        return 0;
    
    $fd = dir($path);
    
    while($file = $fd->read()) {
        if(($file != ".") && ($file != "..")) {
            if(@is_dir("$path$file/"))
                $result += $recursive ? DirSize("$path$file/") : 0;
            else
                $result += filesize("$path$file");
        }
    }
    
    $fd->close();
    return $result;
}

function ListAndSize($path) {
    $size = 0;
    $fd   = dir($path);
    
    while($file = $fd->read()) {
        if(($file != ".") && ($file != "..")) { 
            if(@is_dir("$path$file/")) {
                $dirsize = DirSize("$path$file/");
                $size += $dirsize;
                echo '
    <TR>
        <TD><IMG SRC="/icons/folder.gif">&nbsp;<A HREF="' . $file . '">' . $file . '</A></TD>
        ' . format_size($dirsize) . '
    </TR>';
            } else {
                $filesize = filesize("$path$file");
                $size += $filesize;
                echo '
    <TR>
        <TD><IMG SRC="/icons/layout.gif">&nbsp;' . $file . '</TD>
        ' . format_size($filesize) . '
    </TR>';
            }
        } 
    }
    
    $fd->close();
    echo '
    <TR>
        <TD COLSPAN="3"><HR></TD>
    </TR>
    <TR>
        <TD><B>Total</B></TD>
        <B>' . format_size($size) .'</B>
    </TR>
</TABLE>';
}

ListAndSize("./");

?>

</BODY>
</HTML>