<?php

namespace XF\Cron;

class CleanUp
{
	/**
	 * Clean up tasks that should be done daily. This task cannot be relied on
	 * to run daily, consistently.
	 */
	public static function runDailyCleanUp()
	{
		$app = \XF::app();

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $app->repository('XF:Thread');
		$threadRepo->pruneThreadReadLogs();

		/** @var \XF\Repository\Forum $forumRepo */
		$forumRepo = $app->repository('XF:Forum');
		$forumRepo->pruneForumReadLogs();

		/** @var \XF\Repository\Template $templateRepo */
		$templateRepo = $app->repository('XF:Template');
		$templateRepo->pruneEditHistory();

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $app->repository('XF:Ip');
		$ipRepo->pruneIps();

		/** @var \XF\Repository\Draft $draftRepo */
		$draftRepo = $app->repository('XF:Draft');
		$draftRepo->pruneDrafts();

		/** @var \XF\Repository\Search $searchRepo */
		$searchRepo = $app->repository('XF:Search');
		$searchRepo->pruneSearches();

		/** @var \XF\Repository\FindNew $findNewRepo */
		$findNewRepo = $app->repository('XF:FindNew');
		$findNewRepo->pruneFindNewResults();

		/** @var \XF\Repository\ModeratorLog $modLogRepo */
		$modLogRepo = $app->repository('XF:ModeratorLog');
		$modLogRepo->pruneModeratorLogs();

		/** @var \XF\Repository\AdminLog $adminLogRepo */
		$adminLogRepo = $app->repository('XF:AdminLog');
		$adminLogRepo->pruneAdminLogs();

		/** @var \XF\Repository\Tag $tagRepo */
		$tagRepo = $app->repository('XF:Tag');
		$tagRepo->pruneTagResultsCache();

		/** @var \XF\Repository\UserTfaTrusted $tfaTrustRepo */
		$tfaTrustRepo = $app->repository('XF:UserTfaTrusted');
		$tfaTrustRepo->pruneTrustedKeys();

		/** @var \XF\Repository\EditHistory $editHistoryRepo */
		$editHistoryRepo = $app->repository('XF:EditHistory');
		$editHistoryRepo->pruneEditHistory();

		/** @var \XF\Repository\FileCheck $fileCheckRepo */
		$fileCheckRepo = $app->repository('XF:FileCheck');
		$fileCheckRepo->pruneFileChecks();

		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $app->repository('XF:AddOn');
		$addOnRepo->cleanUpAddOnBatches();

		/** @var \XF\Repository\UpgradeCheck $upgradeCheckRepo */
		$upgradeCheckRepo = $app->repository('XF:UpgradeCheck');
		$upgradeCheckRepo->pruneUpgradeChecks();
	}

