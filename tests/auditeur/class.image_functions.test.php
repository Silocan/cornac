<?php 
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */
include_once('Auditeur_Framework_TestCase.php');

class image_functions_Test extends Auditeur_Framework_TestCase
{
    public function testimage_functions()  { 
        $this->expected = array( 'gd_info',
'image2wbmp',
'imagealphablending',
'imageantialias',
'imagearc',
'imagechar',
'imagecharup',
'imagecolorallocate',
'imagecolorallocatealpha',
'imagecolorat',
'imagecolorclosest',
'imagecolorclosestalpha',
'imagecolorclosesthwb',
'imagecolordeallocate',
'imagecolorexact',
'imagecolorexactalpha',
'imagecolormatch',
'imagecolorresolve',
'imagecolorresolvealpha',
'imagecolorset',
'imagecolorsforindex',
'imagecolorstotal',
'imagecolortransparent',
'imageconvolution',
'imagecopy',
'imagecopymerge',
'imagecopymergegray',
'imagecopyresampled',
'imagecopyresized',
'imagecreate',
'imagecreatefromgd2',
'imagecreatefromgd2part',
'imagecreatefromgd',
'imagecreatefromgif',
'imagecreatefromjpeg',
'imagecreatefrompng',
'imagecreatefromstring',
'imagecreatefromwbmp',
'imagecreatefromxbm',
'imagecreatefromxpm',
'imagecreatetruecolor',
'imagedashedline',
'imagedestroy',
'imageellipse',
'imagefill',
'imagefilledarc',
'imagefilledellipse',
'imagefilledpolygon',
'imagefilledrectangle',
'imagefilltoborder',
'imagefilter',
'imagefontheight',
'imagefontwidth',
'imageftbbox',
'imagefttext',
'imagegammacorrect',
'imagegd2',
'imagegd',
'imagegif',
'imagegrabscreen',
'imagegrabwindow',
'imageinterlace',
'imageistruecolor',
'imagejpeg',
'imagelayereffect',
'imageline',
'imageloadfont',
'imagepalettecopy',
'imagepng',
'imagepolygon',
'imagepsbbox',
'imagepsencodefont',
'imagepsextendfont',
'imagepsfreefont',
'imagepsloadfont',
'imagepsslantfont',
'imagepstext',
'imagerectangle',
'imagerotate',
'imagesavealpha',
'imagesetbrush',
'imagesetpixel',
'imagesetstyle',
'imagesetthickness',
'imagesettile',
'imagestring',
'imagestringup',
'imagesx',
'imagesy',
'imagetruecolortopalette',
'imagettfbbox',
'imagettftext',
'imagetypes',
'imagewbmp',
'imagexbm',
'iptcembed',
'iptcparse',
'jpeg2wbmp',
'png2wbmp',
);
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>