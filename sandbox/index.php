<?php
$midas = new Midas();

// Commands, Data and Questions are core features
// Can extend, which creates a method that returns a new instance of an extension object
// stream() is an extension

class ExtensionClass {
    public function __construct(Midas $midas, $data = null, array $params = null)
    {
        $this->midas = $midas;
        $this->data = $data;
        $this->params = $params;
    }
}

$midas->loadExtension('Michaels\Midas\Streamer');
$midas->stream($data); // now returns new Streamer with this instance of $midas as a dependency


$midas->_someCommand($data);

$midas->is($data); // returns a QuestionerObject -> addQuestion()
$midas->stream($data); // returns a StreamerObject
$midas->make($data); // returns a MidasDataObject

$midas->addCommand();
$midas->addQuestion();
$midas->addData($data);


$midas->addCommand('michael.equation');
$midas->addCommand('nicole.equation.blue');

$midas->michael_equation($data, []);
$midas->nicle_equation_blue($data, []);

