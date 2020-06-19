<?php

namespace XF\Data;

use XF\Util\Arr;

class CodeLanguage
{
	public function getSupportedLanguages($filterDisabled = false)
	{
		$languages = [
			'apacheconf' => [],
			'bash' => [
				'modes' => 'shell'
			],
			'c' => [
				'modes' => 'clike',
				'mime' => 'text/x-csrc'
			],
			'clike' => [
				'modes' => 'clike',
				'mime' => 'text/x-csrc'
			],
			'coffeescript' => [
				'modes' => 'coffeescript'
			],
			'cpp' => [
				'modes' => 'clike',
				'mime' => 'text/x-c++src'
			],
			'csharp' => [
				'modes' => 'clike',
				'mime' => 'text/x-csharp'
			],
			'css' => [
				'modes' => 'css',
				'common' => true,
			],
			'diff' => [
				'modes' => 'diff'
			],
			'html' => [
				'addons' => [
					'edit/closetag',
					'fold/xml-fold'
				],
				'config' => [
					'autoCloseTags' => true
				],
				'modes' => [
					'htmlmixed',
					'css',
					'javascript',
					'xml'
				],
				'common' => true
			],
			'http' => [
				'modes' => 'http'
			],
			'ini' => [],
			'java' => [
				'modes' => 'clike',
				'mime' => 'text/x-java'
			],
			'javascript' => [
				'modes' => 'javascript',
				'common' => true
			],
			'json' => [
				'modes' => 'javascript',
				'config' => [
					'json' => true
				]
			],
			'less' => [
				'modes' => 'css',
				'mime' => 'text/x-less'
			],
			'makefile' => [],
			'markdown' => [
				'addons' => [
					'mode/overlay',
					'edit/closetag',
					'fold/markdown-fold',
					'fold/xml-fold'
				],
				'config' => [
					'autoCloseTags' => true
				],
				'modes' => [
					'gfm',
					'clike',
					'css',
					'htmlmixed',
					'javascript',
					'markdown',
					'php',
					'xml'
				]
			],
			'nginx' => [
				'modes' => 'nginx'
			],
			'objectivec' => [
				'modes' => 'clike',
				'mime' => 'text/x-objectivec'
			],
			'perl' => [
				'modes' => 'perl'
			],
			'php' => [
				'modes' => [
					'clike',
					'php'
				],
				'mime' => 'text/x-php',
				'common' => true
			],
			'python' => [
				'modes' => 'python'
			],
			'ruby' => [
				'modes' => 'ruby'
			],
			'sass' => [
				'modes' => 'sass'
			],
			'scss' => [
				'modes' => 'css',
				'mime' => 'text/x-scss'
			],
			'sql' => [
				'modes' => 'sql',
				'mime' => 'text/x-mysql'
			],
			'svg' => [],
			'swift' => [
				'modes' => 'swift'
			],
			'xml' => [
				'modes' => 'xml',
				'config' => [
					'htmlMode' => false
				]
			]
		];

		$enabledLanguages = Arr::stringToArray(\XF::options()->allowedCodeLanguages,'/\r?\n/');

		foreach ($enabledLanguages AS $language)
		{
			if (isset($languages[$language]))
			{
				// we have some sort of code editor definition for this language
				// so we can just skip doing anything here.
				continue;
			}
			else
			{
				// this adds the supported language into the code editor, but will not add
				// any custom definition for the code editor (essentially, no syntax highlighting)
				$languages[$language] = [];
			}
		}

		array_walk($languages, function(&$language, $key)
		{
			$language['phrase'] = \XF::phrase('code_language.' . $key);
			return $language;
		});

		\XF::app()->fire('code_languages', [&$languages]);

		if ($filterDisabled)
		{
			$languages = array_intersect_key($languages, array_flip($enabledLanguages));
		}

		return $languages;
	}
}