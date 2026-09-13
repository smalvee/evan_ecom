<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_preserves_legitimate_formatting(): void
    {
        $html = '<h2 style="text-align:center;font-family:Kalpurush;font-size:24px;color:#ff0000;">শিরোনাম</h2>'
            . '<ul><li>Item</li></ul>'
            . '<table><tbody><tr><td>A</td></tr></tbody></table>'
            . '<blockquote>Quote</blockquote>'
            . '<a href="https://example.com" target="_blank">link</a>'
            . '<img src="https://example.com/a.png" alt="x" style="max-width:100%">';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringContainsString('font-family:Kalpurush', $clean);
        $this->assertStringContainsString('font-size:24px', $clean);
        $this->assertStringContainsString('text-align:center', $clean);
        $this->assertStringContainsString('color:#ff0000', $clean);
        $this->assertStringContainsString('<h2', $clean);
        $this->assertStringContainsString('<ul>', $clean);
        $this->assertStringContainsString('<table>', $clean);
        $this->assertStringContainsString('<blockquote>', $clean);
        $this->assertStringContainsString('শিরোনাম', $clean);
        $this->assertStringContainsString('target="_blank"', $clean);
    }

    public function test_it_removes_dangerous_content(): void
    {
        $html = '<script>alert(1)</script>'
            . '<style>body{display:none}</style>'
            . '<p onclick="x()">t</p>'
            . '<a href="javascript:alert(1)">x</a>'
            . '<img src="data:text/html;base64,xx" onerror="x()">'
            . '<div style="background:url(javascript:alert(1));width:10px;color:red">y</div>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('<style', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('data:text/html', $clean);
        // Legitimate CSS survives.
        $this->assertStringContainsString('width:10px', $clean);
        $this->assertStringContainsString('color:red', $clean);
    }
}