	/**
	 * Clean up tasks that should be done hourly. This task cannot be relied on
	 * to run every hour, consistently.
	 */
	public static function runHourlyCleanUp()
	{
		$app = \XF::app();

		/** @var \XF\Session\StorageInterface $publicSessionStorage */
		$publicSessionStorage = $app->container('session.public.storage');
		$publicSessionStorage->deleteExpiredSessions();

		/** @var \XF\Session\StorageInterface $adminSessionStorage */
		$adminSessionStorage = $app->container('session.admin.storage');
		$adminSessionStorage->deleteExpiredSessions();

		/** @var \XF\Repository\SessionActivity $activityRepo */
		$activityRepo = $app->repository('XF:SessionActivity');
		$activityRepo->updateUserLastActivityFromSession();
		$activityRepo->pruneExpiredActivityRecords();

		/** @var \XF\Repository\UserRemember $rememberRepo */
		$rememberRepo = $app->repository('XF:UserRemember');
		$rememberRepo->pruneExpiredRememberRecords();

		/** @var \XF\Repository\CaptchaQuestion $captchaQuestion */
		$captchaQuestion = $app->repository('XF:CaptchaQuestion');
		$captchaQuestion->cleanUpCaptchaLog();

		/** @var \XF\Repository\LoginAttempt $loginRepo */
		$loginRepo = $app->repository('XF:LoginAttempt');
		$loginRepo->cleanUpLoginAttempts();

		/** @var \XF\Repository\TfaAttempt $tfaAttemptRepo */
		$tfaAttemptRepo = $app->repository('XF:TfaAttempt');
		$tfaAttemptRepo->cleanUpTfaAttempts();

		/** @var \XF\Repository\UserConfirmation $userConfirmationRepo */
		$userConfirmationRepo = $app->repository('XF:UserConfirmation');
		$userConfirmationRepo->cleanUpUserConfirmationRecords();

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $app->repository('XF:Attachment');
		$attachmentRepo->deleteUnassociatedAttachments();
		$attachmentRepo->deleteUnusedAttachmentData();

		/** @var \XF\Repository\Api $apiRepo */
		$apiRepo = $app->repository('XF:Api');
		$apiRepo->pruneAttachmentKeys();

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $app->repository('XF:UserAlert');
		$alertRepo->pruneReadAlerts();
		$alertRepo->pruneUnreadAlerts();

		/** @var \XF\Repository\ThreadRedirect $redirectRepo */
		$redirectRepo = $app->repository('XF:ThreadRedirect');
		$redirectRepo->pruneThreadRedirects();

		/** @var \XF\Service\FloodCheck $floodChecker */
		$floodChecker = $app->service('XF:FloodCheck');
		$floodChecker->pruneFloodCheckData();

		/** @var \XF\Repository\Spam $spamRepo */
		$spamRepo = $app->repository('XF:Spam');
		$spamRepo->cleanUpRegistrationResultCache();
		$spamRepo->cleanupContentSpamCheck();
		$spamRepo->cleanupSpamTriggerLog();

		/** @var \XF\Repository\ImageProxy $imageProxyRepo */
		$imageProxyRepo = $app->repository('XF:ImageProxy');
		$imageProxyRepo->pruneImageCache();
		$imageProxyRepo->pruneImageProxyLogs();
		$imageProxyRepo->pruneImageReferrerLogs();

		/** @var \XF\Repository\Oembed $oembedRepo */
		$oembedRepo = $app->repository('XF:Oembed');
		$oembedRepo->pruneOembedCache();
		$oembedRepo->pruneOembedLogs();
		$oembedRepo->pruneOembedReferrerLogs();

		/** @var \XF\Repository\LinkProxy $linkProxyRepo */
		$linkProxyRepo = $app->repository('XF:LinkProxy');
		$linkProxyRepo->pruneLinkProxyLogs();
		$linkProxyRepo->pruneLinkReferrerLogs();

		/** @var \XF\Repository\ThreadReplyBan $threadReplyBanRepo */
		$threadReplyBanRepo = $app->repository('XF:ThreadReplyBan');
		$threadReplyBanRepo->cleanUpExpiredBans();

		/** @var \XF\Repository\NewsFeed $newsFeedRepo */
		$newsFeedRepo = $app->repository('XF:NewsFeed');
		$newsFeedRepo->cleanUpNewsFeedItems();

		/** @var \XF\Repository\ChangeLog $changeLogRepo */
		$changeLogRepo = $app->repository('XF:ChangeLog');
		$changeLogRepo->pruneChangeLogs();

		\XF\Util\File::cleanUpPersistentTempFiles();
	}

	/**
	 * Downgrades expired user upgrades.
	 */
	public static function runUserDowngrade()
	{
		$userUpgradeRepo = \XF::repository('XF:UserUpgrade');
		$userUpgradeRepo->downgradeExpiredUpgrades();
	}

	/**
	 * Expire temporary user changes.
	 */
	public static function expireTempUserChanges()
	{
		$userChangeRepo = \XF::repository('XF:UserChangeTemp');
		$userChangeRepo->removeExpiredChanges();
	}
}