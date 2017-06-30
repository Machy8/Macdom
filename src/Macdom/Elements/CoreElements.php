<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace Macdom\Elements;

use Macdom\Compiler;
use Macdom\Engine;


final class CoreElements extends AbstractElementsManager
{

	public static function install(Compiler $compiler)
	{
		$elementsManager = new static($compiler);

		$elementsManager
			// Standard HTML elements
			->addElement('a', ['quickAttributes' => ['href', 'target', 'role']])
			->addElement('abbr', ['quickAttributes' => ['title']])
			->addElement('address')
			->addElement('area', [Engine::UNPAIRED_ELEMENT])
			->addElement('article')
			->addElement('aside')
			->addElement('audio', ['quickAttributes' => ['src']])
			->addElement('b')
			->addElement('base', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['href']])
			->addElement('bdi')
			->addElement('bdo', ['quickAttributes' => ['dir']])
			->addElement('blockquote')
			->addElement('body')
			->addElement('br', [Engine::UNPAIRED_ELEMENT])
			->addElement('button', ['quickAttributes' => ['type', 'value']])
			->addElement('canvas', ['quickAttributes' => ['width', 'height']])
			->addElement('caption')
			->addElement('cite')
			->addElement('code', [Engine::CONTENT_SKIPPED])
			->addElement('col', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['span']])
			->addElement('colgroup')
			->addElement('data', ['quickAttributes' => ['value']])
			->addElement('datalist')
			->addElement('dd')
			->addElement('del')
			->addElement('details')
			->addElement('dfn')
			->addElement('div')
			->addElement('dl')
			->addElement('dt')
			->addElement('element')
			->addElement('em')
			->addElement('embed', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['src', 'type', 'width', 'height']])
			->addElement('fieldset')
			->addElement('figcaption')
			->addElement('figure')
			->addElement('footer')
			->addElement('form', ['quickAttributes' => ['action', 'method', 'target']])
			->addElement('h1')
			->addElement('h2')
			->addElement('h3')
			->addElement('h4')
			->addElement('h5')
			->addElement('h6')
			->addElement('head')
			->addElement('header')
			->addElement('hr', [Engine::UNPAIRED_ELEMENT])
			->addElement('html', ['quickAttributes' => ['lang']])
			->addElement('i')
			->addElement('iframe', ['quickAttributes' => ['src', 'frameborder', 'width', 'height']])
			->addElement('img', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['src', 'alt']])
			->addElement('input', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['type', 'value', 'placeholder']])
			->addElement('ins')
			->addElement('kbd')
			->addElement('label', ['quickAttributes' => ['for']])
			->addElement('legend')
			->addElement('li')
			->addElement('link', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['href', 'rel', 'type']])
			->addElement('main')
			->addElement('map')
			->addElement('mark')
			->addElement('meta', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['name', 'content']])
			->addElement('meter')
			->addElement('nav')
			->addElement('noframes')
			->addElement('noscript')
			->addElement('object', ['quickAttributes' => ['data', 'type']])
			->addElement('ol', ['quickAttributes' => ['type', 'start', 'reversed']])
			->addElement('optgroup', ['quickAttributes' => ['label']])
			->addElement('option', ['quickAttributes' => ['value']])
			->addElement('output')
			->addElement('p')
			->addElement('param', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['name', 'value']])
			->addElement('pre')
			->addElement('progress', ['quickAttributes' => ['value', 'max']])
			->addElement('q', ['quickAttributes' => ['cite']])
			->addElement('rp')
			->addElement('rt')
			->addElement('rtc')
			->addElement('ruby')
			->addElement('s')
			->addElement('samp')
			->addElement('script', ['quickAttributes' => ['src', 'type'], Engine::CONTENT_SKIPPED])
			->addElement('section')
			->addElement('select')
			->addElement('shadow')
			->addElement('small')
			->addElement('source', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['src', 'type']])
			->addElement('span')
			->addElement('strong')
			->addElement('style', ['quickAttributes' => ['type'], Engine::CONTENT_SKIPPED])
			->addElement('sub')
			->addElement('summary')
			->addElement('sup')
			->addElement('table')
			->addElement('tbody')
			->addElement('td', ['quickAttributes' => ['rowspan', 'colspan']])
			->addElement('template')
			->addElement('textarea', ['quickAttributes' => ['placeholder'], Engine::CONTENT_SKIPPED])
			->addElement('tfoot')
			->addElement('th')
			->addElement('thead')
			->addElement('time')
			->addElement('title')
			->addElement('tr')
			->addElement('track', [Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['src', 'srclang', 'kind']])
			->addElement('u')
			->addElement('ul')
			->addElement('var')
			->addElement('video')
			->addElement('wbr', [Engine::UNPAIRED_ELEMENT])

			// Experimental and not standardized API elements
			->addElement('bgsound')
			->addElement('dialog')
			->addElement('hgroup')
			->addElement('image')
			->addElement('menu', ['quickAttributes' => ['type', 'label']])
			->addElement('menuitem', ['quickAttributes' => ['type']])
			->addElement('nobr')
			->addElement('picture')

			// XML elements
			->addElement('xml', [Engine::CONTENT_XML, 'quickAttributes' => ['version', 'encoding'], 'openTags' => ['<?'], 'closeTags' => ['?>']])
			->addElement('category', [Engine::CONTENT_XML])
			->addElement('channel', [Engine::CONTENT_XML])
			->addElement('cloud', [Engine::CONTENT_XML, Engine::UNPAIRED_ELEMENT, 'quickAttributes' => ['domain', 'port', 'path', 'registerProcedure', 'protocol']])
			->addElement('copyright', [Engine::CONTENT_XML])
			->addElement('day', [Engine::CONTENT_XML])
			->addElement('description', [Engine::CONTENT_XML])
			->addElement('docs', [Engine::CONTENT_XML])
			->addElement('enclosure', [Engine::CONTENT_XML, 'quickAttributes' => ['url']])
			->addElement('generator', [Engine::CONTENT_XML])
			->addElement('guid', [Engine::CONTENT_XML])
			->addElement('hour', [Engine::CONTENT_XML])
			->addElement('image', [Engine::CONTENT_XML])
			->addElement('item', [Engine::CONTENT_XML])
			->addElement('language', [Engine::CONTENT_XML])
			->addElement('lastBuildDate', [Engine::CONTENT_XML])
			->addElement('link', [Engine::CONTENT_XML])
			->addElement('managingEditor', [Engine::CONTENT_XML])
			->addElement('pubDate', [Engine::CONTENT_XML])
			->addElement('rating', [Engine::CONTENT_XML])
			->addElement('rss', [Engine::CONTENT_XML, 'quickAttributes' => ['version']])
			->addElement('skipDays', [Engine::CONTENT_XML])
			->addElement('skipHours', [Engine::CONTENT_XML])
			->addElement('source', [Engine::CONTENT_XML, 'url'])
			->addElement('textInput', [Engine::CONTENT_XML])
			->addElement('title', [Engine::CONTENT_XML])
			->addElement('ttl', [Engine::CONTENT_XML])
			->addElement('url', [Engine::CONTENT_XML])
			->addElement('webMaster', [Engine::CONTENT_XML]);
	}

}
