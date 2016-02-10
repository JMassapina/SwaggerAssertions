<?php

namespace FR3D\SwaggerAssertionsTest\PhpUnit;

use FR3D\SwaggerAssertions\PhpUnit\ResponseHeadersConstraint;
use FR3D\SwaggerAssertions\SchemaManager;
use PHPUnit_Framework_ExpectationFailedException as ExpectationFailedException;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestFailure as TestFailure;

/**
 * @covers FR3D\SwaggerAssertions\PhpUnit\ResponseHeadersConstraint
 */
class ResponseHeadersConstraintTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_Constraint
     */
    protected $constraint;

    protected function setUp()
    {
        $schema = '{"ETag":{"minimum":1}}';
        $schema = json_decode($schema);

        $this->constraint = new ResponseHeadersConstraint($schema);
    }

    public function testConstraintDefinition()
    {
        self::assertEquals(1, count($this->constraint));
        self::assertEquals('is valid', $this->constraint->toString());
    }

    public function testValidHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'ETag' => '123',
        ];

        self::assertTrue($this->constraint->evaluate($headers, '', true), $this->constraint->evaluate($headers));
    }

    public function testCaseInsensitiveValidHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'etag' => '123',
        ];

        self::assertTrue($this->constraint->evaluate($headers, '', true), $this->constraint->evaluate($headers));
    }

    public function testInvalidHeaderType()
    {
        $headers = [
            'Content-Type' => 'application/json',
            // 'ETag' => '123', // Removed intentional
        ];

        self::assertFalse($this->constraint->evaluate($headers, '', true));

        try {
            $this->constraint->evaluate($headers);
            self::fail('Expected ExpectationFailedException to be thrown');
        } catch (ExpectationFailedException $e) {
            self::assertEquals(
                <<<EOF
Failed asserting that {"Content-Type":"application\/json"} is valid.
[etag] The property etag is required

EOF
                ,
                TestFailure::exceptionToString($e)
            );
        }
    }
}
