<?php

namespace Tests\Unit\Macroable;

use Illuminate\Validation\Rule;
use Tests\TestCase;

/**
 * Contains macro method of Illuminate\Validation\Rule
 *
 */
class RuleTest extends TestCase
{
    public function test_parse_method()
    {
        $this->assertEquals(['root' => ['required']], Rule::parse('root', ['required']));

        $this->assertEquals(
            ['root' => ['required', 'nullable']],
            Rule::parse('root', ['required', 'nullable'])
        );

        $this->assertEquals(
            [
                'root' => ['required', 'nullable'],
                'root.value' => ['required']
            ],
            Rule::parse('root', [
                'required',
                'nullable',
                'value' => ['required'],
            ])
        );

        # Use special attributes rootRules
        $this->assertEquals(
            [
                'root' => ['required', 'nullable'],
                'root.value' => ['required']
            ],
            Rule::parse('root', [
                'rootRules' => ['required', 'nullable'],
                'value' => ['required'],
            ])
        );

        # Use special attributes rootRules and integer key appeared
        $this->assertEquals(
            [
                'root' => ['required', 'nullable'],
                'root.value' => ['array'],
                'root.value.*' => ['string'],
                'root.3' => ['integer'],
            ],
            Rule::parse('root', [
                'rootRules' => ['required', 'nullable'],
                3 => ['integer'],
                'value' => [
                    'array',
                    '*' => ['string'],
                ],
            ])
        );
    }
}
