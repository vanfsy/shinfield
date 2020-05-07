<?php
App::import('vendor', 'tcpdf/tcpdf');
class TcpdfHelper extends TCPDF {
    public $helpers = array();
    function __construct($options) {
        $defaults = array(
            'orientation'   => 'P',
            'unit'          => 'mm',
            'format'        => 'A4',
            'unicode'       => true,
            'encoding'      => "UTF-8",
        );
        $options =  (is_array($options)) ? am($defaults, $options) : $defaults;
        extract(am($defaults, $options));
        parent::__construct($orientation, $unit, $format, $unicode, $encoding);
    }
}
?>
