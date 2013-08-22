<?php

/**
 * This file is part of the ook-php project and is copyright
 *
 * (c) 2013 Sam Smith <samuel.david.smith@gmail.com>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

define('SYNTAX_ELEMENTS_PATTERN', '/Ook[\.\?\!]/');

$file = $argv[1];
if ( ! is_file($file)) {
    fputs(STDERR, "No such file -- {$file}\n");
    exit(1);
}
$contents = file_get_contents($file);
if ( ! $contents || ! preg_match_all(SYNTAX_ELEMENTS_PATTERN, $contents, $matches, PREG_OFFSET_CAPTURE)) {
    exit(0);
}
$matches = $matches[0];
if (count($matches) % 2 != 0) {
    fputs(STDERR, "{$file}: Syntax error: expected Ook., Ook?, or Ook!\n");
    exit(1);
}
$ookSyntaxElementsToOokMap = array(
    '.?' => 1,
    '?.' => 2,
    '..' => 3,
    '!!' => 4,
    '.!' => 5,
    '!.' => 6,
    '!?' => 7,
    '?!' => 8,
);
$ooks = array();
$ookJumpStack = array();
// A map of ook index to ook index to jump to.
$ookJumps = array();
for ($i = 0, $j = 0, $length = count($matches); $i < $length; $i += 2, ++$j) {
    $ook = $ookSyntaxElementsToOokMap[$matches[$i][0][3] . $matches[$i + 1][0][3]];
    $ooks[$j] = $ook;
    if (7 == $ook) {
        array_push($ookJumpStack, $j);
    }
    if (8 == $ook) {
        if (empty($ookJumpStack)) {
            fputs(STDERR, "{$file}: Syntax error: unexpected Ook? Ook!\n");
            exit(1);
        }
        $index = array_pop($ookJumpStack);
        $ookJumps[$index] = $j + 1;
        $ookJumps[$j] = $index;
    }
}
if ($ookJumpStack) {
    fputs(STDERR, "{$file}: Syntax error: unbalanced Ook! Ook?\n");
    exit(1);
}
$memory = array(0);
$memoryPointer = 0;
for ($i = 0, $length = count($ooks); $i < $length; ++$i) {
    $ook = $ooks[$i];
    switch ($ook) {
        case 1:
            ++$memoryPointer;
            if ( ! isset($memory[$memoryPointer])) {
                $memory[$memoryPointer] = 0;
            }
            break;
        case 2:
            --$memoryPointer;
            break;
        case 3:
            ++$memory[$memoryPointer];
            break;
        case 4:
            --$memory[$memoryPointer];
            break;
        case 5:
            $memory[$memoryPointer] = fgetc(STDIN);
            break;
        case 6:
            fputs(STDOUT, chr($memory[$memoryPointer]));
            break;
        case 7:
            if (0 == $memory[$memoryPointer]) {
                $i = $ookJumps[$i];
            }
            break;
        case 8:
            if (0 < $memory[$memoryPointer]) {
                $i = $ookJumps[$i];
            }
            break;
    }
}
