<?php

namespace XF\Install;

use XF\Container;
use XF\Http\Response;
use XF\Mvc\Renderer\AbstractRenderer;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\RouteMatch;
use XF\Util\File;

class App extends \XF\App
{
	public function initializeExtra()
	{
		$container = $this->container;

		$container['app.classType'] = 'Install';
		$container['job.manual.allow'] = true;

		$container['extension'] = function(Container $c)
		{
			return new \XF\Extension();
		};

		$container['jsVersion'] = function (Container $c)
		{
			return substr(md5(\XF::$versionId), 0, 8);
		};

		$container['router'] = function (Container $c)
		{
			return $c['router.install'];
		};

		$container['session'] = function (Container $c)
		{
			return $c['session.install'];
		};

		$container['templater'] = function (Container $c)
		{
			return $this->setupTemplaterObject($c, '\XF\Install\Templater');
		};

		$container->extend('error', function(\XF\Error $error, Container $c)
		{
			if ($error->hasPendingUpgrade())
			{
				// if an upgrade is pending, we need to be able to get a useful error message
				$error->setForceShowTrace(true);
			}

			return $error;
		});
	}

	/**
	 * This overrides the standard fromRegistry but silences the DB exception when data is not found,
	 * or any other DB error occurs
	 *
	 * @param               $key
	 * @param \Closure      $rebuildFunction
	 * @param \Closure|null $decoratorFunction
	 *
	 * @return \Closure
	 */
	public function fromRegistry($key, \Closure $rebuildFunction, \Closure $decoratorFunction = null)
	{
		return function(Container $c) use ($key, $rebuildFunction, $decoratorFunction)
		{
			try
			{
				$data = $this->container['registry'][$key];

				if ($data === null)
				{
					$data = $rebuildFunction($c, $key);
				}

				return $decoratorFunction ? $decoratorFunction($data, $c, $key) : $data;
			}
			catch (\XF\Db\Exception $e)
			{
				return [];
			}
		};
	}

