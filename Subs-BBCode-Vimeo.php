<?php
/**********************************************************************************
* Subs-BBCode-Vimeo.php
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

function BBCode_Vimeo(&$bbc)
{
	// Format: [vimeo width=x height=x frameborder=x]{Vimeo ID}[/vimeo]
	$bbc[] = array(
		'tag' => 'vimeo',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('match' => '(\d+)'),
			'frameborder' => array('optional' => true, 'match' => '(\d+)'),
		),
		'validate' => 'BBCode_Vimeo_Validate',
		'content' => '{width}|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [vimeo width=x height=x frameborder=x]{Vimeo ID}[/vimeo]
	$bbc[] = array(
		'tag' => 'vimeo',
		'type' => 'unparsed_content',
		'parameters' => array(
			'frameborder' => array('match' => '(\d+)'),
		),
		'validate' => 'BBCode_Vimeo_Validate',
		'content' => '0|0|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [vimeo]{Vimeo ID}[/vimeo]
	$bbc[] = array(
		'tag' => 'vimeo',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_Vimeo_Validate',
		'content' => '0|0|0',
		'disabled_content' => '$1',
	);
}

function BBCode_Vimeo_Button(&$buttons)
{
	$buttons[count($buttons) - 1][] = array(
		'image' => 'vimeo',
		'code' => 'vimeo',
		'description' => 'vimeo',
		'before' => '[vimeo]',
		'after' => '[/vimeo]',
	);
}

function BBCode_Vimeo_Validate(&$tag, &$data, &$disabled)
{
	global $txt, $modSettings;
	
	if (empty($data))
		return ($tag['content'] = $txt['vimeo_no_post_id']);
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
	$pattern = '#(http|https)://(|(.+?).)vimeo.com/(\d+)#i';
	if (!preg_match($pattern, $data, $parts))
		return ($tag['content'] = $txt['vimeo_no_post_id']);
	$data = $parts[4];

	list($width, $frameborder) = explode('|', $tag['content']);
	if (empty($width))
		$width = !empty($modSettings['vimeo_default_width']) ? $modSettings['vimeo_default_width'] : false;
	$tag['content'] = '<div style="max-width: ' . (empty($width) ? '100%;' : $width . 'px;') . '"><div class="vimeo-wrapper">' .
		'<iframe src="https://player.vimeo.com/video/' . $data .'?title=0&byline=0&portrait=0&badge=0" scrolling="no" frameborder="' . $frameborder . '"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div></div>';
}

function BBCode_Vimeo_LoadTheme()
{
	global $context, $settings;
	$context['html_headers'] .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/BBCode-Vimeo.css" />';
	$context['allowed_html_tags'][] = '<iframe>';
}

function BBCode_Vimeo_Settings(&$config_vars)
{
	$config_vars[] = array('int', 'vimeo_default_width');
}

function BBCode_Vimeo_Embed(&$message, &$smileys, &$cache_id, &$parse_tags)
{
	if ($message === false)
		return;
	$replace = (strpos($cache_id, 'sig') !== false ? '[url]$0[/url]' : '[vimeo]$0[/vimeo]');
	$pattern = '~(?<=[\s>\.(;\'"]|^)(http|https)://(|(.+?).)vimeo.com/((recording|song)/([\w\d\-\_\%])+/(\d+)_(\d+))\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	if (strpos($cache_id, 'sig') !== false)
		$message = preg_replace('#\[vimeo.*\](.*)\[\/Vimeo\]#i', '[url]$1[/url]', $message);
}

?>