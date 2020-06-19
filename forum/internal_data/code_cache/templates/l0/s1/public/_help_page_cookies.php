<?php
// FROM HASH: 7add260a5e2c52b2a5cadd1504a21326
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<p>This page discusses how cookies are used by this site. If you continue to use this site, you are consenting to our use of cookies.</p>

<h3 class="textHeading">What are cookies?</h3>
<p>Cookies are small text files stored on your computer by your web browser at the request of a site you\'re viewing. This allows the site you\'re viewing to remember things about you, such as your preferences and history or to keep you logged in.</p>

<p>Cookies may be stored on your computer for a short time (such as only while your browser is open) or for an extended period of time, even years. Cookies not set by this site will not be accessible to us.</p>

<h3 class="textHeading">Our cookie usage</h3>
<p>This site uses cookies for numerous things, including:</p>
<ul>
	<li>Registration and maintaining your preferences. This includes ensuring that you can stay logged in and keeping the site in the language or appearance that you requested.</li>
	<li>Analytics. This allows us to determine how people are using the site and improve it.</li>
	<li>Advertising cookies (possibly third-party). If this site displays advertising, cookies may be set by the advertisers to determine who has viewed an ad or similar things. These cookies may be set by third parties, in which case this site has no ability to read or write these cookies.</li>
	<li>Other third-party cookies for things like Facebook or Twitter sharing. These cookies will generally be set by the third-party independently, so this site will have no ability to access them.</li>
</ul>

<h3 class="textHeading">Standard cookies we set</h3>
<p>
	These are the main cookies we set during normal operation of the software.
</p>
<ul>
	<li>
		<strong>' . $__templater->escape($__vars['xf']['cookie']['prefix']) . 'csrf</strong>
		<ul>
			<li>Stores a token, unique to you, which is used to verify that the actions you perform on this site were intended to be performed by you.</li>
		</ul>
	</li>
	<li>
		<strong>' . $__templater->escape($__vars['xf']['cookie']['prefix']) . 'session</strong>
		<ul>
			<li>Stores the current ID of your session.</li>
		</ul>
	</li>
	<li>
		<strong>' . $__templater->escape($__vars['xf']['cookie']['prefix']) . 'user</strong>
		<ul>
			<li>Stores a key, unique to you, which allows us to keep you logged in to the software as you navigate from page to page.</li>
		</ul>
	</li>
</ul>

<h3 class="textHeading">Additional cookies and those set by third parties</h3>
<p>
	Additional cookies may be set during the use of the site to remember information as certain actions are being performed, or remembering certain preferences.
</p>
<p>
	Other cookies may be set by third party service providers which may provide information such as tracking anonymously which users are visiting the site, or set by content embedded into some pages, such as YouTube or other media service providers.
</p>

<h3 class="textHeading">Removing/disabling cookies</h3>
<p>Managing your cookies and cookie preferences must be done from within your browser\'s options/preferences. Here is a list of guides on how to do this for popular browser software:</p>
<ul>
	<li><a href="https://support.microsoft.com/en-gb/help/17442/windows-internet-explorer-delete-manage-cookies" target="_blank">Microsoft Internet Explorer</a></li>
	<li><a href="https://privacy.microsoft.com/en-us/windows-10-microsoft-edge-and-privacy" target="_blank">Microsoft Edge</a></li>
	<li><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank">Mozilla Firefox</a></li>
	<li><a href="https://support.google.com/chrome/answer/95647?hl=en" target="_blank">Google Chrome</a></li>
	<li><a href="https://support.apple.com/en-gb/guide/safari/manage-cookies-and-website-data-sfri11471/mac" target="_blank">Safari for macOS</a></li>
	<li><a href="https://support.apple.com/en-gb/HT201265" target="_blank">Safari for iOS</a></li>
</ul>

<h3 class="textHeading">More information about cookies</h3>
<p>
	To learn more about cookies, and find more information about blocking certain types of cookies, please visit the <a href="https://ico.org.uk/for-the-public/online/cookies/" target="_blank">ICO website Cookies page</a>.
</p>';
	return $__finalCompiled;
});