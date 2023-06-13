<?php namespace App\Http\Traits;

use Illuminate\Support\Str;

trait EscapeTextTrait
{
    public static function escapeText($text)
    {
        $findWord = [
            '<?php',
            '?>',
            'alert(',
            '<scrip',
            '<meta',
            't>',
            '</scrip',
            '</meta',
            '<SCRIP',
            '<META',
            'T>',
            '</SCRIP',
            '</META',
            '&lt;script&gt;',
            '&lt;/script&gt;',
            '&lt;meta&gt;',
            '&lt;/meta&gt;',
            '?&gt;',
            'script&gt;',
            'meta&gt;',
            '?php',
            '&lt;SCRIPT&gt;',
            '&lt;/SCRIPT&gt;',
            '&lt;META&gt;',
            '&lt;/META&gt;',
            'qavalue', '1=1', '1 = 1', '1=', 'script',
            '*/', '/*', 'OR 1', 'or 1', "OR '1", "or '1", '--', '&lt;a', '&lt;/a&gt;', '&lt;A',
            '&lt;/A&gt;', 'xss', 'XSS', '<svg', '&lt;svg', '&#97&#108&#101&#114&#116', 'onload=', 'onload',
            '<iframe', '&lt;iframe', 'prompt(', 'prompt',
            '<marquee', '<MARQUEE', '&lt;marquee', '</marquee', '</MARQUEE', '&lt;/marquee',
            'data:text/html;base64', 'data:', ':text/', ':text', '/html', 'base64',
            'onerror=', 'onmouseover', 'document.domain', 'java:', 'onerror', 'onfocus', 'onblur', 'oncontextmenu', 'oncut', 'ondblclick', 'ondrag',
            'contenteditable', 'onkeypress', 'onmousedown', 'onmousemove', 'brutelogic', 'setInterval', 'appendChild', 'HOST:PORT', 'createElement',
            'formaction=', 'xlink:', 'ontouchstart', 'onpageshow', 'onscroll', 'onresize', 'onhelp', 'onstart', 'top[', 'eval(', 'onkeydown', 'onmouseleave',
            'onmouseup', 'ondragleave', 'onbeforeactivate', 'autofocus', 'onbeforecopy', 'ondragend', 'draggable=', 'rticle', ':[', 'getElementById', 'dataformatas',
            'EVENT=', 'match=', 'xmlns', 'x-schema', '?xml', 'x:', 'j$', 'handler=', 'ev:', 'foreignObject', '/svg', 'foo=', 'feImage', 'mhtml:', 'x:x', 'clip-path:',
            '.svg', 'innerHTML', 'text/plain', 'dataTransfer', 'event.', 'view-source:', 'makePopups', 'window.open', 'text/xml', 'xml-stylesheet', 'xsl:',
            'ATTLIST', 'DOCTYPE', 'version=', '/handler', 'feed:', 'tab/traffic', 'attributeName=', 'begin=', 'oncopy=', 'onclick=', '<brute', '&lt;brute',
            'ontouchend=', 'onorientationchange=', 'location.', 'slice(', 'onhashchange=', 'onfinish=', 'onsubmit=', 'onbeforedeactivate=', 'onmouseenter=',
            'ondeactivate=', 'onmouseout=', 'onbeforepaste=', 'onkeyup=', '/acronym', 'mocha:', '@keyframes', 'onbeforecut=', 'HTTP-EQUIV='
        ];

        return str_ireplace($findWord, "", trim($text));
    }
}
