<?php
//namespace Michaels\Midas\Test\Unit\Question;
//
//use Codeception\Specify;
//use Michaels\Midas\Questions\Manager as QuestionManager;
//
//class QuestionManagerTest extends \PHPUnit_Framework_TestCase
//{
//    use Specify;
//
//    public function testFetchQuestions()
//    {
//        $manager = new QuestionManager();
//        $interface = 'Michaels\Midas\Questions\QuestionInterface';
//
//        $this->specify("it returns a valid *class-based* command on fetch()", function() use ($manager, $interface) {
//            $manager->add('classTest', 'Michaels\Midas\Test\Stubs\ClassBasedQuestion');
//            $command = $manager->fetch('classTest');
//
//            $this->assertInstanceOf($interface, $command, "Invalid because does not implement QuestionInterface");
//        });
//
//        $this->specify("it returns a valid *object-based* command on fetch()", function() use ($manager, $interface) {
//            $manager->add('objectTest', new \Michaels\Midas\Test\Stubs\ClassBasedQuestion);
//            $command = $manager->fetch('objectTest');
//
//            $this->assertInstanceOf($interface, $command, "Invalid because does not implement QuestionInterface");
//        });
//
//        $this->specify("returns a valid *closure-based* command on fetch()", function() use ($manager, $interface) {
//            $manager->add('closureTest', function($data, array $params) {
//                return true;
//            });
//
//            $command = $manager->fetch('closureTest');
//
//            $this->assertInstanceOf($interface, $command, "Invalid because does not implement QuestionInterface");
//        });
//    }
//
//    public function testFetchQuestionsExceptions()
//    {
//        $manager = new QuestionManager();
//
//        $this->specify("it throws an exception when command is not registered", function() use ($manager) {
//            $manager->fetch('commandThatDoesNotExist');
//        }, ['throws' => 'Michaels\Midas\Exceptions\QuestionNotFoundException']);
//
//        $this->specify("it throws Exception when class is not found", function () use ($manager) {
//            $manager->add('classTest', 'A\Wrong\Class');
//            $manager->fetch('classTest');
//        }, ['throws' => 'Michaels\Midas\Exceptions\QuestionNotFoundException']);
//
//        $this->specify("throws Exception when object doesnt implement QuestionInterface", function () use ($manager) {
//            $manager->add('invalidClassTest', 'Michaels\Midas\Test\Stubs\InvalidClassBasedQuestion');
//            $manager->fetch('invalidClassTest');
//        }, ['throws' => 'Michaels\Midas\Exceptions\InvalidQuestionException']);
//    }
//}