	protected function getInstallerPhrases()
	{
		return [
			'all_directories_under_x_must_be_writable' => 'All directories under {directory} must be writable. Please change the permissions on these directories to be world writable (chmod 0777).',
			'check_completed_on_x_files' => 'Check completed on {total_checked} files. We found some problems with the following files.',
			'config_file_x_could_not_be_found' => 'The configuration file {file} could not be found.',
			'directory_x_must_be_writable' => 'The directory {directory} must be writable. Please change the permissions on this directory to be world writable (chmod 0777). If the directory does not exist, please create it.',
			'do_not_have_permission' => 'You do not have permission to view this page or perform this action.',
			'email_address_you_entered_appears_have_typo' => 'The email address you entered appears to have a typo. Please try another.',
			'execution_has_been_allowed_to_continue' => 'Execution has been allowed to continue.',
			'file_health_check_okay' => 'All {numFiles} checked files are present and correct. :)',
			'following_add_ons_missing_necessary_files_for_health_checking' => 'The following add-ons are missing the necessary files for health checking.',
			'following_error_occurred_while_connecting_database' => '
				<div>The following error occurred while connecting to the database:</div>
				<div class="blockMessage blockMessage--error">
					{error}
				</div>
				<div>This indicates that your configuration information is not correct. Please check the values you have entered. If you are unsure what values are correct or how to proceed, please contact your host for help. These values are specific to your server.</div>
			',
			'following_errors_were_found_when_verifying_requirements' => 'The following errors were found when verifying requirements',
			'following_warnings_were_found_when_verifying_requirements' => 'The following warnings were found when verifying requirements',
			'gd_jpeg_support_missing' => 'The required PHP extension GD was found, but JPEG support is missing. Please ask your host to add support for JPEG images.',
			'incorrect_password' => 'Incorrect password. Please try again.',
			'javascript_is_disabled_please_enable_before_proceeding' => 'JavaScript is disabled. For a better experience, please enable JavaScript in your browser before proceeding.',
			'mysql_version_x_does_not_meet_requirements' => 'MySQL 5.5 or newer is required. {version} does not meet this requirement. Please ask your host to upgrade MySQL.',
			'oops_we_ran_into_some_problems' => 'Oops! We ran into some problems.',
			'oops_we_ran_into_some_problems_more_details_console' => 'Oops! We ran into some problems. Please try again later. More error details may be in the browser console.',
			'passwords_did_not_match' => 'Passwords did not match. Please enter the same password in both fields.',
			'pcre_unicode_property_support_missing' => 'The required PHP extension PCRE was found with Unicode support, but Unicode character property support is missing.',
			'pcre_unicode_support_missing' => 'The required PHP extension PCRE was found, but Unicode support is missing. Please ask your host to add support for Unicode to PCRE.',
			'php_functions_disabled_impossible_check' => 'Your server has disabled functions that make it impossible to detect server information. Other errors may occur.',
			'php_functions_disabled_fundamental' => 'Your server has disabled fundamental core PHP functions via the disable_functions directive in php.ini. This will cause unexpected problems in XenForo. All PHP functions should be enabled.',
			'php_functions_disabled_warning' => 'Your server has disabled core PHP functions via the disable_functions directive in php.ini. Depending on the functions that have been disabled, this may cause unexpected problems in XenForo. All PHP functions should be enabled.',
			'php_function_x_disabled_fundamental' => 'Your server has disabled a fundamental core PHP function <code><b>{function}</b></code> via the <code>disable_functions</code> directive in php.ini. This will cause unexpected problems in XenForo.',
			'php_function_x_disabled_warning' => 'Your server has disabled a core PHP function <code><b>{function}</b></code> via the <code>disable_functions</code> directive in php.ini. This may cause unexpected problems in XenForo.',
			'php_no_ssl_support' => 'Your PHP does not have support for SSL connections. This may interfere with integrations into external services, such as Facebook.',
			'php_version_x_does_not_meet_requirements' => 'PHP 5.4.0 or newer is required. {version} does not meet this requirement. Please ask your host to upgrade PHP.',
			'php_version_x_outdated_upgrade' => 'Your server is running an outdated and unsupported version of PHP ({version}). If possible, you should upgrade to PHP 5.6 or newer (we recommend PHP 7.2) to benefit from improved security and performance.',
			'php_weak_random_values' => 'Your PHP configuration does not allow generation of secure random values. This may impact security. To resolve this, you may need to upgrade PHP or enable certain PHP extensions.',
			'please_enter_valid_email' => 'Please enter a valid email.',
			'please_enter_valid_password' => 'Please enter a valid password.',
			'rebuilding' => 'Rebuilding',
			'requested_page_not_found' => 'The requested page could not be found.',
			'requested_user_not_found' => 'The requested user could not be found.',
			'requested_user_x_not_found' => 'The requested user \'{name}\' could not be found.',
			'required_php_extension_x_not_found' => 'The required PHP extension {extension} could not be found. Please ask your host to install this extension.',
			'required_php_xml_extensions_not_found' => 'The required PHP extensions for XML handling (DOM and SimpleXML) could not be found. Please ask your host to install these extensions.',
			'security_error_occurred' => 'Security error occurred. Please press back, refresh the page, and try again.',
			'server_error_occurred' => 'A server error occurred. Please try again later.',
			'uh_oh_upgrade_did_not_complete' => 'Uh oh! The upgrade did not complete successfully. <a href="index.php">Please try again.</a>',
			'upgrade_found_newer_than_version' => 'An upgrade was found for a version of XenForo that is newer than the uploaded files. Please reupload all of the files for the new version and reload this page.',
			'you_are_using_out_of_date_browser_upgrade' => 'You are using an out of date browser. It  may not display this or other websites correctly.<br />You should upgrade or use an <a href="https://www.google.com/chrome/browser/" target="_blank">alternative browser</a>.',
			'you_cannot_proceed_unless_tables_removed' => 'You cannot proceed unless all XenForo database tables are removed.',
			'you_cannot_proceed_unless_tables_removed_cli' => 'You cannot proceed unless all XenForo database tables are removed. You must specify the --clear option to delete the tables first.',
			'you_do_not_have_permission_upgrade' => 'You do not have permission to upgrade XenForo. If you are getting this unexpectedly, you should make yourself a super admin via the Administrators page of the admin control panel.',
			'you_have_completed_installation_to_reinstall' => 'You have already completed installation. If you wish to reinstall, please delete the file internal_data/install-lock.php.',
			'you_sure_you_want_to_continue_cli' => 'Are you sure you want to continue? (y/n) ',
			'your_account_does_not_have_admin_privileges' => 'Your account does not have admin privileges.'
		];
	}

