<?php

namespace randomId;


class RandomIdTest extends \PHPUnit_Framework_TestCase
{
    public $blobDir;

    public function setUp()
    {
        $this->blobDir = __DIR__ . '/blobdir/';
        if (!is_dir($this->blobDir)) {
            mkdir($this->blobDir);
        }
        foreach (glob($this->blobDir . '*') as $item) {
            unlink($item);
        }
    }

    public function test8BitFileNotShuffled()
    {
        $rind = new RandomId();
        $blob = $this->blobDir . '8bit_straight';
        $rind->createBlob(1, $blob, false);
        $this->assertEquals(pow(2, 8), filesize($blob));

        for ($i = 0; $i < 256; $i++) {
            $this->assertEquals($i, $rind->readRandomId($i, $blob, 1));
        }
        $this->expectException(\ErrorException::class);
        $rind->readRandomId(256, $blob, 1);
    }

    public function test16BitFileNotShuffled()
    {
        $rind = new RandomId();
        $blob = $this->blobDir . '16bit_straight';
        $rind->createBlob(2, $blob, false);
        $this->assertEquals(pow(2, 16) * 2, filesize($blob));

        for ($i = 0; $i < 0xffff; $i += 16) { // just pick some random values
            $this->assertEquals($i, $rind->readRandomId($i, $blob, 2));
        }
        $this->expectException(\ErrorException::class);
        $rind->readRandomId(0x10000, $blob, 2);
    }

    public function testRead4ByteFrom1ByteFile()
    {
        $rind = new RandomId();
        $blob = $this->blobDir . '8bit_straight';
        $rind->createBlob(1, $blob, false);
        $this->assertEquals(pow(2, 8), filesize($blob));

        for ($i = 0; $i < 64; $i++) {
            $expected = $i*4;
            $expected <<= 8;
            $expected += $i*4 + 1;
            $expected <<= 8;
            $expected += $i*4 + 2;
            $expected <<= 8;
            $expected += $i*4 + 3;
            $this->assertEquals($expected, $rind->readRandomId($i, $blob, 4));
        }
        $this->expectException(\ErrorException::class);
        $rind->readRandomId(64, $blob, 4);
    }
}
