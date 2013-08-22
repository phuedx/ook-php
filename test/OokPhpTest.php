<?php

/**
 * This file is part of the ook-php project and is copyright
 *
 * (c) 2013 Sam Smith <samuel.david.smith@gmail.com>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

use Symfony\Component\Process\Process;

class OokPhpTest extends PHPUnit_Framework_TestCase
{
    public function test_it_should_fail_when_the_file_doesnt_exist()
    {
        $process = $this->runOokPhp('fixtures/file-doesnt-exist.ook');
        $this->assertFalse($process->isSuccessful());
    }

    public function test_it_should_message_the_user_when_the_file_doesnt_exist()
    {
        $process = $this->runOokPhp('fixtures/file-doesnt-exist.ook');
        $this->assertRegExp('/^No such file -- .*$/', $process->getErrorOutput());
    }

    public function test_it_should_succeed_when_the_file_is_empty()
    {
        $process = $this->runOokPhp('fixtures/file-is-empty.ook');
        $this->assertTrue($process->isSuccessful());
    }

    public function test_it_should_fail_when_the_file_contains_an_odd_number_of_ooks()
    {
        $process = $this->runOokPhp('fixtures/contains-an-odd-number-of-ooks.ook');
        $this->assertFalse($process->isSuccessful());
    }

    public function test_it_should_message_the_user_when_the_file_contains_an_odd_number_of_ooks()
    {
        $process = $this->runOokPhp('fixtures/contains-an-odd-number-of-ooks.ook');
        $this->assertRegExp('/^.*\: Syntax error\: expected Ook\., Ook\?, or Ook\!$/', $process->getErrorOutput());
    }

    public function test_it_should_succeed_when_the_file_doesnt_contain_any_ooks()
    {
        $process = $this->runOokPhp('fixtures/file-doesnt-contain-any-ooks.ook');
        $this->assertTrue($process->isSuccessful());
    }

    public function test_it_should_successfully_execute_hello_world_ook()
    {
        $process = $this->runOokPhp('fixtures/hello-world.ook');
        $this->assertTrue($process->isSuccessful());
        $this->assertEquals('Hello World!', $process->getOutput());
    }

    public function test_it_should_fail_when_the_file_contains_an_unbalanced_bang_question()
    {
        $process = $this->runOokPhp('fixtures/contains-an-unbalanced-bang-question.ook');
        $this->assertFalse($process->isSuccessful());
    }

    public function test_it_should_message_the_user_when_the_file_contains_an_unbalanced_bang_question()
    {
        $process = $this->runOokPhp('fixtures/contains-an-unbalanced-bang-question.ook');
        $this->assertRegExp('/^.*\: Syntax error\: unbalanced Ook\! Ook\?$/', $process->getErrorOutput());
    }

    public function test_it_should_fail_when_the_file_contains_a_question_bang_before_a_bang_question()
    {
        $process = $this->runOokPhp('fixtures/contains-a-question-bang-before-a-bang-question.ook');
        $this->assertFalse($process->isSuccessful());
    }

    public function test_it_should_message_the_user_when_the_file_contains_a_question_bang_before_a_bang_question()
    {
        $process = $this->runOokPhp('fixtures/contains-a-question-bang-before-a-bang-question.ook');
        $this->assertRegExp('/^.*\: Syntax error\: unexpected Ook\? Ook\!$/', $process->getErrorOutput());
    }

    private function runOokPhp($file)
    {
        $dir = __DIR__;
        $process = new Process("/usr/bin/env php {$dir}/../bin/ook.php {$dir}/{$file}");
        $process->run();
        return $process;
    }
}