	protected function getInstallerLanguage()
	{
		return new Language(0, [], $this->db(), null, $this->getInstallerPhrases());
	}

	public function setup(array $options = [])
	{
		\XF::setLanguage($this->getInstallerLanguage());

		$this->setupAddOnComposerAutoload();
	}

	public function start($allowShortCircuit = false)
	{
		parent::start($allowShortCircuit);

		// try to prevent timeouts and errors when installing/upgrading
		@ini_set('display_errors', true);

		\XF::setMemoryLimit(-1);
		@set_time_limit(0);
	}

	public function setupUpgradeSession()
	{
		$user = $this->getVisitorFromSession($this->session(), ['Admin']);
		\XF::setVisitor($user);
	}

	public function complete(Response $response)
	{
		parent::complete($response);

		if ($this->container->isCached('session'))
		{
			$session = $this->session();

			if ($session->isStarted() && $session->hasData())
			{
				$session->save();
				$session->applyToResponse($response);
			}
		}
	}

	public function preRender(AbstractReply $reply, $responseType) {}

	public function setupTemplaterObject(Container $c, $class)
	{
		/** @var \XF\Template\Templater $templater */
		$templater = new $class(
			$this,
			\XF::language(),
			File::canonicalizePath(\XF::getSourceDirectory() . '/XF/Install/_templates')
		);

		$templater->addDefaultHandlers();
		$templater->addFilters($c['templater.config.filters']);
		$templater->addFunctions($c['templater.config.functions']);
		$templater->addTests($c['templater.config.tests']);

		$templater->setJquerySource($c['jQueryVersion'], 'local');
		$templater->setJsVersion($c['jsVersion']);

		return $templater;
	}

	protected function renderPageHtml($content, array $params, AbstractReply $reply, AbstractRenderer $renderer)
	{
		$templateName = isset($params['templateName']) ? $params['templateName'] : 'PAGE_CONTAINER';
		if (!$templateName)
		{
			return $content;
		}

		$templater = $this->templater();

		if (!strpos($templateName, ':'))
		{
			$templateName = 'install:' . $templateName;
		}

		$pageSection = $reply->getSectionContext();
		if (isset($params['section']))
		{
			$pageSection = $params['section'];
			$reply->setSectionContext($pageSection);
		}
		$params['pageSection'] = $pageSection;

		$params['controller'] = $reply->getControllerClass();
		$params['action'] = $reply->getAction();
		$params['actionMethod'] = 'action' . str_replace(' ', '', ucwords(str_replace('-', ' ', $reply->getAction())));

		$params['classType'] = $this->container('app.classType');
		$params['containerKey'] = $reply->getContainerKey();
		$params['contentKey'] = $reply->getContentKey();

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			$params['view'] = $reply->getViewClass();
			$params['templateName'] = $reply->getTemplateName();
		}

		$params['content'] = $content;

		return $templater->renderTemplate($templateName, $params);
	}
}